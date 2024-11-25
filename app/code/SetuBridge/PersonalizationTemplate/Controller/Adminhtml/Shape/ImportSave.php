<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
?>
<?php

namespace SetuBridge\PersonalizationTemplate\Controller\Adminhtml\Shape;

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
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationShapeFactory $shapeFactory,
        DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $file,
        \SetuBridge\Personalization\Helper\Data $helperData
    ) {
        parent::__construct($context);
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->csv = $csv;
        $this->shapeFactory = $shapeFactory;
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

                    $result = $uploader->save($mediaDirectory->getAbsolutePath('Personalization/ImportShape'));
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
            foreach ($csv_data as $key=>$data){
                if($i==0)
                {
                    foreach($data as $k => $values){
                        $val = trim($values);
                        if(strtolower($val) == 'name'){
                            $shapeName = $k;
                        }

                        if(strtolower($val) == 'image'){
                            $shapeImagePathKey = $k;
                        }

                        if(strtolower($val) == 'status'){
                            $statusKey = $k;
                        }
                    }

                    $i++;
                    continue;
                }

                if(!isset($shapeImagePathKey) || !isset($shapeName)){
                    $this->messageManager->addError(__('First column is required (please select - field name & image)'));
                    if($sampleDataStatus){

                        return $this->_redirect($this->_redirect->getRefererUrl());  
                    }
                    else{
                        $this->_redirect('personalization/shape/importshape');
                        return;
                    }
                }

                $rowData = $this->shapeFactory->create();

                if(($data[$shapeName] != '' || $data[$shapeName] != null) && ($data[$shapeImagePathKey] != '' || $data[$shapeImagePathKey] != null)){

                    $image_path = $this->ImageCustomSave($data[$shapeImagePathKey],$file_directory);  
                    $rowData->setData('shape_image',$image_path);
                    $rowData->setData('shape_name',$data[$shapeName]);
                    if(isset($statusKey) &&($data[$statusKey] == 0 || $data[$statusKey] == 1)){ 
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

                    $rowData->save();

                }
            }

            $this->messageManager->addSuccess(__('patterns has been successfully saved.'));
        } catch (\Exception $e) {
            if($sampleDataStatus){
                $this->messageManager->addError(__($e->getMessage()));
                $this->messageManager->addError(__("Attention: Something went wrong."));
                return $this->_redirect($this->_redirect->getRefererUrl());  
            }
            else{
                $this->messageManager->addError(__($e->getMessage()));
                $this->messageManager->addError(__("Attention: Something went wrong."));
                $this->_redirect('personalization/shape/importshape');
                return;
            }
        }

        $this->_redirect('personalization/shape/index');

    }

    protected function _isAllowed()
    {
        return true;
    }

    public function ImageCustomSave($image_path,$file_directory = 'Personalization/import'){
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $tmpDir = $mediaDirectory->getAbsolutePath('Personalization/importShape/Shape');

        $this->file->checkAndCreateFolder($tmpDir);
        $file_directory = substr($file_directory, strpos($file_directory, "media/") + 6); 
        $filename = $file_directory.'/'.$image_path;         
        $subdirToSave = 'Personalization/importShape/Shape/'.$image_path;  
        $result = $this->mediaDirectory->copyFile(
            $filename,$subdirToSave
        );          
        if(isset($result)){
            return $subdirToSave;
        }
        return false;
    }

}