<?php
namespace Excellence\Pagespeed\Controller\Status;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Excellence\Pagespeed\Model\PagespeedFactory
     */
    protected $_pageSpeed;
    /**
     * @var \Excellence\Pagespeed\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Framework\App\Action\Context $contex
     * @param  \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Excellence\Pagespeed\Model\PagespeedFactory $pageSpeed
     * @param \Excellence\Pagespeed\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Excellence\Pagespeed\Model\PagespeedFactory $pageSpeed,
        \Excellence\Pagespeed\Helper\Data $helper

    ) {
        $this->_messageManager = $messageManager;
        $this->resultPageFactory = $resultPageFactory;
        $this->_pageSpeed = $pageSpeed;
        $this->_helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        if($this->_helper->getConfigVal('excellence_pagespeed/general/enabled')){
            $templateId = 'send_details';
            $this->_helper->getSpeedData();
            $post = $this->_pageSpeed->create();
            $speedInfo = $this->_helper->getPageSpeedCollection();
            $lastUpdateDate = $this->_helper->formatDate($speedInfo['created_at']);
            $runCron = $this->_helper->getTimeDiff($lastUpdateDate);
            if((count($speedInfo) > 0) && $runCron){
                $pageSpeedInfo = $this->_helper->getSpeedData();
                if($pageSpeedInfo['onload_time'] != $speedInfo['onload_time'] ||  $pageSpeedInfo['page_load_time'] != $speedInfo['page_load_time'] || $pageSpeedInfo['fully_loaded_time'] != $speedInfo['fully_loaded_time'] || $pageSpeedInfo['yslow_score'] != $speedInfo['yslow_score'] || $pageSpeedInfo['pagespeed_score'] != $speedInfo['pagespeed_score'] || $pageSpeedInfo['backend_duration'] != $speedInfo['backend_duration']){
                    $post->setData($pageSpeedInfo);
                    $tokenSave = $post->save();
                    $this->_helper->sendNotification($templateId);
                }
            }elseif((count($speedInfo) == 0) && !$speedInfo){
                $pageSpeedInfo = $this->_helper->getSpeedData();
                $post->setData($pageSpeedInfo);
                $tokenSave = $post->save();
            }
        }
    }
}