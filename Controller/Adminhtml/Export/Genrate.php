<?php
/**
 *
 * Copyright © 2015 Excellencecommerce. All rights reserved.
 */
namespace Excellence\Pagespeed\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Genrate extends \Magento\Backend\App\Action
{

	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Framework\Controller\Result\Redirect
     */
    protected $resultRedirectFactory;
    /**
     * @var \Excellence\Pagespeed\Model\PagespeedFactory
     */
    protected $_pageSpeed;
    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @param Context $context
     * @param \Excellence\Pagespeed\Model\PagespeedFactory $pageSpeed
     * @param \Magento\Framework\Controller\Result\Redirect $resultRedirectFactory
     * @param TimezoneInterface $timezone
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Excellence\Pagespeed\Model\PagespeedFactory $pageSpeed,
        \Magento\Framework\Controller\Result\Redirect $resultRedirectFactory,
        TimezoneInterface $timezone,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->timezone = $timezone;
        $this->_pageSpeed = $pageSpeed;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    { 
        $fromDate = $this->getRequest()->getParam('report-from-date');
        $toDate = $this->getRequest()->getParam('report-to-date');
        $fromDate = $this->timezone->date(new \DateTime($fromDate))->format('Y-m-d H:i:s');
        $toDate = $this->timezone->date(new \DateTime($toDate))->format('Y-m-d H:i:s');

        $collections = $this->_pageSpeed->create()->getCollection()->addFieldToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate));
        
        $file = $this->createCsv($collections);

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl("pagestatic/export/index");
        return $resultRedirect;
    }

    public function createCsv($collections)
    {
        $heading = [
         __('Gmetric Id'),
         __('Onload Time (Sec)'),
         __('Page Load Time (Sec)'),
         __('fully Load Time (Sec)'),
         __('Page Speed Score'),
         __('Created At')
        ];
        $outputFile = "PagespeedReport". date('Ymd_His').".csv";
        $handle = fopen($outputFile, 'w');
        fputcsv($handle, $heading);
        foreach ($collections as $item) {
            $row = [
                $item->getGtmetrixId(),
                $item->getOnloadTime(),
                $item->getPageLoadTime(),
                $item->getFullyLoadedTime(),
                $item->getPagespeedScore(),
                $item->getCreatedAt()         
            ];
            fputcsv($handle, $row);
        }
        $this->downloadCsv($outputFile);

        print_r($outputFile); die;
        return $outputFile;
    }

    public function downloadCsv($file)
    {
        if (file_exists($file)) {
            
            //set appropriate headers
            header('Content-Description: File Transfer');
            // header('Content-Type: application/csv');
            header('Content-type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();flush();
            readfile($file);
        }
    }
}
