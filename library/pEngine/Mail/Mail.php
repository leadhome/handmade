<?php

class pEngine_Mail_Mail
{
    /**
     * Send email
     *
     * $recipients sample:
     * array(array('name' => 'name1', 'email' => 'email1'), array('name' => 'name2', 'email' => 'email2'))
     *
     * $text - html message text
     *
     * @param array(array) $recipients
     * @param string $subject
     * @param string $text
     * @return bool
     */
    public function send($recipients, $subject, $text)
    {
        try
        {
            $config = Zend_Registry::get('options');
            $config = $config->mail->toArray();

            $mailTransport = new Zend_Mail_Transport_Smtp($config['host'], $config);
            $zmail = new Zend_Mail('utf-8');

            foreach ($recipients as $item)
            {
                $zmail->addTo($item['email'], $item['name']);
            }

            $zmail->setFrom($config['email'], $config['companyName']);

            $zmail->setSubject($subject);
            $zmail->setBodyHtml($text);

            $zmail->send($mailTransport);

            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
}