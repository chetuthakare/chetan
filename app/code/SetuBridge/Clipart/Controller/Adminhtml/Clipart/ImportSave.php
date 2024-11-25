<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
?>
<?php

namespace SetuBridge\Clipart\Controller\Adminhtml\Clipart;

use Magento\Framework\Controller\ResultFactory; 
use Magento\Framework\App\Filesystem\DirectoryList;

class ImportSave extends \Magento\Backend\App\Action
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Csv $csv,
        \SetuBridge\Clipart\Model\ClipartFactory $clipartFactory,
        \SetuBridge\Clipart\Model\ClipartCategoriesFactory $clipartcategoriesFactory,
        DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $file,
        \SetuBridge\Personalization\Helper\Data $helperData
    ) {
        parent::__construct($context);
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->csv = $csv;
        $this->clipartFactory = $clipartFactory;
        $this->clipartcategoriesFactory = $clipartcategoriesFactory;
        $this->file = $file;
        $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->helperData = $helperData;  
    }

    public function execute()
    {
        $sampleDataStatus = false;
        $data = $this->getRequest()->getPostValue();
        $requestFiles=(array)$this->getRequest()->getFiles();
        $file_directory = $this->getRequest()->getParam('file_directory');

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

                    $result = $uploader->save($mediaDirectory->getAbsolutePath('Personalization/ImportClipart'));
                }
            }
            if(isset($data['resultPath']) && isset($data['CsvName'])){
                $sampleDataStatus = true;
                $result['file'] = $data['CsvName'];
                $result['path'] = $data['resultPath'];
            }         

            if(!isset($result['file']) || !isset($result['path'])){
                if($sampleDataStatus){
                    $this->messageManager->addError(__('Unable to submit your request. Please, try again later'));
                    return $this->_redirect($this->_redirect->getRefererUrl());  
                }
                else{
                    throw new \Exception(__('Unable to submit your request. Please, try again later')) ;
                }
            }

            $new_path=$result['path'].$result['file'];
            $csv_data = $this->csv->getData($new_path);

            $i=0;
            $allreadyData = array();
            $status = false;
            $checkedcategory = array();
            $checkedcategorystatus = false;
            $newcategory = array();
            $newcategorystatus = false;
            foreach ($csv_data as $key=>$data){
                if($i==0)
                {
                    foreach($data as $k => $values){
                        $val = trim($values);
                        if(strtolower($val) == 'clipart_categories'){
                            $clipartCategoriesKey = $k;
                        }

                        if(strtolower($val) == 'clipart_image_path'){
                            $clipartImagePathKey = $k;
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

                if(!isset($clipartImagePathKey) || !isset($clipartCategoriesKey)){
                    $this->messageManager->addError(__('First column is required (please select - field clipart_categories & clipart_image_path)'));
                    if($sampleDataStatus){
                        return $this->_redirect($this->_redirect->getRefererUrl());  
                    }
                    else{
                        $this->_redirect('clipart/clipart/importclipart');
                        return;
                    }
                }

                $clipartCategories = $this->clipartcategoriesFactory->create();
                $clipartCategoriesData =$clipartCategories->getCollection()
                ->addFieldToFilter('clipartcategories', array('eq' => $data[$clipartCategoriesKey]))
                ->addFieldToSelect(array('clipartcategories','clipartcategories_id'))->getFirstItem();

                $rowData = $this->clipartFactory->create();

                if(($data[$clipartCategoriesKey] != '' || $data[$clipartCategoriesKey] != null) && ($data[$clipartImagePathKey] != '' || $data[$clipartImagePathKey] != null)){
                    if(!empty($clipartCategoriesData->getData())){          
                        $image_path = $this->ImageCustomSave($data[$clipartImagePathKey],$file_directory);  
                        $rowData->setData('clipart_image',$image_path);
                        if(isset($statusKey) &&($data[$statusKey] == 0 || $data[$statusKey] == 1)){ 
                            $rowData->setData('status',$data[$statusKey]);
                        }
                        else{
                            $rowData->setData('status',1);
                        }
                        if(isset($positionKey)){
                            $rowData->setData('position',$data[$positionKey]);
                        }
                        if($sampleDataStatus){
                            $rowData->setData('sample_data_status',1);
                        }else{
                            $rowData->setData('sample_data_status',0);
                        }
                        $rowData->setData('category',$clipartCategoriesData->getClipartcategoriesId());
                        $rowData->save();
                    }
                    else{
                        $newcategorystatus = true;
                        array_push($newcategory,$data[$clipartCategoriesKey]);
                        $clipartCategories->setData('clipartcategories',$data[$clipartCategoriesKey]);

                        if(isset($statusKey) &&($data[$statusKey] == 0 || $data[$statusKey] == 1)){         
                            $clipartCategories->setData('status',$data[$statusKey]);
                        }
                        else{
                            $clipartCategories->setData('status',1);
                        }
                
                        $clipartCategories->save();
                        $clipartCategories = $this->clipartcategoriesFactory->create();
                        $clipartCategoriesData =$clipartCategories->getCollection()
                        ->addFieldToFilter('clipartcategories', array('eq' => $data[$clipartCategoriesKey]))
                        ->addFieldToSelect(array('clipartcategories','clipartcategories_id'))->getFirstItem();

                        $image_path = $this->ImageCustomSave($data[$clipartImagePathKey],$file_directory);
                        $rowData->setData('clipart_image',$image_path);
                        if(isset($statusKey)){
                            $rowData->setData('status',$data[$statusKey]);
                        }
                        else{
                            $rowData->setData('status',1);
                        }
                        if($sampleDataStatus){
                            $rowData->setData('sample_data_status',1);
                        }else{
                            $rowData->setData('sample_data_status',0);
                        }
                        if(isset($positionKey)){
                            $rowData->setData('position',$data[$positionKey]);
                        }
                        $rowData->setData('category',$clipartCategoriesData->getClipartcategoriesId());
                        $rowData->save();
                    }
                }
                else{
                    $checkedcategorystatus = true;
                    array_push($checkedcategory,$data[$clipartImagePathKey]);
                }

            }

            if($status){
                $commaList = implode(', ', $allreadyData);
                $this->messageManager->addError(__($commaList.'  Clipart has already in created.'));
            }
            if($checkedcategorystatus){
                $commaList = implode(', ', $checkedcategory);
                $this->messageManager->addError(__($commaList.'  in Clipart image or Category is required filled.'));
            }
            if($newcategorystatus){
                $commaList = implode(', ', $newcategory);
                $this->messageManager->addSuccess(__($commaList.'  in new ClipartCategories are created.'));
            }
            $this->messageManager->addSuccess(__('Clipart has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            if($sampleDataStatus){
                $this->messageManager->addError(__("Attention: Something went wrong."));
                return $this->_redirect($this->_redirect->getRefererUrl());  
            }
            else{
                $this->messageManager->addError(__("Attention: Something went wrong."));
                $this->_redirect('clipart/clipart/importclipart');
                return;
            }
        }

        if($checkedcategorystatus){
            $this->_redirect('clipart/clipart/importclipart');
        }else{
            $this->_redirect('clipart/clipart/index');
        }


    }

    protected function _isAllowed()
    {
        return true;
    }

    public function ImageCustomSave($image_path,$file_directory = 'Personalization/import'){
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $tmpDir = $mediaDirectory->getAbsolutePath('Personalization/importClipart/Clipart');

        $this->file->checkAndCreateFolder($tmpDir);
        $file_directory = substr($file_directory, strpos($file_directory, "media/") + 6); 
        $filename = $file_directory.'/'.$image_path;         
        $subdirToSave = 'Personalization/importClipart/Clipart/'.$image_path;  
        $result = $this->mediaDirectory->copyFile(
            $filename,$subdirToSave
        );          
        if(isset($result)){
            return $subdirToSave;
        }
        return false;
    }

}