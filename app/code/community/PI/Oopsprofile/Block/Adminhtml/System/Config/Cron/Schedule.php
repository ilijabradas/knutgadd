<?php

class PI_Oopsprofile_Block_Adminhtml_System_Config_Cron_Schedule extends Mage_Adminhtml_Block_Widget_Form{

	protected function _prepareForm(){

		$profile = Mage::registry('current_convert_profile');
		$oopsprofile = Mage::registry('current_convert_oopsprofile');

		$freqSource = Mage::getModel('adminhtml/system_config_source_cron_frequency');
		$freqSourceOptions = $freqSource->toOptionArray();
		array_unshift($freqSourceOptions, array(
				'label' => Mage::helper('oopsprofile')->__('Please Select'),
				'value' => ''
			)
		);

		$cronExprKeyPath = 'crontab/jobs/oopsprofile_cron_' . $profile->getId() . '_schedule/cron_expr';
		$cronModelKeyPath =  'crontab/jobs/oopsprofile_cron_' . $profile->getId() . '_run/model';

		try {
       $cronExprValue =  Mage::getModel('core/config_data')->load($cronExprKeyPath, 'path');
       $cronModelValue = Mage::getModel('core/config_data')->load($cronModelKeyPath, 'path');
            
		}
    catch (Exception $e) {
        Mage::throwException(Mage::helper('adminhtml')->__('Cron expression no more exist in system for this profile'));
    }

		$form = new Varien_Data_Form();

		$fieldset = $form->addFieldset('schedule_run', array('legend'=>Mage::helper('catalog')->__(' Cron Schedule')));

		if(Mage::helper('oopsprofile/cron')->isHeartbeatCheckDisabled() == 0){	

			/***Check if master cron is configured**/
			$heartbeatStatus = Mage::helper('oopsprofile/cron')->checkHeartbeat();
			if(is_array($heartbeatStatus) && !empty($heartbeatStatus)){

					if($heartbeatStatus['status'] == 0){
						$cronStatusElement = $fieldset->addField('cron_heartbeat_status', 'textarea', array(
										'label'=> 'Master Cron Status',
								    'readonly' => true,
								    'rows'=> 2,
								    'cols'=> 3,	
								    'title'=> Mage::helper('oopsprofile')->__('Master Cron Status'),
								    'class'=> 'beating'
						));
					}else{
						$cronStatusElement = $fieldset->addField('cron_heartbeat_status', 'textarea', array(
										'label'=> 'Master Cron Status',
								    'title'=> Mage::helper('oopsprofile')->__('Master Cron Status'),
								    'class'=> 'dead',
								    'readonly' => true,
						));	
					}

					$oopsprofile->setData('cron_heartbeat_status', $heartbeatStatus['msg']);	
			}
			/****Ends****/
		}
		

		$cronEnabledElement = $fieldset->addField('cron_enabled', 'select', array(
            'label'=> Mage::helper('oopsprofile')->__('Enable Cron'),
            'title'=> Mage::helper('oopsprofile')->__('Enable Cron'),
            'name'=>'cron_enabled',
						'values'    => array(
                0 => Mage::helper('oopsprofile')->__('No'),
                1 => Mage::helper('oopsprofile')->__('Yes')
            ),
            'onchange'  => 'hideShowCronSchedule(this);', 
    ));

    

		$cronFrequencyElement = $fieldset->addField('cron_frequency', 'select', array(
            'label' => Mage::helper('oopsprofile')->__('Cron Frequency'),
            'title' => Mage::helper('oopsprofile')->__('Cron Frequency'),
            'name'  =>'cron_frequency',
            'class' =>'cron_frequency',
						'values'=> $freqSourceOptions
    ));

    
		$timeElement = $fieldset->addField('cron_start_time', 'time', array(
            'label'=> Mage::helper('oopsprofile')->__('Cron Start Time'),
            'title'=> Mage::helper('oopsprofile')->__('Cron Start Time'),
            'name'=>'cron_start_time',
            'class'=>'cron_start_time' 
    ));

		$expertModeElement = $fieldset->addField('cron_expert_mode', 'checkbox', array(
            'label'=> Mage::helper('oopsprofile')->__('Schedule in Expert Mode'),
            'title'=> Mage::helper('oopsprofile')->__('Manually Enter Cron Expression'),
            'name'=>'cron_expert_mode',
            'class'=>'cron_expert_mode',
            'onchange'=> 'hideShowCronExprField(this);',
            'checked'=> $oopsprofile->getData('cron_expert_mode') ? true: false,
            'note'=> Mage::helper('oopsprofile')->__('Manually Enter Cron Expression.'), 
    ));

    $oopsprofile->setData('cron_expert_mode', 1);

		$cronExprElement = $fieldset->addField('cron_expression', 'text', array(
            'label'=> Mage::helper('oopsprofile')->__('Cron Expression'),
            'title'=> Mage::helper('oopsprofile')->__('Cron Expression'),
            'name'=>'cron_expression',
            'class'=>'cron_expression',
            'note'=> Mage::helper('oopsprofile')->__('Ex. * 2 * * *(every night at 2pm) <strong>|</strong>  Seperate characters by space. <strong>|</strong> Frequency must be enough to get the task completed.'), 
    ));

		if($oopsprofile->getData('cron_enabled')){
			if($oopsprofile->getData('cron_expert_mode') != 1){
				$cronFrequencyElement->setRequired(true);
				$timeElement->setRequired(true);
			}else{
				$cronExprElement->setRequired(true);
			}
		}

		$cronExprElement->setAfterElementHtml(
			'<script type="text/javascript">'
		  . '//<![CDATA[
				function hideShowCronExprField(element){
					
					if (element.checked) {
						$("cron_expression").up("tr").show();
						$("cron_frequency").disabled = true;
						$("cron_frequency").removeClassName("required-entry");
						$("cron_expression").addClassName("required-entry");
						$$(".cron_start_time").each(function(em){
							em.disabled = true;
						});
					} else {
						$("cron_expression").up("tr").hide();
							$("cron_frequency").disabled = false;
							if($("cron_enabled").value == 1){
								console.log("required entry added");
								$("cron_frequency").addClassName("required-entry");
							}
							$("cron_expression").removeClassName("required-entry");
							$$(".cron_start_time").each(function(em){
							em.disabled = false;
						});
					}
					return true;
				}'

			. 'document.observe("dom:loaded", hideShowCronExprField($("cron_expert_mode")));'
			. '//]]> '
		  . '</script>'
		);

		$timeElement->setAfterElementHtml(
		    '<script type="text/javascript">'
		    . '//<![CDATA[
					function hideShowCronSchedule(element){
						console.log(element.value);
						if (element.value == 1) {
							$("cron_frequency").up("tr").show();
							$("cron_frequency").addClassName("required-entry");
							$$(".cron_start_time").each(function(em){
								em.up("tr").show();
								//em.addClassName("required-entry");
							});
						
						} else {
							$("cron_frequency").removeClassName("required-entry");
						  $("cron_frequency").up("tr").hide();
							$$(".cron_start_time").each(function(em){
								em.up("tr").hide();
								//em.removeClassName("required-entry");
							});
					
						}
						return true;
					}'

				. 'document.observe("dom:loaded", hideShowCronSchedule($("cron_enabled")));'
				. '//]]> '
		    . '</script>'
		);

		$form->setValues($oopsprofile->getData());
    $this->setForm($form);

    return parent::_prepareForm();
	}
}
