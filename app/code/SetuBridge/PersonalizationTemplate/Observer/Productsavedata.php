<?php

/** Setubridge Technolabs
 * http://www.setubridge.com/
 * @author SetuBridge
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 **/

namespace SetuBridge\PersonalizationTemplate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Productsavedata implements ObserverInterface
{
    protected $messageManager;
    public $_request;
    public $model;
    public $filesystem;
    public $personalizeProduct;
    public $_storeManager;
    public $mediaDirectory;
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \SetuBridge\PersonalizationTemplate\Model\ProductEditpageData $model,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SetuBridge\Personalization\Model\PersonalizeProduct $personalizeProduct
    ) {
        $this->_request = $context->getRequest();
        $this->model = $model;
        $this->messageManager = $messageManager;
        $this->filesystem = $filesystem;
        $this->personalizeProduct = $personalizeProduct;
        $this->_storeManager = $storeManager;
        $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $_product = $observer->getProduct();
        try {
            $this->model->load($_product->getId(), 'product_id');
            if ($this->model->getEntityId()) {

                $Data = json_decode($this->model->getPersonalizationJsonData());

                if ($Data && $_product->getPersonalizationTemplate() == 0) {
                    $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

                    $images = $_product->getMediaGalleryImages();

                    foreach ($Data as $key => $row) {
                        if (strpos($row->image, 'product') !== false && strpos($row->image, 'catalog') !== false) {
                            $i = 0;

                            foreach ($images as $image) {

                                if ($key == $i) {

                                    $getMediaPathImagePos = strpos($image->getUrl(), "pub/media");

                                    if (!$getMediaPathImagePos) {
                                        $getMediaPathImagePos = strpos($image->getUrl(), "pub\media");
                                    }
                                    $productImgPath = substr($image->getUrl(), $getMediaPathImagePos + 10);

                                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                                    $uniqueIdImagePath = $mediaDirectory->getAbsolutePath($productImgPath);

                                    if (file_exists($uniqueIdImagePath)) {

                                        $subdirToSave = 'Personalization/productimage/' . $_product->getPersonalizationTemplate() . '/' . $_product->getId() . '/' . basename($image->getUrl());
                                        $result = $this->mediaDirectory->copyFile(
                                            $productImgPath,
                                            $subdirToSave
                                        );

                                        if (!$result) {
                                            $this->messageManager->addError("Attention: Something went wrong. for saved personlization template data!");
                                        } else {
                                            $row->image = $mediaUrl . 'Personalization/productimage/' . $_product->getPersonalizationTemplate() . '/' . $_product->getId() . '/' . basename($image->getUrl());
                                        }
                                    }
                                }
                                $i++;
                            }
                        }
                    }

                    $this->model->setData('personalization_json_data', json_encode($Data));
                }



                if ($_product->getPersonalizationTemplate() != 0 && $this->model->getTemplateId() != 0 && $_product->getPersonalizationTemplate() == $this->model->getTemplateId()) {

                    $_product->setData('personalization_template', 0);
                    $_product->save();

                    $this->model->setData('template_id', 0);
                }



                //$this->model->setData('template_id',$_product->getPersonalizationTemplate());
                $_product->setData('personalization_json_data', null);
            } else if ($this->_request->getParam('product')['personalization_json_data']) {

                $this->model->load($this->_request->getParam('product')['personalization_json_data'], 'uniqid');
                $Data = $this->model->getPersonalizationJsonData();
                $uniqId = $this->_request->getParam('product')['personalization_json_data'];

                $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

                if ($Data) {
                    $Data = json_decode($Data);
                    $images = $_product->getMediaGalleryImages();

                    foreach ($Data as $key => $row) {
                        if (strpos($row->image, 'product') !== false && strpos($row->image, 'catalog') !== false) {
                            $i = 0;

                            foreach ($images as $image) {

                                if ($key == $i) {

                                    $getMediaPathImagePos = strpos($image->getUrl(), "pub/media");

                                    if (!$getMediaPathImagePos) {
                                        $getMediaPathImagePos = strpos($image->getUrl(), "pub\media");
                                    }
                                    $productImgPath = substr($image->getUrl(), $getMediaPathImagePos + 10);

                                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                                    $uniqueIdImagePath = $mediaDirectory->getAbsolutePath($productImgPath);

                                    if (file_exists($uniqueIdImagePath)) {

                                        $subdirToSave = 'Personalization/productimage/' . $_product->getPersonalizationTemplate() . '/' . $_product->getId() . '/' . basename($image->getUrl());
                                        $result = $this->mediaDirectory->copyFile(
                                            $productImgPath,
                                            $subdirToSave
                                        );

                                        if (!$result) {
                                            $this->messageManager->addError("Attention: Something went wrong. for saved personlization template data!");
                                        } else {
                                            $row->image = $mediaUrl . 'Personalization/productimage/' . $_product->getPersonalizationTemplate() . '/' . $_product->getId() . '/' . basename($image->getUrl());
                                        }
                                    }
                                }
                                $i++;
                            }
                        }
                        if (strpos($row->image, $uniqId) !== false) {

                            $imageUrl = str_replace($uniqId, $_product->getId(), $row->image);
                            $row->image = $imageUrl;
                        }
                    }
                }

                if ($_product->getPersonalizationTemplate() != 0 && $this->model->getTemplateId() != 0 && $_product->getPersonalizationTemplate() == $this->model->getTemplateId()) {

                    $_product->setData('personalization_template', 0);
                    $_product->save();

                    $this->model->setData('template_id', 0);
                }

                $this->model->setData('personalization_json_data', json_encode($Data));
                $this->model->setData('product_id', $_product->getId());
                $_product->setData('personalization_json_data', null);


                if (isset($uniqId) && $uniqId) {

                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                    $uniqueIdImagePath = $mediaDirectory->getAbsolutePath('Personalization/productimage/' . $_product->getPersonalizationTemplate() . '/' . $uniqId);
                    if (file_exists($uniqueIdImagePath)) {
                        $movedPath = $mediaDirectory->getAbsolutePath('Personalization/productimage/' . $_product->getPersonalizationTemplate() . '/' . $_product->getId());

                        $status = $this->personalizeProduct->copyOneFolderToAnotherDeleteCopyFolder($uniqueIdImagePath, $movedPath);

                        if (!$status) {
                            $this->messageManager->addError("Attention: Something went wrong. for saved personlization template data!");
                        }
                    }
                }
            }

            $saveData = $this->model->save();
        } catch (\Exception $e) {

            $this->messageManager->addError("Attention: Something went wrong." . $e->getMessage());
        }
    }
}
