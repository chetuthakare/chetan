<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Model;

class ImageProcessor extends \Magento\Framework\DataObject
{
    const IMAGE_DIR_PATH='Personalization/SourceCode/Quote/%d/%d/FullImages/%s.png';
    const QUOTE_ITEM_DIR_PATH='Personalization/SourceCode/Order/%d/%d';
    const QUOTE_PDF_DIR_PATH='Personalization/SourceCode/Order/%d/%d/PDF';
    const QUOTE_IMAGE_DIR_PATH='Personalization/SourceCode/Quote/%d/%d/Png/%s.png';
    const QUOTE_SVG_IMAGE_DIR_PATH='Personalization/SourceCode/Order/%d/%d/Svg/%s.svg';
    const QUOTE_FULL_SVG_IMAGE_DIR_PATH='Personalization/SourceCode/Order/%d/%d/FullSvg/%s.svg';
    const QUOTE_FONT_DIR_PATH='Personalization/SourceCode/Order/%d/%d/font';
    const CUSTOM_IMAGE_DIR_PATH='Personalization/CustomerSaveDesign/%d/%s.png';
    const CUSTOM_DESIGN_DIR_PATH='Personalization/CustomerSaveDesign/%d';
    const DIR_PATH='Personalization/SourceCode/Quote/%d/%d/FullImages';
    const DIR_ORDER_PATH='Personalization/SourceCode/Order/%d/%d/FullImages';
    const ADMIN_IMAGE_DIR_PATH='Personalization/personalization/admin/%d.svg';
    const ADMIN_DIR_PATH='Personalization/personalization/admin/%d';
    protected $directory;
    protected $storeManager;
    protected $fileDriver;
    protected $mediaUrl;
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    )
    {
        $this->directoryList =$directoryList;
        $this->directory =$filesystem->getDirectoryWrite('media');
        $this->storeManager =$storeManager;
        $this->fileDriver =$fileDriver;
        $this->_file = $file;
        $this->request = $request;
        $this->mediaUrl=$this->storeManager->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $this->serializer =$serializer;
    }
    public function saveFile($QuoteId,$id,$content,$imgType){
        list($type, $data) = explode(';', $content);
        list(, $data)      = explode(',', $data);
        if($this->directory->writeFile($this->getImageDir($QuoteId,$id,$imgType),base64_decode($data))){
            return $this->getImageDir($QuoteId,$id,$imgType);
        }
        return false;
    }
    public function saveQuoteFile($QuoteId,$id,$content,$imgType){
        list($type, $data) = explode(';', $content);
        list(, $data)      = explode(',', $data);
        if($this->directory->writeFile($this->getQuoteImageDir($QuoteId,$id,$imgType),base64_decode($data))){
            return $this->getQuoteImageDir($QuoteId,$id,$imgType);
        }
        return false;
    }
    public function saveQuoteSvgFile($QuoteId,$id,$content,$imgType){

        if($this->directory->writeFile($this->getQuoteSvgImageDir($QuoteId,$id,$imgType),$content)){
            return $this->getQuoteSvgImageDir($QuoteId,$id,$imgType);
        }

        return false;
    }

    public function saveQuoteFullSvgFile($QuoteId,$id,$content,$imgType){

        if($this->directory->writeFile($this->getQuoteFullSvgImageDir($QuoteId,$id,$imgType),$content)){
            return $this->getQuoteFullSvgImageDir($QuoteId,$id,$imgType);
        }

        return false;
    }

    public function saveQuoteFontFamily($QuoteId,$id,$path,$imgType){


        $filePath = $this->getQuoteFontFamilyDir($QuoteId,$id);
        $fontfamilyPath = $this->directoryList->getPath('media').'/'.$filePath;

        if (!is_dir($fontfamilyPath)) {
            $ioAdapter = $this->_file;
            $ioAdapter->mkdir($fontfamilyPath, 0775);
        }

        $fontOrignalPath = $this->directoryList->getPath('media').'/'.$path;
        $fileName = pathinfo($path);
        $fontfamilyPath = $fontfamilyPath.'/'.$fileName['basename'];

        copy($fontOrignalPath,$fontfamilyPath);
        return true; 
    }

    public function getQuoteFontFamilyDir($QuoteId,$id){
        return sprintf(self::QUOTE_FONT_DIR_PATH,$QuoteId,$id);
    }
    public function getQuoteImageDir($QuoteId,$id,$imgType='front'){
        return sprintf(self::QUOTE_IMAGE_DIR_PATH,$QuoteId,$id,$imgType);
    }
    public function getQuoteSvgImageDir($QuoteId,$id,$imgType='front'){
        return sprintf(self::QUOTE_SVG_IMAGE_DIR_PATH,$QuoteId,$id,$imgType);
    }

    public function getQuoteFullSvgImageDir($QuoteId,$id,$imgType='front'){
        return sprintf(self::QUOTE_FULL_SVG_IMAGE_DIR_PATH,$QuoteId,$id,$imgType);
    }
    public function getImageDir($QuoteId,$id,$imgType='front'){
        return sprintf(self::IMAGE_DIR_PATH,$QuoteId,$id,$imgType);
    }

    private function getDirPath($itemId,$quoteId){
        if($this->request->getFullActionName() == 'checkout_cart_index'){
            return $this->directoryList->getPath('media').'/'.sprintf(self::DIR_PATH,$quoteId,$itemId);
        }
        else{
            return $this->directoryList->getPath('media').'/'.sprintf(self::DIR_ORDER_PATH,$quoteId,$itemId);
        }
    }
    private function getImageWebUrl($itemId,$quoteId,$imageName){
        if($this->request->getFullActionName() == 'checkout_cart_index'){
            return $this->mediaUrl.sprintf(self::DIR_PATH,$quoteId,$itemId).'/'.$imageName;
        }
        else{
            return $this->mediaUrl.sprintf(self::DIR_ORDER_PATH,$quoteId,$itemId).'/'.$imageName;
        }
    }
    public function getItemOptionImages($itemId,$quoteId){

        $imgDir=$this->getDirPath($itemId,$quoteId);

        if($this->fileDriver->isExists($imgDir) && $this->fileDriver->isDirectory($imgDir)){
            $images =$this->fileDriver->readDirectory($imgDir);
            $itemImages=[];
            if(!empty($images)){
                foreach($images as $img){
                    $itemImages[] =$this->getImageWebUrl($itemId,$quoteId,basename($img)); 
                }
            }

            return $itemImages;
        }
        return [];
    }

    public function saveAdminFile($id,$content,$imgType){
        list($type, $data) = explode(';', $content);
        list(, $data)      = explode(',', $data);

        if($this->directory->writeFile($this->getAdminImageDir($id,$imgType),base64_decode($data))){
            return $this->getAdminImageDir($id,$imgType);;
        }
        return false;
    }


    public function getAdminImageDir($id,$imgType='front'){
        return sprintf(self::ADMIN_IMAGE_DIR_PATH,$id,$imgType);
    }
    private function isAdminFileExist($itemId){
        return file_exists($this->directoryList->getPath('media').'/'.$this->getAdminImageDir($itemId)); 
    }
    private function getAdminDirPath($itemId){
        return $this->directoryList->getPath('media').'/'.sprintf(self::ADMIN_DIR_PATH,$itemId);
    }
    private function getAdminImageWebUrl($itemId,$imageName){
        return $this->mediaUrl.sprintf(self::ADMIN_DIR_PATH,$itemId).'/'.$imageName;
    }

    public function getAdminItemOptionImages($itemId){
        $imgDir=$this->getAdminDirPath($itemId);
        if($this->fileDriver->isExists($imgDir) && $this->fileDriver->isDirectory($imgDir)){
            $images =$this->fileDriver->readDirectory($imgDir);
            $itemImages=[];
            if(!empty($images)){
                foreach($images as $img){
                    $itemImages[] =$this->getAdminImageWebUrl($itemId,basename($img)); 
                }
            }
            return $itemImages;
        }
        return [];
    }

    private function getCustomDesignDirPath($designId){
        return $this->directoryList->getPath('media').'/'.sprintf(self::CUSTOM_DESIGN_DIR_PATH,$designId);
    }

    public function saveCustomDesignFile($id,$content,$imgType){
        list($type, $data) = explode(';', $content);
        list(, $data)      = explode(',', $data);
        if($this->directory->writeFile($this->getCustomDesignImageDir($id,$imgType),base64_decode($data))){
            return $this->getCustomDesignImageDir($id,$imgType);
        }
        return false;
    }
    public function getCustomDesignImageDir($id,$imgType='0'){
        return sprintf(self::CUSTOM_IMAGE_DIR_PATH,$id,$imgType);
    }

    private function getCustomDesignImageWebUrl($itemId,$imageName){
        return $this->mediaUrl.sprintf(self::CUSTOM_DESIGN_DIR_PATH,$itemId).'/'.$imageName;
    }

    public function getCustomDesignImages($designId){
        $imgDir=$this->getCustomDesignDirPath($designId);

        if($this->fileDriver->isExists($imgDir) && $this->fileDriver->isDirectory($imgDir)){
            $images =$this->fileDriver->readDirectory($imgDir);
            $itemImages=[];
            if(!empty($images)){
                foreach($images as $img){
                    $itemImages[] =$this->getCustomDesignImageWebUrl($designId,basename($img)); 
                }
            }
            return $itemImages;
        }
        return [];
    }

    public function removeDesignFileDirectory($designId){

        $dirname=$this->getCustomDesignDirPath($designId);

        if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                    unlink($dirname."/".$file);
                else
                    $this->removeDesignFileDirectory($dirname.'/'.$file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;

    }

    public function html2rgb($color)
    {
        if ($color[0] == '#')
            $color = substr($color, 1);

        if (strlen($color) == 6)
            list($r, $g, $b) = array($color[0].$color[1],
                $color[2].$color[3],
                $color[4].$color[5]);
        elseif (strlen($color) == 3)
            list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
        else
            return false;

        $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

        return array($r, $g, $b);
    }

    public function saveQuoteItemPDF($QuoteId,$itemId){

        try {
            $quoteItemDirectory = $this->directory->getAbsolutePath($this->getQuoteItemDir($QuoteId,$itemId));

            $svgImagesDirctory = $quoteItemDirectory.'/Png';

            if(is_dir($svgImagesDirctory)){
                $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, "in", PDF_PAGE_FORMAT, true, 'UTF-8', false);

                $pdf->setPrintHeader(false);
                $pdf->setPrintFooter(false);
                $pdf->SetMargins(false, false, false);
                $pdf->SetHeaderMargin(false);
                $pdf->SetFooterMargin(false);
                $pdf->SetAutoPageBreak(false, 0);

                $imageRatio = 72;
                $svgImages = glob($svgImagesDirctory . "/*.png");

                foreach($svgImages as $key => $svgImage){

                    /*$svgfile = simplexml_load_file($svgImage);
                    $xmlattributes = $svgfile->attributes();
                    $width = (string) $xmlattributes->width; 
                    $height = (string) $xmlattributes->height;*/

                    list($width, $height, $type, $attr) = getimagesize($svgImage); 

                    $imageWidth = $width / $imageRatio;
                    $imageHeight = $height / $imageRatio;

                    $custom_layout = array($imageWidth, $imageHeight );  

                    if($imageWidth >= $imageHeight){
                        $pageLayout = 'L';
                    }
                    else{
                        $pageLayout = 'P';
                    }

                    $pdf->AddPage($pageLayout,$custom_layout);

                    /*if($svgfile->rect){
                    $rectColor = (string) $svgfile->rect->attributes()->fill;
                    if($rectColor){
                    $pdf->Rect(0, 0, $imageWidth, $imageHeight, 'DF', "", $this->html2rgb($rectColor));
                    }
                    }*/

                    /*$pdf->ImageSVG($file=$svgImage, $x=0, $y=0, $w=$imageWidth, $h=$imageHeight, $link='', $align='', $palign='', $border=0, $fitonpage=true);*/

                    $pdf->Image($file=$svgImage, $x=0, $y=0, $w=$imageWidth, $h=$imageHeight, $link='', $align='', $palign='', $border=0, $fitonpage=true);

                }

                $pdfDir = $this->directory->getAbsolutePath($this->getQuoteItemPDFDir($QuoteId,$itemId));
                $pdfItemDir = $pdfDir.'/'.$itemId.'.pdf';
                if (!is_dir($pdfDir)) {
                    $ioAdapter = $this->_file;
                    $ioAdapter->mkdir($pdfDir, 0775);
                }
                $pdf->Output($pdfItemDir, 'F');
            }
        } catch (\Exception $e) {

        }

    }

    public function saveQuoteItemFullImagePDF($QuoteId,$itemId,$productMainImages,$personalizationProductColor){

        try {

            $data = $this->serializer->unserialize($productMainImages);
            $quoteItemDirectory = $this->directory->getAbsolutePath($this->getQuoteItemDir($QuoteId,$itemId));

            $svgImagesDirctory = $quoteItemDirectory.'/FullImages';

            if(is_dir($svgImagesDirctory)){

                $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, "in", PDF_PAGE_FORMAT, true, 'UTF-8', false);

                $pdf->setPrintHeader(false);
                $pdf->setPrintFooter(false);
                $pdf->SetMargins(false, false, false);
                $pdf->SetHeaderMargin(false);
                $pdf->SetFooterMargin(false);
                $pdf->SetAutoPageBreak(false, 0);

                $imageRatio = 96;
                $svgImages = glob($svgImagesDirctory . "/*.png");

                foreach($svgImages as $key => $svgImage){

                    $dataImage = $data[$key];

                    unset($dataImage['canvasJson']); 

                    $svgImageHeight = $dataImage['height'] / $imageRatio;
                    $svgImageWidth = $dataImage['width'] / $imageRatio;
                    $svgImageTop = $dataImage['top'] / $imageRatio;
                    $svgImageLeft = $dataImage['left'] / $imageRatio;

                    list($width, $height, $type, $attr) = getimagesize($dataImage['image']); 

                    $imageWidth = $width / $imageRatio;
                    $imageHeight = $height / $imageRatio;

                    $custom_layout = array($imageWidth, $imageHeight );  

                    if($imageWidth >= $imageHeight){
                        $pageLayout = 'L';
                    }
                    else{
                        $pageLayout = 'P';
                    }

                    $pdf->AddPage($pageLayout,$custom_layout);

                   if($personalizationProductColor){
                        $pdf->Rect(0, 0, $imageWidth, $imageHeight, 'DF', "", $this->html2rgb($personalizationProductColor));
                    }

                    $pdfHtml = '
                    <img src="'.$data[$key]['image'].'"> 
                    ';

                    $pdf->writeHTML($pdfHtml, true, false, false, false, '');


                    /*$pdf->Image($file=$svgImage, $x=0, $y=0, $w=$imageWidth, $h=$imageHeight, $link='', $align='', $palign='', $border=0, $fitonpage=true);*/

                    $pdf->Image($file=$svgImage, $x=0, $y=0, $w=$imageWidth, $h=$imageHeight, $link='', $align='', $palign='', $border=0, $fitonpage=true);

                }

                $pdfDir = $this->directory->getAbsolutePath($this->getQuoteItemPDFDir($QuoteId,$itemId));
                $pdfItemDir = $pdfDir.'/'.$itemId.'_fullImages.pdf';
                if (!is_dir($pdfDir)) {
                    $ioAdapter = $this->_file;
                    $ioAdapter->mkdir($pdfDir, 0775);
                }
                $pdf->Output($pdfItemDir, 'F');
            }
        }
        catch (\Exception $e) {

        }

    }

    public function getQuoteItemDir($QuoteTd,$itemId){
        return sprintf(self::QUOTE_ITEM_DIR_PATH,$QuoteTd,$itemId);
    }

    public function getQuoteItemPDFDir($QuoteTd,$itemId){
        return sprintf(self::QUOTE_PDF_DIR_PATH,$QuoteTd,$itemId);
    }


    public function saveTemplateProductImage($content,$extensionType){
        list($type, $data) = explode(';', $content);
        list(, $data)      = explode(',', $data);
        if($this->directory->writeFile($this->getQuoteImageDir($QuoteId,$id,$imgType),base64_decode($data))){
            return $this->getQuoteImageDir($QuoteId,$id,$imgType);
        }
        return false;
    }

}