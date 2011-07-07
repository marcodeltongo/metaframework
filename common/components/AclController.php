<?php

/**
 * AclController is the customized base controller class for ACL managed operations.
 *
 * All controllers that needs ACL in application should extend from this base class.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
abstract class AclController extends Controller
{
    /**
     * Set to true in child classes to temporarly jump over ACL.
     *
     * @var boolean
     */
    protected $aclTurnedOff = false;

    /**
     * Setup filters.
     */
    public function filters()
    {
        return array(
                'accessControl',
                'ACL',
        );
    }

    /**
     * Empty access rules.
     */
    public function accessRules()
    {
        return array();
    }

    /**
     * The filter method for 'ACL' filter.
     *
     * This filter reports an error if the applied action cannot be called by user.
     *
     * @param CFilterChain $filterChain the filter chain that the filter is on.
     */
    public function filterACL($filterChain)
    {
        $user = Yii::app()->getUser();
        $actionId = $this->getAction()->getId();
        $hasAccess = false;

        /*
         * Turned off ?
         */
        if ($this->aclTurnedOff) {
            $filterChain->run();
            return;
        }

        /*
         * Check controller access rules.
         */
        $conRules = $this->accessRules();
        foreach ($conRules as $rule) {
            if ($rule[0] == 'allow') {
                if (!array_key_exists('actions', $conRules) or in_array($actionId, $rule['actions'])) {
                    if (in_array('*', $rule['users'])
                            or (!$user->getIsGuest() and
                            (in_array($user->getName(), $rule['users']) or in_array('@', $rule['users'])))
                            or ($user->getIsGuest() and
                            (in_array($user->getName(), $rule['users']) or in_array('?', $rule['users'])))) {
                        $hasAccess = true;
                        break;
                    }
                }
            }
        }
        if (!$hasAccess and !empty($conRules)) {
            throw new CHttpException(403, Yii::t('yii', 'You are not authorized to perform this action.'));
        }

        /*
         * ACL rules to check.
         */
        $action = $actionId . '.' . $this->getId();
        $controller = '*.' . $this->getId();
        $module = $this->getModule();
        if (null !== $module) {
            $module = '.' . $module->getId();
            $action .= $module;
            $controller .= $module;
        }

        /*
         * Check against ACL, start from '*' superuser item.
         */
        $authManager = Yii::app()->getAuthManager();
        if (!$user->checkAccess('*') and (null === $authManager->getAuthItem('*'))) {
            /*
             * Superuser permission rule doesn't exist, try controller.
             */
            if (!$user->checkAccess($controller) and (null === $authManager->getAuthItem($controller))) {
                /*
                 * Controller permission rule doesn't exist, try action.
                 */
                if (!$user->checkAccess($action) and (null === $authManager->getAuthItem($action))) {
                    throw new CHttpException(403, Yii::t('yii', "The action '$action' has no applicable authorization rules."));
                }
            } else {
                $hasAccess = true;
            }
        } else {
            $hasAccess = true;
        }

        /*
         * Result ?
         */
        if ($hasAccess) {
            $filterChain->run();
        } else {
            throw new CHttpException(403, Yii::t('yii', 'You are not authorized to perform this action.'));
        }
    }

}
