<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Block\Adminhtml\ImportFont;

class Csvfile extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_assetRepo;

    public function __construct( 
         \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
         $this->_assetRepo = $assetRepo;
    }

    public function getElementHtml()
    {

         $csvFile = $this->_assetRepo->getUrl('SetuBridge_PersonalizationTemplate::csv/fonts.csv');
         $csvLink = "<a href=".$csvFile." style='padding-left: 50%;' class='sample-file' download>Download Sample File</a><br><button style='margin-left: 50%; margin-top:10px;'>Import</button>";
        return $csvLink;
    }

}