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

class HelpTabOptions implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'link', 'label' => __('Link')],
            ['value' => 'video', 'label' => __('Video popup')],
        ];
    }
}