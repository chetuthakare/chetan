<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/ 
namespace SetuBridge\Personalization\Block\Adminhtml\Customer\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

class Tabs extends \Magento\Backend\Block\Template implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    /**
    * Core registry
    *
    * @var \Magento\Framework\Registry
    */
    protected $_coreRegistry;
    /**
    * @param \Magento\Backend\Block\Template\Context $context
    * @param \Magento\Framework\Registry $registry
    * @param array $data
    */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
    * @return string|null
    */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }
    /**
    * @return \Magento\Framework\Phrase
    */
    public function getTabLabel()
    {
        return __('Customer Saved Designs');
    }
    /**
    * @return \Magento\Framework\Phrase
    */
    public function getTabTitle()
    {
        return __('Customer Saved Designs');
    }
    /**
    * @return bool
    */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            return true;
        }
        return false;
    }

    /**
    * @return bool
    */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }
    /**
    * Tab class getter
    *
    * @return string
    */
    public function getTabClass()
    {
        return '';
    }
    /**
    * Return URL link to Tab content
    *
    * @return string
    */
    public function getTabUrl()
    {
        //replace the tab with the url you want
        return $this->getUrl('personalization/customer/mydesigns', ['_current' => true]);
    }
    /**
    * Tab should be loaded trough Ajax call
    *
    * @return bool
    */
    public function isAjaxLoaded()
    {
        return true;
    }
}