<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Faqs
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Faqs\Model\Config\Source\System;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class LinkPosition
 * @package Mageplaza\Faqs\Model\Config\Source\System
 */
class LinkPosition implements ArrayInterface
{
    const TOPLINK    = 0;
    const FOOTERLINK = 1;
    const CATEGORY   = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->toArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['' => __('-- Please select --'), self::TOPLINK => __('Toplink'), self::FOOTERLINK => __('Footerlink'), self::CATEGORY => __('Category')
        ];
    }
}
