<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Block\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ColorPicker extends Field {
    protected $_coreRegistry;
    public function __construct(
        Context $context, 
        Registry $coreRegistry, 
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }
    protected function _getElementHtml(AbstractElement $element) {
        $html = $element->getElementHtml();
        $cpPath=$this->getViewFileUrl('SetuBridge_Personalization::js');
        if (!$this->_coreRegistry->registry('colorpicker_loaded')) {
            $html .= '<script type="text/javascript" src="' . $cpPath.'/'.'jscolor.js"></script>';
            $this->_coreRegistry->registry('colorpicker_loaded', 1);
        }
        $html .= '<script type="text/javascript">
        var el = document.getElementById("' . $element->getHtmlId() . '");
        el.className = el.className + " jscolor{hash:true}";
        </script>';

        return $html;
    }

}