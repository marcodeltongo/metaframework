<?php

include 'PHPMailer/class.phpmailer.php';

/**
 * Mailer is a component acting as an adapter for PHPMailer.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class Mailer extends CApplicationComponent
{

    /**
     * Initialize
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Send an email.
     *
     * @param string $subject
     * @param string $body
     * @param string $sender_mail
     * @param string $sender_name
     * @param string $addressee_mail
     * @param string $addressee_name
     *
     * @return mixed true on success, error info on failure
     */
    public function send($subject = '', $body = '', $sender_mail = '', $sender_name = '', $destination_mail = '', $destination_name = '')
    {
        /*
         * Get mail object
         */
        $mail = new phpmailer();

        /*
         * Prepare mail attributes
         */
        $mail->SetFrom($sender_mail, $sender_name);
        if (empty($destination_name)) {
            $mail->AddAddress($destination_mail);
        } else {
            $mail->AddAddress($destination_mail, $destination_name);
        }

        /*
         * Prepare mail content
         */
        $mail->Subject = $subject;
        $mail->Body = $body;

        /*
         * Try to send
         */
        if (!$mail->Send()) {
            return $mail->ErrorInfo;
        } else {
            return true;
        }
    }

    /**
     * Send an HTML email.
     *
     * @param string $subject
     * @param string $body
     * @param string $txt_body
     * @param string $sender_mail
     * @param string $sender_name
     * @param string $addressee_mail
     * @param string $addressee_name
     *
     * @return mixed true on success, error info on failure
     */
    public function sendHtml($subject = '', $body = '', $txt_body = '', $sender_mail = '', $sender_name = '', $destination_mail = '', $destination_name = '')
    {
        /*
         * Get mail object
         */
        $mail = new phpmailer();

        /*
         * Prepare mail attributes
         */
        $mail->SetFrom($sender_mail, $sender_name);
        if (empty($destination_name)) {
            $mail->AddAddress($destination_mail);
        } else {
            $mail->AddAddress($destination_mail, $destination_name);
        }

        /*
         * Prepare mail content
         */
        $mail->Subject = $subject;
        $mail->MsgHTML($body);
        if (!empty($txt_body)) {
            $mail->AltBody = $txt_body;
        }

        /*
         * Try to send
         */
        if (!$mail->Send()) {
            return $mail->ErrorInfo;
        } else {
            return true;
        }
    }

    /**
     * Send an email using a view as body.
     *
     * @param string $subject
     * @param string $mailPartial View file to use to build the body
     * @param string $sender_mail
     * @param string $sender_name
     * @param string $addressee_mail
     * @param string $addressee_name
     * @param array $extra Variables available to the view
     *
     * @return mixed TRUE on success, error info on failure
     */
    public function sendView($subject = '', $mailPartial = '', $sender_mail = '', $sender_name = '', $addressee_mail = '', $addressee_name = '', $extra = array())
    {
        /*
         * Prepare mail content
         */
        if (!is_array($extra)) {
            $extra = array($extra);
        }
        $body = $this->renderPartial($mailPartial, $extra, true);
        $txt_body = strip_tags(str_replace(array('<br>','<br/>','<br />'), array(PHP_EOL, PHP_EOL, PHP_EOL), $body));

        return $this->sendHtml($subject, $body, $txt_body, $sender_mail, $sender_name, $destination_mail, $destination_name);
    }

}