<?php
/**
 * Copyright © 2015 Excellence. All rights reserved.
 */
namespace Excellence\Pagespeed\Model\ResourceModel;

/**
 * Pagespeed resource
 */
class Pagespeed extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('excellence_pagespeed', 'id');
    } 
}
