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
     * Setup filters
     */
    public function filters()
    {
        return array(
                'accessControl',
                'ACL',
        );
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

        /*
         * Turned off ?
         */
        if ($this->aclTurnedOff) {
            $filterChain->run();
            return;
        }

        /*
         * ACL rules to check.
         */
        $action = $this->getAction()->getId() . '.' . $this->getId();
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
        $hasAccess = false;
        $authManager = Yii::app()->getAuthManager();
        if (!$user->checkAccess('*')) {
            if (null === $authManager->getAuthItem('*')) {
                /*
                 * Superuser permission rule doesn't exist, try controller.
                 */
                if (!$user->checkAccess($controller)) {
                    if (null === $authManager->getAuthItem($controller)) {
                        /*
                         * Controller permission rule doesn't exist, try action.
                         */
                        if (!$user->checkAccess($action)) {
                            if (null === $authManager->getAuthItem($action)) {
                                throw new CHttpException(403, Yii::t('yii', "The action '$action' has no applicable authorization rules."));
                            }
                        } else {
                            $hasAccess = true;
                        }
                    }
                } else {
                    $hasAccess = true;
                }
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