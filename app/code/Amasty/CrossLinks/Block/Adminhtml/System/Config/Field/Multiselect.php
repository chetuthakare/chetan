<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Cross Linking for Magento 2
 */

namespace Amasty\CrossLinks\Block\Adminhtml\System\Config\Field;

use Magento\Framework\Data\Form\Element\AbstractElement as AbstractElement;

class Multiselect extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setData('size', count($element->getValues()) + 1 ?: 10);
        return $element->getElementHtml();
    }
}
