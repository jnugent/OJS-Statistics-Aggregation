<?php

/**
 * @file StatisticsAggregationSettingsForm.inc.php
 *
 * Copyright (c) 2003-2010 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class StatisticsAggregationSettingsForm
 * @ingroup plugins_generic_statisticsAggregation
 *
 * @brief Form for journal managers to modify Statistics Aggregation plugin settings
 */

// $Id$


import('lib.pkp.classes.form.Form');

class StatisticsAggregationSettingsForm extends Form {

	/** @var $journalId int */
	var $journalId;

	/** @var $plugin object */
	var $plugin;

	/**
	 * Constructor
	 * @param $plugin object
	 * @param $journalId int
	 */
	function StatisticsAggregationSettingsForm(&$plugin, $journalId) {
		$this->journalId =& $journalId;
		$this->plugin =& $plugin;

		parent::Form($plugin->getTemplatePath() . 'settingsForm.tpl');

		$this->addCheck(new FormValidator($this, 'statisticsAggregationSiteId', 'required', 'plugins.generic.statisticsAggregation.manager.settings.statisticsAggregationSiteIdRequired'));
		$this->addCheck(new FormValidatorEmail($this, 'statisticsAggregationSiteEmail', 'required', 'plugins.generic.statisticsAggregation.manager.settings.statisticsAggregationSiteEmailRequired'));
		$this->addCheck(new FormValidatorEmail($this, 'statisticsAggregationSiteEmailConfirm', 'required', 'plugins.generic.statisticsAggregation.manager.settings.statisticsAggregationSiteEmailConfirmRequired'));
		$this->addCheck(new FormValidatorInSet($this, 'statisticsAggregationSiteEmailConfirm', 'required', 'plugins.generic.statisticsAggregation.manager.settings.emailsMustMatch', array(Request::getUserVar('statisticsAggregationSiteEmail'))));
	}

	/**
	 * Initialize form data.
	 */
	function initData() {

		$plugin =& $this->plugin;

		$this->_data = array(
			'statisticsAggregationSiteId' => $plugin->getSetting($this->journalId, 'statisticsAggregationSiteId'),
			'statisticsAggregationSiteEmail' => $plugin->getSetting($this->journalId, 'statisticsAggregationSiteEmail'),
			'statisticsAggregationSiteEmailConfirm' => $plugin->getSetting($this->journalId, 'statisticsAggregationSiteEmailConfirm'),
			'disableForm' => ($plugin->getSetting($this->journalId, 'statisticsAggregationSiteId') != '') ? true : false
		);
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('statisticsAggregationSiteId', 'statisticsAggregationSiteEmail', 'statisticsAggregationSiteEmailConfirm'));
	}

	/**
	 * Save settings.
	 */
	function execute() {
		$plugin =& $this->plugin;

		$plugin->updateSetting($this->journalId, 'statisticsAggregationSiteId', trim($this->getData('statisticsAggregationSiteId'), "\"\';"), 'string');
		$plugin->updateSetting($this->journalId, 'statisticsAggregationSiteEmail', trim($this->getData('statisticsAggregationSiteEmail'), "\"\';"), 'string');
	}
}

?>
