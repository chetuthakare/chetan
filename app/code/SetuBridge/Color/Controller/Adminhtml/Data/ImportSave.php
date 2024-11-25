<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Color\Controller\Adminhtml\Data;

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
        \SetuBridge\Color\Model\DataFactory $dataFactory,
        \Magento\Framework\Filesystem\Io\File $file,
        \SetuBridge\Personalization\Helper\Data $helperData
    ) {
        parent::__construct($context);
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->csv = $csv;
        $this->dataFactory = $dataFactory;
        $this->file = $file;
        $this->helperData = $helperData;  
    }

    public function execute()
    {
        $sampleDataStatus = false;
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

                    $result = $uploader->save($mediaDirectory->getAbsolutePath('Personalization/ImportColor'));
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
                        if(strtolower($val) == 'color_name'){
                            $colorNameKey = $k;
                        }
                        if(strtolower($val) == 'color_code'){
                            $colorCodeKey = $k;
                        }
                    }
                    $i++;
                    continue;
                }

                if(!isset($colorNameKey) || !isset($colorCodeKey)){
                    $this->messageManager->addError(__('First column is required (please select - field color_name & color_code)'));
                    if($sampleDataStatus){
                        return $this->_redirect($this->_redirect->getRefererUrl());  
                    }
                    else{
                        $this->_redirect('color/data/importcolor');
                        return;
                    }
                }

                $rowData = $this->dataFactory->create();

                if(($data[$colorNameKey] != '' || $data[$colorNameKey] != null) && ($data[$colorCodeKey] != '' || $data[$colorCodeKey] != null)){

                    $checkedData =$rowData->getCollection()->addFieldToFilter('color_code', array('eq' => $data[$colorCodeKey]))->getFirstItem();

                    if(!(empty($checkedData->getData())) && $data[$colorNameKey]){
                        $rowData->setData('color_name',$data[$colorNameKey]);
                        $rowData->setData('color_code',$data[$colorCodeKey]);
                        $rowData->setData('color_id',$checkedData->getData('color_id'));
                        $updatedStatus = true;
                        array_push($updatedData,$data[$colorNameKey]);
                    }
                    else{
                        $rowData->setData('color_name',$data[$colorNameKey]);
                        $rowData->setData('color_code',$data[$colorCodeKey]);
                        $newStatus = true;
                        array_push($newData,$data[$colorNameKey]);
                    }

                    if($sampleDataStatus){
                        $rowData->setData('sample_data_status',1);
                    }else{
                        $rowData->setData('sample_data_status',0);
                    }

                    $rowData->save();

                }
                else{
                    $requiredStatus = true;
                    array_push($requiredData,$data[$colorNameKey]);
                }

            }

            if($requiredStatus){
                $commaList = implode(', ', $requiredData);
                $this->messageManager->addError(__($commaList.' color name and color code is required field.'));
            }
            if($newStatus){
                $commaList = implode(', ', $newData);
                $this->messageManager->addSuccess(__($commaList.' Colors has been successfully saved.'));   
            }
            if($updatedStatus){
                $commaList = implode(', ', $updatedData);
                $this->messageManager->addSuccess(__($commaList.' Colors has been successfully updated.'));   
            }

        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            $this->messageManager->addError(__("Attention: Something went wrong."));
            if($sampleDataStatus){
                return $this->_redirect($this->_redirect->getRefererUrl());  
            }
            else{
                $this->_redirect('color/data/importcolor');
                return;
            }
        }
        if($requiredStatus){
            $this->_redirect('color/data/importcolor');
        }
        else{
            $this->_redirect('color/data/index');
        }
    }

    protected function _isAllowed()
    {
        return true;
    }

}