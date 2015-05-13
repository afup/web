<?php

require_once dirname(__FILE__) . '/AFUP_Configuration.php';
require_once dirname(__FILE__) . '/../../dependencies/mandrill/Mandrill.php';

/**
 * Send emails via Mandrill
 */
class AFUP_Mail
{

    protected $_apiKey;
    protected $_mandrill;
    
    /**
     * Init the object by getting the Maindrill API key
     */
    public function __construct()
    {
        // Get the API key
        $this->_apiKey = $this->_getConfig()->obtenir('mandrill|key');
    }

    /**
     * Retrieve the configuration
     * @throws Exception
     * @return AFUP_Configuration
     */
    protected function _getConfig()
    {
        if (!isset($GLOBALS['AFUP_CONF'])) {
            throw new Exception("Please include configuration.");
        }

        return $GLOBALS['AFUP_CONF'];
    }

    /**
     * Retrieve Mandrill
     * @return \Mandrill
     */
    public function getMandrill()
    {
        if (null === $this->_mandrill) {
            $this->_mandrill = new Mandrill($this->_apiKey);
        }
        return $this->_mandrill;
    }

    /**
     * Send an email
     * @see https://mandrillapp.com/api/docs/messages.php.html#method=send-template
     *
     * @param string $template Template identifier
     * @param array $receiver Receiver's data like ['email' => 'foo@bar.baz', 'name' => 'John Doe']
     * @param array $data Data to put in the email
     * @param array $parameters Some parameters (like bcc, etc.)
     * @param boolean $async See Mandrill::sendTemplate()
     * @param string $ipPool See Mandrill::sendTemplate()
     * @param string $sendAt See Mandrill::sendTemplate()
     * @return bool TRUE on success, FALSE on failure
     */
    public function send($template, array $receiver, array $data = array(), array $parameters = array(), $async = false, $ipPool = null, $sendAt = null)
    {
        // Receiver
        if (!isset($parameters['to'])) {
            $parameters['to'] = array($receiver);
        }

        // Blind copy
        if (!isset($parameters['bcc_address'])) {
            $parameters['bcc_address'] = $this->_getConfig()->obtenir('mandrill|bcc');
        }

        // Vars in the content
        $vars = array();
        foreach (($receiver + $data) as $key => $value) {
            $vars[] = array(
                'name'    => $key,
                'content' => $value,
            );
        }

        // Send the email using template
        try {
            $resp = $this->getMandrill()->messages->sendTemplate(
                $template,
                $vars,
                $parameters,
                $async,
                $ipPool,
                $sendAt
            );
        } catch(Mandrill_Error $e) {
            //throw $e;
            return false;
        }

        $status = $resp[0]['status'];
        return ($status === 'sent' || $status === 'queued');
    }

    /**
     * Send simple message
     * @param string $subject
     * @param string $message
     * @param array|null $receivers Array of receivers (['email', 'name']) or NULL to use default
     * @return bool
     */
    public function sendSimpleMessage($subject, $message, $receivers = null)
    {
        $data = array(
            'message' => $message,
        );
        $parameters = array(
            'subject' => $subject,
            'bcc_address' => false,
        );
        if (is_array($receivers)) {
            $parameters['to'] = $receivers;
        } else {
            $parameters['to'] = array(array(
                'email' => 'tresorier@afup.org',
                'name'  => 'Trésorier',
            ));
        }
        
        return $this->send('message', array(), $data, $parameters);
    }

}

