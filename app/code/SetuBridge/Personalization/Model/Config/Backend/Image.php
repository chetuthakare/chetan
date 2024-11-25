<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
?>
<?php
namespace SetuBridge\Personalization\Model\Config\Backend;

class Image extends \Magento\Config\Model\Config\Backend\Image
{

    const UPLOAD_DIR = 'Personalization/Logo'; // Folder save image

    
    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    
    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    
    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png'];
    }
}