<?php

/**
 * @file plugins/generic/googleAnalytics/GoogleAnalyticsPlugin.inc.php
 *
 * Copyright (c) 2014 Simon Fraser University Library
 * Copyright (c) 2003-2014 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class GoogleAnalyticsPlugin
 * @ingroup plugins_generic_googleAnalytics
 *
 * @brief Google Analytics plugin class
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class GoogleAnalyticsPlugin extends GenericPlugin {
	/**
	 * Called as a plugin is registered to the registry
	 * @param $category String Name of category plugin was registered to
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path) {
		$success = parent::register($category, $path);
		if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return true;
		if ($success && $this->getEnabled()) {
			// Insert field into author submission page and metadata form
			HookRegistry::register('Templates::Author::Submit::Authors', array($this, 'metadataField'));
			HookRegistry::register('Templates::Submission::MetadataEdit::Authors', array($this, 'metadataField'));

			// Hook for initData in two forms
			HookRegistry::register('metadataform::initdata', array($this, 'metadataInitData'));
			HookRegistry::register('authorsubmitstep3form::initdata', array($this, 'metadataInitData'));

			// Hook for execute in two forms
			HookRegistry::register('Author::Form::Submit::AuthorSubmitStep3Form::Execute', array($this, 'metadataExecute'));
			HookRegistry::register('Submission::Form::MetadataForm::Execute', array($this, 'metadataExecute'));

			// Add element for AuthorDAO for storage
			HookRegistry::register('authordao::getAdditionalFieldNames', array($this, 'authorSubmitGetFieldNames'));

			// Insert Google Analytics page tag to common footer
			HookRegistry::register('Templates::Common::Footer::PageFooter', array($this, 'insertFooter'));

			// Insert Google Analytics page tag to article footer
			HookRegistry::register('Templates::Article::Footer::PageFooter', array($this, 'insertFooter'));

			// Insert Google Analytics page tag to article interstitial footer
			HookRegistry::register('Templates::Article::Interstitial::PageFooter', array($this, 'insertFooter'));

			// Insert Google Analytics page tag to article pdf interstitial footer
			HookRegistry::register('Templates::Article::PdfInterstitial::PageFooter', array($this, 'insertFooter'));

			// Insert Google Analytics page tag to reading tools footer
			HookRegistry::register('Templates::Rt::Footer::PageFooter', array($this, 'insertFooter'));

			// Insert Google Analytics page tag to help footer
			HookRegistry::register('Templates::Help::Footer::PageFooter', array($this, 'insertFooter'));
		}
		return $success;
	}

	function getDisplayName() {
		return __('plugins.generic.googleAnalytics.displayName');
	}

	function getDescription() {
		return __('plugins.generic.googleAnalytics.description');
	}

	/**
	 * Extend the {url ...} smarty to support this plugin.
	 */
	function smartyPluginUrl($params, &$smarty) {
		$path = array($this->getCategory(), $this->getName());
		if (is_array($params['path'])) {
			$params['path'] = array_merge($path, $params['path']);
		} elseif (!empty($params['path'])) {
			$params['path'] = array_merge($path, array($params['path']));
		} else {
			$params['path'] = $path;
		}

		if (!empty($params['id'])) {
			$params['path'] = array_merge($params['path'], array($params['id']));
			unset($params['id']);
		}
		return $smarty->smartyUrl($params, $smarty);
	}

	/**
	 * Display verbs for the management interface.
	 */
	function getManagementVerbs() {
		$verbs = parent::getManagementVerbs();
		if ($this->getEnabled()) {
			$verbs[] = array('settings', __('plugins.generic.googleAnalytics.manager.settings'));
		}
		return $verbs;
	}

	/**
	 * Insert Google Scholar account info into author submission step 3
	 */
	function metadataField($hookName, $params) {
		$smarty =& $params[1];
		$output =& $params[2];

		$output .= $smarty->fetch($this->getTemplatePath() . 'authorSubmit.tpl');
		return false;
	}

	function authorSubmitGetFieldNames($hookName, $params) {
		$fields =& $params[1];
		$fields[] = 'gs';
		return false;
	}

	function metadataExecute($hookName, $params) {
		$author =& $params[0];
		$formAuthor =& $params[1];
		$author->setData('gs', $formAuthor['gs']);
		return false;
	}

	function metadataInitData($hookName, $params) {
		$form =& $params[0];
		$article =& $form->article;
		$formAuthors = $form->getData('authors');
		$articleAuthors =& $article->getAuthors();

		for ($i=0; $i<count($articleAuthors); $i++) {
			$formAuthors[$i]['gs'] = $articleAuthors[$i]->getData('gs');
		}

		$form->setData('authors', $formAuthors);
		return false;
	}

	/**
	 * Insert Google Analytics page tag to footer
	 */
	function insertFooter($hookName, $params) {
		$smarty = $params[1];
		$output =& $params[2];
		$request = $this->getRequest();
		$templateMgr = TemplateManager::getManager($request);
		$currentJournal = $templateMgr->getTemplateVars('currentJournal');

		if (!empty($currentJournal)) {
			$journal = $request->getJournal();
			$journalId = $journal->getId();
			$googleAnalyticsSiteId = $this->getSetting($journalId, 'googleAnalyticsSiteId');

			$article = $templateMgr->getTemplateVars('article');
			if ($request->getRequestedPage() == 'article' && $article) {
				$authorAccounts = array();
				foreach ($article->getAuthors() as $author) {
					$account = $author->getData('gs');
					if (!empty($account)) $authorAccounts[] = $account;
				}
				$templateMgr->assign('gsAuthorAccounts', $authorAccounts);
			}

			if (!empty($googleAnalyticsSiteId) || !empty($authorAccounts)) {
				$templateMgr->assign('googleAnalyticsSiteId', $googleAnalyticsSiteId);
				$trackingCode = $this->getSetting($journalId, 'trackingCode');
				if ($trackingCode == "ga") {
					$output .= $templateMgr->fetch($this->getTemplatePath() . 'pageTagGa.tpl');
				} else {
					$output .= $templateMgr->fetch($this->getTemplatePath() . 'pageTagUrchin.tpl');
				}
			}
		}
		return false;
	}

 	/**
	 * @see Plugin::manage()
	 */
	function manage($verb, $args, &$message, &$messageParams, &$pluginModalContent = null) {
		if (!parent::manage($verb, $args, $message, $messageParams)) return false;

		switch ($verb) {
			case 'settings':
				$request = $this->getRequest();
				$templateMgr = TemplateManager::getManager($request);
				$templateMgr->registerFunction('function', 'plugin_url', array($this, 'smartyPluginUrl'));
				$journal = $request->getJournal();

				$this->import('GoogleAnalyticsSettingsForm');
				$form = new GoogleAnalyticsSettingsForm($this, $journal->getId());
				if ($request->getUserVar('save')) {
					$form->readInputData();
					if ($form->validate()) {
						$form->execute();
						$request->redirect(null, 'manager', 'plugin');
						return false;
					} else {
						$form->display();
					}
				} else {
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
