<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {
        $this->backendUrl = $backendUrl;
        parent::__construct($context);
    }
    public function getProductsGridUrl()
    {
        return $this->backendUrl->getUrl('personalization/grid/products',['_current' => true]);
    }
    public function getCustomizationUrl()
    {
        return $this->backendUrl->getUrl('personalization/grid/customization',['_current' => true]);
    }
}
