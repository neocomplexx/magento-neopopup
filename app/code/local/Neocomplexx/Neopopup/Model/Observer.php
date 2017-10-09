<?php
/**
 *
 * @author: Neocomplexx
 *
 */

class Neocomplexx_Neopopup_Model_Observer {

	const NEED_PARAM_KEY		= 'need_param';
	const NO_PARAM_KEY			= 'no_param';
	const MODULE_HANDLER_NAME	= 'neopopup_show_modal';
	const COOKIE_NAME           = 'neopopup';
	const SHOW_INTERVAL         = 30; // days



	public function showModal($observer)
	{
		$helper = Mage::helper('neopopup');

		if(!$helper->isEnabled()) {
			return $observer;
		}

		// Validate if we need to show the Popup regarding configuration Mode.
		if($helper->getMode() == self::NEED_PARAM_KEY)
		{
			$paramName		= $helper->getParamName();
			$paramValue		= $helper->getParamValue();

			if(!$paramName || !$paramValue) {
				$helper->log('Param/ParamValue was not configured.');
				return $observer;
			}


			$value = Mage::app()->getRequest()->getParam($paramName);

			if(!$value) {
				$helper->log('Param does not exists or has different value. Cannot continue.');
				$helper->log('Requested Uri: ' . Mage::app()->getRequest()->getRequestUri());
				return $observer;
			}

			if($paramValue != $value) {
				$helper->log('Configured param value is different. Cannot continue.');
				$helper->log('Requested Uri: ' . Mage::app()->getRequest()->getRequestUri());
				return $observer;
			}

		} else {
			$helper->log('No param is required to show Popup.');
		}

		// Last validation oto check if we're changing pages.
		// If so, we shouldn't show the page. Just the first time the User access to the site.
		$_referrer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';

		if(!strstr($_referrer, Mage::getBaseUrl()) && $this->_showPopup())
		{
			$helper->log('REFERER is not the same. We show the PopUp');
			$update = $observer->getEvent()->getLayout()->getUpdate();
			$update->addHandle(self::MODULE_HANDLER_NAME);
		} else {
			$helper->log('The referer is the same as the website. Do not show the popup.');
			$helper->log('Referer: ' . $_referrer);
		}

		return $observer;
	}

	protected function _showPopup()
	{
		if ($this->_customerIsSubscribed()) { return false; }

		if (Mage::helper('neopopup')->getMode() != self::NEED_PARAM_KEY) {

			$timestamp = (int)Mage::getSingleton('core/cookie')->get(self::COOKIE_NAME);

			if (!$timestamp) { $timestamp = $this->_setCookie(); }

			if ($this->_isIntervalOver($timestamp)) { return false; }
		}

		return true;
	}

	protected function _isIntervalOver($timestamp)
	{
		$today = new DateTime();
		$start = new DateTime();
		$start->setTimestamp($timestamp);

		$interval = (int)$start->diff($today)->format('%a');
		return ($interval > self::SHOW_INTERVAL);
	}

	protected function _setCookie()
	{
		Mage::getSingleton('core/cookie')->delete(self::COOKIE_NAME);

		$path     = '/';
		$value    = time();
		$lifetime = 5 * 365 * 24 * 60 * 60;

		Mage::getSingleton('core/cookie')
		->set(self::COOKIE_NAME, $value, $lifetime, $path);

		return $value;
	}

	protected function _customerIsSubscribed()
	{
		$customer = Mage::getSingleton('customer/session');

		if ($customer->isLoggedIn()) {

			$customerData = Mage::getModel('customer/customer')
			->load($customer->getId());

			$subscriber = Mage::getModel('newsletter/subscriber')
			->loadByEmail($customerData->getEmail());

			return $subscriber->getId() ? true : false;
		}

		return false;
	}
}