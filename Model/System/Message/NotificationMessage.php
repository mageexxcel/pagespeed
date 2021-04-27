<?php
/**
* @author Excellence Technologies Pvt. Ltd
* @copyright Copyright (c) 2017 Excellence
* @package Excellence_AdminNotifications
*/

//File: app/code/Atwix/AdminNotifications/Model/System/Message/CustomSystemMessage.php

namespace Excellence\Pagespeed\Model\System\Message;

use Magento\Framework\Notification\MessageInterface;

/**
* Class CustomNotification
*/
class NotificationMessage implements MessageInterface
{
    protected $helper;

    public function __construct(
        \Excellence\Pagespeed\Helper\Data $helper

    ) {
        $this->helper = $helper;
    }
    public function getIdentity()
    {
        // Retrieve unique message identity
        return 'identity';
    }

    public function isDisplayed()
    {
        // Return true to show your message, false to hide it
        $recordInfo = $this->helper->getLastTwoCollection();
        if(!$recordInfo){
            return false;
        }
        return true;
    }

    public function getText()
    {
        $recordInfo = $this->helper->getLastTwoCollection();
        if($recordInfo){
            return $recordInfo;
        }
        // Retrieve message text
    }

    public function getSeverity()
    {
        // Possible values: SEVERITY_CRITICAL, SEVERITY_MAJOR, SEVERITY_MINOR, SEVERITY_NOTICE
        return self::SEVERITY_MAJOR;
    }
}