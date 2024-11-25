<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Block\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\App\Filesystem\DirectoryList;

class FontButton extends Field
{
    const CSV_FILE ="personalization_fonts.csv";

    protected $_template = 'SetuBridge_Personalization::System/Config/button.phtml';

    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\Filesystem $filesystem,   
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->fileDriver =$fileDriver;
        $this->filesystem = $filesystem;
    }

    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }
    public function getControllerUrl()
    {
        return $this->getUrl('personalization/index/fontsampledata');
    }
    public function csvIsExist()
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);            
        $resultPath = $mediaDirectory->getAbsolutePath('Personalization/import/csv/');
        $csvFileName = sprintf(self::CSV_FILE);
        $dir =  $resultPath.$csvFileName;
        $rr = file_exists($dir);
        return $rr;
    }
    
     public function csvNotExistMessage()
    {
        $csvFileName = sprintf(self::CSV_FILE);
        $dir =  '/pub/media/Personalization/import/csv/'.$csvFileName;
        $str = "The file does'nt exist in the following path ".$dir;
        return $str;
    }    
    
    public function imgDirectoryExist()
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);            
        $dir = $mediaDirectory->getAbsolutePath('Personalization/import/fonts');
        return is_dir($dir);
    }

    public function imgDirectoryNotExistMessage()
    {
        $csvFileName = sprintf(self::CSV_FILE);
        $dir =  '/pub/media/Personalization/import/cliparts';
        $str = "The folder for images does'nt exist in the following path : '".$dir."'";
        return $str;
    }    
}