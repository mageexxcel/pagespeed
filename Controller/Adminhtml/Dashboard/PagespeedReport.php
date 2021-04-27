<?php
namespace Excellence\Pagespeed\Controller\Adminhtml\Dashboard;

use Magento\Backend\Controller\Adminhtml\Dashboard\AjaxBlock;

class PagespeedReport extends AjaxBlock
{
    /**
     * Gets Excellence PageSpeed Report tab
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $output = $this->layoutFactory->create()
            ->createBlock(\Excellence\Pagespeed\Block\Adminhtml\Dashboard\Tab\PagespeedReport::class)
            ->setId('pagespeedReportTab')
            ->toHtml();
        $resultRaw = $this->resultRawFactory->create();

        return $resultRaw->setContents($output);
    }
}