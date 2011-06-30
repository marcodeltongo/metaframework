<?php

/**
 * UserController
 *
 * @todo:
 * "Remember me" option.
 * Using phpass library for password hashing (instead of unsafe md5).
 * Optional login attempt count for bruteforce preventing, determined by IP and by username.
 * Optional logging last login IP-address and time.
 * Optional CAPTCHA for registration and repetitive login attempts.
 * Unactivated accounts and forgotten password requests auto-expire.
 * Facebook, Twitter, Live integration.
 */
class UserController extends Controller
{

    public function accessRules()
    {
        return array(
                array('allow',
                        'actions' => array('register', 'login', 'activate', 'forgot', 'reset'),
                        'users' => array('?'),
                ),
                array('allow',
                        'users' => array('@'),
                ),
                array('deny',
                        'users' => array('?'),
                ),
        );
    }

    /**
     * User home page
     */
    public function actionIndex()
    {
        $this->render('index');
    }

    /**
     * Register user on the site.
     *
     * If registration is successful, a new user account is created.
     * If email_activation flag in config-file is set to true,
     * then this account have to be activated by clicking special link sent by email;
     * otherwise it is activated already.
     *
     * Please notice: after registration user remains unauthenticated; login is still required.
     */
    public function actionRegister()
    {
        $this->render('register');
    }

    /**
     * Activate user account.
     *
     * Normally this method is invoked by clicking a link in activation email.
     * Clicking a link in "forgot password" email activates account as well.
     * User is verified by authentication code in the URL.
     */
    public function actionActivate()
    {
        $this->render('activate');
    }

    /**
     * Login user on the site.
     *
     * If login is successful and user account is activated, s/he is redirected to returnUrl or home.
     * If account is not activated, then activate is invoked.
     * In case of login failure user remains on the same page.
     */
    public function actionLogin()
    {
        $model = new LoginForm;

        if (isset($_POST['ajax']) and ($_POST['ajax'] === 'login-form')) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];


            if ($model->validate() && $model->login()) {
                $this->redirect(Yii::app()->user->returnUrl);
            }
        }

        $this->render('login', array('model' => $model));
    }

    /**
     * Logout user from the site.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    /**
     * Forgot password.
     *
     * Generate special reset code (to change password) and send it to user.
     * Obviously this method may be used when user has forgotten their password.
     */
    public function actionForgot()
    {
        $this->render('forgot');
    }

    /**
     * Reset password.
     *
     * Replace user password (forgotten) with a new one (set by user).
     * The method can be called by clicking on link in mail.
     * User is verified by authentication code in the URL.
     */
    public function actionReset()
    {
        $this->render('reset');
    }

    /**
     * Manage user site preferences.
     */
    public function actionPreferences()
    {
        $this->render('preferences');
    }

    /**
     * Manage user profile.
     */
    public function actionProfile()
    {
        $this->render('profile');
    }

    /**
     * Delete user account.
     *
     * Can be called only when user is logged in and activated.
     * For higher security user's password is required.
     */
    public function actionDelete()
    {
        $this->render('delete');
    }

}