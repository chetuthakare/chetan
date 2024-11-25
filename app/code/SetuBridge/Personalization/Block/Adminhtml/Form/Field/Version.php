<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*/
namespace SetuBridge\Personalization\Block\Adminhtml\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\ModuleListInterface;
class Version extends \Magento\Config\Block\System\Config\Form\Field
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ModuleListInterface $moduleList,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_moduleList = $moduleList;
    }
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setValue($this->getExtensionVersion()); 
        $html = parent::_getElementHtml($element);
        return $html;
    }
    protected function getExtensionVersion()
    {
        $classPath = explode('\\', __CLASS__);
        return $this->_moduleList->getOne(sprintf("%s_%s",$classPath[0],$classPath[1]))['setup_version'];
    }
}
