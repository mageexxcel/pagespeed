<?php

namespace Excellence\Pagespeed\Block\Adminhtml\Dashboard;

use Magento\Backend\Block\Template\Context;

class Report extends \Magento\Backend\Block\Template
{
    /**
     * @method __construct
     * @param  Context                $context
     * @param  array                  $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
}
