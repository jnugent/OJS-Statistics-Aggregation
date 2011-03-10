<?php

/**
 * @file StatisticsAggregationPlugin.inc.php
 *
 * Copyright (c) 2003-2010 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class StatisticsAggregationPlugin
 * @ingroup plugins_generic_statisticsAggregation
 *
 * @brief Statistics Aggregation for Synergies/SUSHI plugin class
 */

// $Id$


import('lib.pkp.classes.plugins.GenericPlugin');

class StatisticsAggregationPlugin extends GenericPlugin {
	/**
	 * Called as a plugin is registered to the registry
	 * @param $category String Name of category plugin was registered to
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path) {
		if (parent::register($category, $path)) {
			HookRegistry::register('TemplateManager::display', array(&$this, 'callbackInsertSA'));
			return true;
		} else {
			return false;
		}
	}

	function getDisplayName() {
		return Locale::translate('plugins.generic.statisticsAggregation.displayName');
	}

	function getDescription() {
		return Locale::translate('plugins.generic.statisticsAggregation.description');
	}

	/**
	 * Set the page's breadcrumbs, given the plugin's tree of items
	 * to append.
	 * @param $subclass boolean
	 */
	function setBreadcrumbs($isSubclass = false) {
		$templateMgr =& TemplateManager::getManager();
		$pageCrumbs = array(
			array(
				Request::url(null, 'user'),
				'navigation.user'
			),
			array(
				Request::url(null, 'manager'),
				'user.role.manager'
			)
		);
		if ($isSubclass) $pageCrumbs[] = array(
			Request::url(null, 'manager', 'plugins'),
			'manager.plugins'
		);

		$templateMgr->assign('pageHierarchy', $pageCrumbs);
	}

	/**
	 * Display verbs for the management interface.
	 */
	function getManagementVerbs() {
		$verbs = array();
		if ($this->getEnabled()) {
			$verbs[] = array('settings', Locale::translate('plugins.generic.statisticsAggregation.manager.settings'));
		}
		return parent::getManagementVerbs($verbs);
	}

	/**
	 * Insert Statistics Aggregation page tag to footer
	 */
	function callbackInsertSA($hookName, $params) {

		$templateMgr =& $params[0];
		$template =& $params[1];
		$journal =& Request::getJournal();
		$session =& Request::getSession();

		if (!empty($journal) && ! Request::isBot()) {

			switch ($template) {
				case 'article/article.tpl':
				case 'article/interstitial.tpl':
				case 'article/pdfInterstitial.tpl':
				// Log the request as an article view.
					$article = $templateMgr->get_template_vars('article');
					$galley = $templateMgr->get_template_vars('galley');

					// If no galley exists, this is an abstract view -- don't include it. (FIXME?)
					if (!$galley) {
						return false;
					}
					$currentTime = time(); // timestamp for this request

					$journalId = $journal->getId();
					$statisticsAggregationSiteId = $this->getSetting($journalId, 'statisticsAggregationSiteId');

					$statsArray = array();
					$statsArray['ip'] =& Request::getRemoteAddr();
					$statsArray['rp'] =& Request::getRequestedPage();
					$statsArray['ua'] = $_SERVER["HTTP_USER_AGENT"];
					$statsArray['ts'] = date('d/M/Y:H:i:s O', $currentTime);
					$statsArray['title'] = $article->getLocalizedTitle();
					$protocol =& Request::getProtocol();

					$statsArray['host'] = $protocol . '://' . Request::getServerHost();

					if (isset($_SERVER['HTTP_REFERER']) && $this->isRemoteReferer($statsArray['host'], $_SERVER['HTTP_REFERER'])) {
						$statsArray['ref'] = $_SERVER['HTTP_REFERER'];
					} else {
						$statsArray['ref'] = '';
					}

					$statsArray['uri'] =& Request::getRequestPath();

					$jsonString = json_encode($statsArray);
					$this->import('StatisticsSocket');
					$statisticsSocket = new StatisticsSocket();
					$statisticsSocket->send($statisticsAggregationSiteId, $jsonString);

				break;
				default:
					error_log($template . ' was called but not logged.');
				break;
			}
		}
		return false;
	}

	/**
	 * @brief determines if a referring document is coming from an off-site location.
	 * @param $docHost the base host of this request (e.g., http://your.journals.site).
	 * @param $referer the full referring document, if there was one.
	 * @return boolean true if the referring document has a different base domain.
	 */
	function isRemoteReferer($docHost, $referer) {
		if (!preg_match("{^" . quotemeta($docHost) . "}", $referer)) {
			return true;
		} else {
			return false;
		}
	}

 	/*
 	 * Execute a management verb on this plugin
 	 * @param $verb string
 	 * @param $args array
	 * @param $message string Location for the plugin to put a result msg
 	 * @return boolean
 	 */
	function manage($verb, $args, &$message) {
		if (!parent::manage($verb, $args, $message)) return false;

		switch ($verb) {

			case 'getNewHash':
				$emailAddress = Request::getUserVar('email');
				$journal =& Request::getJournal();
				$journalTitle = $journal->getLocalizedTitle();
				if ($emailAddress != '')  {
					$jsonResult = file_get_contents('http://warhammer.hil.unb.ca/index.php/getNewHash/0/' . urlencode($emailAddress) . '/' . urlencode($journalTitle));
					echo $jsonResult;
					return true;
				} else {
					return false;
				}
			case 'settings':
				$templateMgr =& TemplateManager::getManager();
				$templateMgr->register_function('plugin_url', array(&$this, 'smartyPluginUrl'));
				$journal =& Request::getJournal();

				$this->import('StatisticsAggregationSettingsForm');
				$form = new StatisticsAggregationSettingsForm($this, $journal->getId());
				if (Request::getUserVar('save')) {
					$form->readInputData();
					if ($form->validate()) {
						$form->execute();
						Request::redirect(null, 'manager', 'plugin');
						return false;
					} else {
						$this->setBreadCrumbs(true);
						$form->display();
					}
				} else {
					$this->setBreadCrumbs(true);
					$form->initData();
					$form->display();
				}
				return true;
			default:
				// Unknown management verb
				assert(false);
				return false;
		}
	}
}
?>
