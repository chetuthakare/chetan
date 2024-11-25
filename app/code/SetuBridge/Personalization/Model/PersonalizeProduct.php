<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Model;
use Magento\Catalog\Model\Session;

class PersonalizeProduct extends \Magento\Framework\DataObject
{
    const OPTIONS_PRICE='personalize_options_price';
    const DIR_PATH='Personalization/SourceCode/Quote/%d/%d/FullImages';
    protected $imageProcessor;
    protected $serializer;
    protected $sessionFactory;
    public function __construct(
        ImageProcessor $imageProcessor,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        Session $session,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $file,
        \SetuBridge\Color\Model\DataFactory $colorFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    )
    {
        $this->imageProcessor =$imageProcessor;
        $this->serializer =$serializer;
        $this->session =$session;
        $this->registry = $registry;
        $this->directoryList =$directoryList;
        $this->file = $file;
        $this->colorFactory = $colorFactory;
        $this->_coreSession = $coreSession;
    }

    public function getColorNameByCode($code){

        $colorData = $this->colorFactory->create()->getCollection()->addFieldToFilter('color_code',$code)->getFirstItem();

        if($colorData && $colorData->getColorName()){
            return $colorData->getColorName();
        }
        return false;
    }

    public function registerPersonlizationOptions($product,$request, $optionColorValue = null){

        //$optionColorValue = $request->getParam('product_color');
        if($optionColorValue){
            if($this->getColorNameByCode($optionColorValue)){
                $options['Color'] = $this->getColorNameByCode($optionColorValue);
            }
        }

        $optionValue = $request->getParam('personlization_options');
        if($optionValue){
            $options['Comment'] = $optionValue;
        }

        $productOptions=[];
        if(!empty($options)){
            foreach($options as $label => $value){
                $productOptions[]=['label'=>$label,'value'=>$value];
            }
            $product->addCustomOption('additional_options', $this->serializer->serialize($productOptions));
        }
    }
    public function saveCustomerItemImage($QuoteId,$itemId,$personalize_image){

        if($itemId && $QuoteId && $personalize_image){



            if(file_exists($this->getDirPath($QuoteId,$itemId))){
                $this->deleteDir($this->getDirPath($QuoteId,$itemId));
            }

            foreach($personalize_image as $lebel=>$imgData){

                $this->imageProcessor->saveFile($QuoteId,$itemId,$imgData,$lebel);  
            } 
        }
        /* if($itemId && $request->getParam('personalize_image')){
        if(file_exists($this->getDirPath($QuoteId,$itemId))){
        $this->deleteDir($this->getDirPath($QuoteId,$itemId));
        }

        foreach($request->getParam('personalize_image') as $lebel=>$imgData){

        $this->imageProcessor->saveFile($QuoteId,$itemId,$imgData,$lebel);  
        } 

        }*/
    }

    public function saveCustomerQuoteItemImage($QuoteId,$itemId,$request){

        /* if($itemId && $QuoteId && array_key_exists("canvasAreaData",$request) && isset($request['canvasAreaData']) && !empty($request['canvasAreaData'])){

        $canvasAreaData = $request['canvasAreaData'];

        foreach($canvasAreaData as $lebel=>$imgData){
        $image = file_get_contents($imgData);
        if ($image !== false){
        $imgData = 'data:image/jpg;base64,'.base64_encode($image);

        }
        $this->imageProcessor->saveQuoteFile($QuoteId,$itemId,$imgData,$lebel);  
        } 

        }*/

        if($itemId && $request->getParam('canvasAreaData')){

            foreach($request->getParam('canvasAreaData') as $lebel=>$imgData){
                $image = file_get_contents($imgData);
                if ($image !== false){
                    $imgData = 'data:image/jpg;base64,'.base64_encode($image);

                }
                $this->imageProcessor->saveQuoteFile($QuoteId,$itemId,$imgData,$lebel);  
            } 

        }
    } 

    public function saveCustomerQuoteItemSvgImage($QuoteId,$itemId,$request){

        if($itemId && $request){

            foreach($request as $lebel=>$imgData){
                $this->imageProcessor->saveQuoteSvgFile($QuoteId,$itemId,$imgData,$lebel);  
            } 

        }
    }

    public function saveCustomerQuoteItemFullSvgImage($QuoteId,$itemId,$request){

        if($itemId && $request){

            foreach($request as $lebel=>$imgData){
                $this->imageProcessor->saveQuoteFullSvgFile($QuoteId,$itemId,$imgData,$lebel);  
            } 

        }
    }

    public function saveCustomerQuoteItemFonts($QuoteId,$itemId,$request){

        if($itemId && $request){
            foreach($request as $lebel=>$imgData){
                $this->imageProcessor->saveQuoteFontFamily($QuoteId,$itemId,$imgData,$lebel);  
            } 

        }
    }


    public function saveCustomerQuoteItemPDF($QuoteId,$itemId){

        $this->imageProcessor->saveQuoteItemPDF($QuoteId,$itemId);  

    }

    public function saveCustomerQuoteItemFullImagesPDF($QuoteId,$itemId,$request,$personalizationProductColor){

         $this->imageProcessor->saveQuoteItemFullImagePDF($QuoteId,$itemId,$request[$itemId],$personalizationProductColor);  

    }

    private function getDirPath($QuoteId,$itemId){
        return $this->directoryList->getPath('media').'/'.sprintf(self::DIR_PATH,$QuoteId,$itemId);
    }
    private function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function saveCustomerCustomDesignImage($designId,$request){
        if($designId && $request->getPostValue('images')){
            foreach($request->getPostValue('images') as $lebel=>$imgData){
                $this->imageProcessor->saveCustomDesignFile($designId,$imgData,$lebel);  
            } 

        }
    }
    public function prepareProductInfo($product){
        return [
            'name' =>$product->getName(),
            'price' =>$product->getFinalPrice(),
            'template_data' =>$this->getTemplateData($product),
            'template_id' => $product->getPersonalizationTemplate()

        ];
    }
    private function getTemplateData($product){

        return $product->getPersonalizationJsonObject();
    }
    private function getProductImages($product){

        $images = $product->getMediaGalleryImages();
        $jsondata = json_decode(trim($product->getId()));

        $newImages=[];
        if(!empty($images)){
            foreach($images as $img){
                if(!empty($jsondata)){
                    $row = strtolower($img->getLabel());
                    $label = $jsondata->$row; 
                }
                else{
                    $label = null;
                }
                $imageSize = getimagesize($img->getUrl());
                $newImages[strtolower($img->getLabel())]=['url'=>$img->getUrl(),'height'=>$imageSize[1],'width'=>$imageSize[0],
                    'template_data' => $label];
            }
        }
        return $newImages;
    }
    public function setProductCustomOptions($productId,$productOptions){
        $options=$this->getProductCustomOptions();
        if(empty($options)){
            $options=[];
        }
        $options[$productId]=$productOptions;
        $this->session->setProductCustomOptions($options);
    }
    public function getProductCustomOptions(){
        return $this->session->getProductCustomOptions();
    }
    public function getProductCustomOptionsById($productId){
        $options=$this->getProductCustomOptions();
        return isset($options[$productId])?$options[$productId]:[];
    }
    public function setOptionsPrice($price){
        if($this->registry->registry(self::OPTIONS_PRICE)){
            $this->registry->unregister(self::OPTIONS_PRICE); 
        }
        $this->registry->register(self::OPTIONS_PRICE,$price);
    }
    public function getOptionsPrice(){
        return $this->registry->registry(self::OPTIONS_PRICE);
    }

    public function saveAdminItemImage($itemId,$request){
        if($itemId && $request->getParam('canvasAreaData')){
            foreach($request->getParam('canvasAreaData') as $lebel=>$imgData){
                $this->imageProcessor->saveAdminFile($itemId,$imgData,$lebel);  
            }  
        }
    }

    public function removeDirectory($dirname) {
        if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                    unlink($dirname."/".$file);
                else
                    $this->removeDirectory($dirname.'/'.$file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

    public function dir_is_empty($dir) {
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                closedir($handle);
                return FALSE;
            }
        }
        closedir($handle);
        return TRUE;
    }

    public function copyOneFolderToAnotherDeleteCopyFolder($copyPath, $movedPath, $deleteCopyPath = true){

        if($this->isFileExist($copyPath)){

            $this->file->checkAndCreateFolder($movedPath);

            if (is_dir($copyPath) && !$this->dir_is_empty($copyPath)) {
                $this->recurse_copy(
                    $copyPath,$movedPath
                );  

                if($deleteCopyPath){
                    $this->removeDirectory($copyPath);
                }
                return true;
            }
        }
        return false;
    }

    private function isFileExist($path){
        return file_exists($path); 
    }

    public function recurse_copy($src,$dst) { 
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    $this->recurse_copy($src . '/' . $file,$dst . '/' . $file); 
                } 
                else { 
                    copy($src . '/' . $file,$dst . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    } 

    public function copyFolderSourceToDestination($source, $destination, $removeSourceDir = false){

        if (is_dir($source) && !$this->dir_is_empty($source)) {
            $this->recurse_copy(
                $source,$destination
            );  

            if($removeSourceDir){
                $this->removeDirectory($source);
            }
        }

    }

    public function setSessionPersonalizationJsonData($itemId, $request, $status = false, $color = null){
        if($itemId && $request){
            $this->_coreSession->start();
            if($status){
                $personalizationJsonObject = $request;
            }
            else{
                $personalizationJsonObject = $request->getParam('exportjson');
                if($color){
                    $data = $this->serializer->unserialize($personalizationJsonObject);
                    $data[0]['backgroundColor'] =  '#'.$color;
                    $personalizationJsonObject = $this->serializer->serialize($data);
                }
            }

            if($this->getSessionPersonalizationJsonData()){
                $personalizationArray = $this->getSessionPersonalizationJsonData();
            }

            $personalizationArray[$itemId] = $personalizationJsonObject;    

            $this->_coreSession->setSessionPersonalizationJsonData($personalizationArray);
        }
    }

    public function getSessionPersonalizationJsonData(){
        $this->_coreSession->start();
        return $this->_coreSession->getSessionPersonalizationJsonData();
    }

    public function unsSessionPersonalizationJsonData(){
        $this->_coreSession->start();
        return $this->_coreSession->unsSessionPersonalizationJsonData();
    }

    public function setSessionPersonalizationReorderItemId($itemId){
        $this->_coreSession->start();
        $this->_coreSession->setSessionPersonalizationReorderItemId($itemId);

    }

    public function getSessionPersonalizationReorderItemId(){
        $this->_coreSession->start();
        return $this->_coreSession->getSessionPersonalizationReorderItemId();
    }

    public function unsSessionPersonalizationReorderItemId(){
        $this->_coreSession->start();
        return $this->_coreSession->unsSessionPersonalizationReorderItemId();
    }

    public function setSessionPersonalizationReorderOrderId($orderId){
        $this->_coreSession->start();
        $this->_coreSession->setSessionPersonalizationReorderOrderId($orderId);

    }

    public function getSessionPersonalizationReorderOrderId(){
        $this->_coreSession->start();
        return $this->_coreSession->getSessionPersonalizationReorderOrderId();
    }

    public function unsSessionPersonalizationReorderOrderId(){
        $this->_coreSession->start();
        return $this->_coreSession->unsSessionPersonalizationReorderOrderId();
    }

    public function setPersonalizationCartData($request){
        $this->_coreSession->start();
        $this->_coreSession->setPersonalizationCartData($request);

    }

    public function getPersonalizationCartData(){
        $this->_coreSession->start();
        return $this->_coreSession->getPersonalizationCartData();
    }

    public function unsPersonalizationCartData(){
        $this->_coreSession->start();
        return $this->_coreSession->unsPersonalizationCartData();
    }

    public function setPersonalizationSourceDataSavedId($status){
        $this->_coreSession->start();
        $this->_coreSession->setPersonalizationSourceDataSavedId($status);

    }

    public function getPersonalizationSourceDataSavedId(){
        $this->_coreSession->start();
        return $this->_coreSession->getPersonalizationSourceDataSavedId();
    }

    public function unsPersonalizationSourceDataSavedId(){
        $this->_coreSession->start();
        return $this->_coreSession->unsPersonalizationSourceDataSavedId();
    }

}