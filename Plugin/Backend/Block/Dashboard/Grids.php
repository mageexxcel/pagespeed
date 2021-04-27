<?php

namespace Excellence\Pagespeed\Plugin\Backend\Block\Dashboard;

/**
 * Plugin for Grids
 */
class Grids
{
    /**
     * @method beforeRenderResult
     * @param  \Magento\Backend\Block\Dashboard\Grids $subject
     * @return array
     */
    public function beforeToHtml(
        \Magento\Backend\Block\Dashboard\Grids $subject
    ) {
        $subject->addTab(
            'pagespeed_report',
            [
                'label' => __('PageSpeed Report Chart'),
                'url' => $subject->getUrl('pagestatic/*/PagespeedReport', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
    }
}