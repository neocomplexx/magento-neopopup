<?php
/**
 *
 * @author: Neocomplexx
 *
 */
class Neocomplexx_Neopopup_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
	 * Check if Neocomplexx_Neopopup module is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return (bool)((int)$this->config('general/active') !== 0);
	}


	/**
	 * Logging facility
	 *
	 * @param mixed $data Message to save to file
	 * @param string $filename log filename, default is <yymmdd-Neocomplexx_Neopopup.log>
	 */
	public function log($data, $filename = 'Neocomplexx_neopopup.log')
	{
		if($this->isEnabled() && $this->config('general/debug')):
			Mage::log($data, null, Date('Ymd').'-'.$filename);
		endif;
	}


	/**
	 * Get module configuration value
	 *
	 * @param string $value
	 * @param string $store
	 * @return mixed Configuration setting
	 */
	public function config($value, $store = null)
	{
		$store = is_null($store) ? Mage::app()->getStore() : $store;

		$configscope = Mage::app()->getRequest()->getParam('store');
		if( $configscope && ($configscope !== 'undefined') ){
			$store = $configscope;
		}

		return Mage::getStoreConfig("neocomplexx_neopopup/$value", $store);
	}


	/**
	 * Set module configuration value
	 *
	 * @param string $path
	 * @param string $value Value to be recorded.
	 * @param string $store Store to save the value, default: default.
	 * @return mixed Configuration setting
	 */
	public function setConfig($path, $value, $store = 'default')
	{
		$model = new Mage_Core_Model_Config();

		if($model)
			$model->saveConfig('neocomplexx_neopopup/'.$path, $value, $store);

		unset($model);
	}


	public function getMode()
	{
		return $this->config('general/mode');
	}


	/**
	 * @return mixed
	 */
	public function getParamName()
	{
		return $this->config('param/name');
	}


	/**
	 * @return mixed
	 */
	public function getParamValue()
	{
		return $this->config('param/value');
	}


}