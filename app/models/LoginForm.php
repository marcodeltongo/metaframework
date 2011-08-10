<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
    public $username;
    public $password;
    public $rememberMe;
    private $_identity;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
                array('username, password', 'required'),
                array('rememberMe', 'boolean'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
                'username' => Yii::t('login', 'Username'),
                'password' => Yii::t('login', 'Password'),
                'rememberMe' => Yii::t('login', 'Remember me'),
        );
    }

    /**
     * Authenticates the password.
     *
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate($attribute, $params)
    {
        $this->_identity = new UserIdentity($this->username, $this->password);

        if (!$this->_identity->authenticate()) {
            $this->addError('password', 'Incorrect username or password.');
        }
    }

    /**
     * Logs in the user using the given username and password in the model.
     *
     * @return boolean whether login is successful
     */
    public function login()
    {
        if ($this->_identity === null) {
            $this->authenticate($this->username, $this->password);
        }

        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
            Yii::app()->user->login($this->_identity, $duration);

            return true;
        }

        return false;
    }

}
