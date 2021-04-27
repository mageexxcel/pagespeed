<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Excellence\Pagespeed\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $resultPage;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    
    public function execute()
    {
        /** 
         * @var \Magento\Backend\Model\View\Result\Page $resultPage 
         */
        $this->resultPage = $this->resultPageFactory->create();
        $this->resultPage->setActiveMenu('Magento_ImportExport::system_convert_export');
        $this->resultPage->getConfig()->getTitle()->prepend(__('Import/Export'));
        $this->resultPage->getConfig()->getTitle()->prepend(__('Export'));
        $this->resultPage->addBreadcrumb(__('Export'), __('Export'));
        return $this->resultPage;
    }
}
