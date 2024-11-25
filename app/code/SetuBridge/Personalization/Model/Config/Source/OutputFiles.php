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

class OutputFiles implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'configure_svg', 'label' => __('Configure Area Svg')],
            ['value' => 'full_svg', 'label' => __('Full Images Svg')],
            ['value' => 'configure_pdf', 'label' => __('Configure Area Pdf')],
            ['value' => 'full_pdf', 'label' => __('Full Images Pdf')],
        ];
    }
}