<?php
/**
 *
 * @author: Neocomplexx
 *
 */

class Neocomplexx_Neopopup_Model_System_Source_Mode
{
	public function toOptionArray()
	{
		return array(
			array(
				'value' => 'no_param',
				'label' => Mage::helper('neopopup')->__('No param needed')
			)
			,array(
				'value' => 'need_param',
				'label' => Mage::helper('neopopup')->__('Need param in URL')
			)
		);
	}
}