<?php

/**
 * Controller is the customized base controller class.
 *
 * All controller classes for this application should extend from this base class.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
abstract class Controller extends CController
{

	/**
	 * Default layout for the controller view.
	 *
	 * @var string
	 */
	public $layout = 'application.views.layouts.base';
	/**
	 * Base URL.
	 *
	 * @var string
	 */
	public $baseUrl = '';
	/**
	 * Requested URL.
	 *
	 * @var string
	 */
	public $currentUrl = '';
	/**
	 * Current language.
	 *
	 * @var string
	 */
	public $language = 'en';
	/**
	 * Meta keywords.
	 *
	 * @var string
	 */
	public $metaKeywords = '';
	/**
	 * Meta description.
	 *
	 * @var string
	 */
	public $metaDescription = '';
	/**
	 * Optional body CSS classes.
	 *
	 * @var array
	 */
	public $bodyClasses = array();
	/**
	 * Optional breadcrumbs array (defaults to false).
	 *
	 * @var array
	 */
	public $breadcrumbs = false;
	/**
	 * Optional menu flag or array (defaults to false).
	 *
	 * @var array
	 */
	public $menu = true;
	/**
	 * User shortcut.
	 *
	 * @var object
	 */
	public $user = null;

	/**
	 * Overload Yii method to add support to POST params.
	 *
	 * @return array
	 */
	public function getActionParams()
	{
		return array_merge(array_remove_empty($_GET), array_remove_empty($_POST));
	}

	/**
	 * Invoked right before an action is to be executed.
	 */
	protected function beforeAction($action)
	{
		/*
		 * Ensure parent returns true.
		 */
		if (!parent::beforeAction($action)) {
			return false;
		}
		/*
		 * Setup commodity shortcuts and constants.
		 */
		if (!defined('BASE_URL')) {
			$this->baseUrl = $this->createAbsoluteUrl('/') . '/';
			define('BASE_URL', $this->baseUrl);
		} else {
			$this->baseUrl = BASE_URL;
		}
		$this->currentUrl = Yii::app()->request->getHostInfo() . Yii::app()->request->getRequestUri();
		defined('CURRENT_URL') or define('CURRENT_URL', $this->currentUrl);

		/*
		 * Locale and language
		 */
		if (isset($_GET['_language'])) {
			$this->setLanguage($_GET['_language']);
		}

		/*
		 * Prepare currently logged user.
		 */
		if (!Yii::app()->user->getIsGuest()) {
			/**
			 * Set/get language preference.
			 */
			if (Yii::app()->user->getState('preferred_language')) {
				$this->language = Yii::app()->user->getState('preferred_language');
			} else {
				global $config;
				$this->language = (isset($config['language'])) ? $config['language'] : 'en';
			}
			Yii::app()->setLanguage($this->language);
		}
		$this->user = Yii::app()->user;

		/*
		 * Cache flusher.
		 */
		if (isset($_GET['_flushcache'])) {
			Yii::app()->cache->flush();
			Yii::app()->file->set(Yii::app()->getAssetManager()->getBasePath())->purge();
			$this->redirect(str_replace('_flushcache', '', CURRENT_URL));
		}

		/*
		 * CHtml settings.
		 */
		CHtml::$afterRequiredLabel = '';

		return true;
	}

	/**
	 * Changes user language preference.
	 *
	 * @param string $language
	 */
	protected function setLanguage($language)
	{
		$this->language = $language;
		Yii::app()->setLanguage($language);
		if (!Yii::app()->user->getIsGuest()) {
			Yii::app()->user->setState('preferred_language', $language);
		}
	}

	/**
	 * Get user language preference.
	 *
	 * @return string $language
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * Alias for Yii::app()->params array with little magic.
	 */
	public function param($name, $default = null)
	{
		return (isset(Yii::app()->params[$name]) and !empty(Yii::app()->params[$name])) ? Yii::app()->params[$name] : $default;
	}

	/**
	 * Alias for Yii::app()->user->setFlash($key, $value, null).
	 */
	public function setFlash($key, $value)
	{
		$values = Yii::app()->user->getFlash($key, array(), false);
		$values[] = $value;
		Yii::app()->user->setFlash($key, $values, null);
	}

	/**
	 * Alias for Yii::app()->user->setFlash('message', $value, null)
	 */
	public function setFlashMessage($message)
	{
		$this->setFlash('message', $message, null);
	}

	/**
	 * Alias for Yii::app()->user->setFlash('success', $value, null)
	 */
	public function setFlashSuccess($message)
	{
		$this->setFlash('success', $message, null);
	}

	/**
	 * Alias for Yii::app()->user->setFlash('error', $value, null)
	 */
	public function setFlashError($message)
	{
		$this->setFlash('error', $message, null);
	}

}