<?php

namespace Excellence\Pagespeed\Block\Adminhtml\Dashboard\Tab;

/**
 * Adminhtml dashboard PageSpeed Report tab
 */
class PagespeedReport extends \Excellence\Pagespeed\Block\Adminhtml\Dashboard\Report
{
    /**
     * @var string
     */
    protected $_defaultPeriod = 'all';

    /**
     * @var string
     */
    protected $_template = 'Excellence_Pagespeed::dashboard/pagespeed_report_tab.phtml';
}
