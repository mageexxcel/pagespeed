<?php
namespace Excellence\Pagespeed\Model\Config\Source;
class Crontab extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const HR1 = 60;
    const HR2 = 120;
    const HR6 = 360;
    const HR12 = 720;
    protected $_options;

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => self::HR1, 'label' => __('Every Hour')],
                ['value' => self::HR2, 'label' => __('Every 2nd Hour')],
                ['value' => self::HR6, 'label' => __('Every 6th Hour')],
                ['value' => self::HR12, 'label' => __('Every 12th Hour')]
            ];
        }
        return $this->_options;
    }
    final public function toOptionArray()
    {
        return array(
            array('value' => self::HR1, 'label' => __('Every Hour')),
            array('value' => self::HR2, 'label' => __('Every 2nd Hour')),
            array('value' => self::HR6, 'label' => __('Every 6th Hour')),
            array('value' => self::HR12, 'label' => __('Every 12th Hour'))
        );
    }
}