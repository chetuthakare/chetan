<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
?>
<?php
namespace SetuBridge\Clipart\Controller\Adminhtml\ClipartCategories;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory; 

class ImportSave extends \Magento\Backend\App\Action
{

    var $personalizationFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Csv $csv,
        \SetuBridge\Clipart\Model\ClipartCategoriesFactory $clipartcategoriesFactory
    ) {
        parent::__construct($context);
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->csv = $csv;
        $this->clipartcategoriesFactory = $clipartcategoriesFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $requestFiles=(array)$this->getRequest()->getFiles();
        $requiredData = array();
        $requiredStatus = false;
        $updatedStatus = false;
        $updatedData = array();
        $newStatus = false;
        $newData = array();

        try {
            if(!empty($this->getRequest()->getFiles('csv'))){
                $profileImage = $this->getRequest()->getFiles('csv');
                $fileName = ($profileImage && array_key_exists('name', $profileImage)) ? $profileImage['name'] : null;
                if ($profileImage && $fileName) {
                    $uploader = $this->uploaderFactory->create(['fileId' => 'csv']);
                    $uploader->setAllowedExtensions(['csv']);

                    $imageAdapterFactory = $this->adapterFactory->create();

                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);
                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

                    $result = $uploader->save($mediaDirectory->getAbsolutePath('Personalization/ImportClipartCategories'));
                }
            }         

            if(!isset($result['file']) || !isset($result['path'])){
                throw new \Exception(__('Unable to submit your request. Please, try again later')) ;
            }

            $new_path=$result['path'].$result['file'];
            $csv_data = $this->csv->getData($new_path);
            $i=0;


            foreach ($csv_data as $key=>$data){
                if($i==0)
                {
                    foreach($data as $k => $values){
                        $val = trim($values);
                        if(strtolower($val) == 'categories'){
                            $categoriesKey = $k;
                        }

                        if(strtolower($val) == 'status'){
                            $statusKey = $k;
                        }

                        if(strtolower($val) == 'position'){
                            $positionKey = $k;
                        }
                    }

                    $i++;
                    continue;
                }
            
                if(!isset($categoriesKey)){
                    $this->messageManager->addError(__('First column is required (please select - field categories)'));
                    $this->_redirect('clipart/clipartcategories/importclipartcategories');
                    return;
                }

                $rowData = $this->clipartcategoriesFactory->create();
                if(($data[$categoriesKey] != '' || $data[$categoriesKey] != null)){
                    $multiselectupdate =$rowData->getCollection()
                    ->addFieldToFilter('clipartcategories', array('eq' => $data[$categoriesKey]))->getFirstItem();

                    if(empty($multiselectupdate->getData()) && $data[$categoriesKey]){
                        $rowData->setData('clipartcategories',$data[$categoriesKey]);
                        if(isset($statusKey)){
                            if($data[$statusKey] == 0 || $data[$statusKey] == 1){
                                $rowData->setData('status',$data[$statusKey]);
                            }
                            else{
                                $rowData->setData('status',1);
                            }
                            if(isset($positionKey)){
                                $rowData->setData('position',$data[$positionKey]);
                            }
                        }
                        else{
                            $rowData->setData('status',1);
                        }
                        $newStatus = true;
                        array_push($newData,$data[$categoriesKey]);
                    }
                    else{
                        $rowData->setData('clipartcategories',$data[$categoriesKey]);

                        if(isset($statusKey)){
                            if($data[$statusKey] == 0 || $data[$statusKey] == 1){
                                $rowData->setData('status',$data[$statusKey]);
                            }
                            else{
                                $rowData->setData('status',1);
                            }
                            if(isset($positionKey)){
                                $rowData->setData('position',$data[$positionKey]);
                            }
                        }
                        else{
                            $rowData->setData('status',1);
                        }
                        
                        if(isset($positionKey)){
                            $rowData->setData('position',$data[$positionKey]);
                        }

                        $rowData->setData('clipartcategories_id',$multiselectupdate->getData('clipartcategories_id'));
                        $updatedStatus = true;
                        array_push($updatedData,$data[$categoriesKey]);

                    }
                    $rowData->save();
                }
                else{
                    $requiredStatus = true;
                    array_push($requiredData,$data[$categoriesKey]);
                }
            }


            if($requiredStatus){
                $commaList = implode(', ', $requiredData);
                $this->messageManager->addError(__($commaList.'categories is required field.'));
            }
            if($newStatus){
                $commaList = implode(', ', $newData);
                $this->messageManager->addSuccess(__($commaList.' Clipart categories has been successfully saved.'));   
            }
            if($updatedStatus){
                $commaList = implode(', ', $updatedData);
                $this->messageManager->addSuccess(__($commaList.' Clipart categories has been successfully updated.'));   
            }

        } catch (\Exception $e) {
            $this->messageManager->addError(__("Attention: Something went wrong."));
            $this->_redirect('clipart/clipartcategories/importclipartcategories');
            return;
        }

        if($requiredStatus){
            $this->_redirect('clipart/clipartcategories/importclipartcategories');
        }
        else{
            $this->_redirect('clipart/clipartcategories/index');
        }
    }

    protected function _isAllowed()
    {
        return true;
    }
}