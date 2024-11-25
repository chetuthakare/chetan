<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
?>
<?php
namespace SetuBridge\Personalization\Model\Config\Source;

class MultiAddToCart implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'simple', 'label' => __('Simple')],
            ['value' => 'configrable', 'label' => __('Configrable')]
        ];
    }
}