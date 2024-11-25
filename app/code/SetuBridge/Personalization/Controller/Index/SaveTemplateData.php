<?php

/** Setubridge Technolabs
 * http://www.setubridge.com/
 * @author SetuBridge
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 **/

namespace SetuBridge\Personalization\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Filesystem\DirectoryList;

class SaveTemplateData extends \Magento\Framework\App\Action\Action
{

    public $model;
    public $serializer;
    public $imageProcessor;
    public $filesystem;
    public $file;
    public $mediaDirectory;
    public $personalizeProduct;
    public $_helperData;
    public function __construct(
        Context $context,
        Json $serializer,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationTemplate $personalization,
        \SetuBridge\Personalization\Model\ImageProcessor $imageProcessor,
        \Magento\Framework\Filesystem $filesystem,
        \SetuBridge\Personalization\Model\PersonalizeProduct $personalizeProduct,
        \Magento\Framework\Filesystem\Io\File $file,
        \SetuBridge\Personalization\Helper\Data $helperData
    ) {
        parent::__construct($context);
        $this->model = $personalization;
        $this->serializer = $serializer;
        $this->imageProcessor = $imageProcessor;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->personalizeProduct = $personalizeProduct;
        $this->_helperData = $helperData;
    }

    public function execute()
    {
        try {
            $template_data = $this->getRequest()->getPostValue('template_data');
            $uniqId = $this->getRequest()->getPostValue('uniqId');
            $templateID = $this->getRequest()->getPostValue('templateID');
            $jsonFactory = $this->resultFactory->create(ResultFactory::TYPE_JSON);

            if (!$templateID) {
                if ($uniqId) {
                    $this->model->load($uniqId, 'unique_id');
                    if ($this->model->getEntityId()) {
                        $this->model->setPersonalizationJsonData($template_data);
                    } else {
                        $this->model->setPersonalizationJsonData($template_data);
                        $this->model->setUniqueId($uniqId);
                    }
                } else {
                    $uniqId = Date("Ymdhis");
                    $this->model->setPersonalizationJsonData($template_data);
                    $this->model->setUniqueId($uniqId);
                }
            } else {
                $this->model->load($templateID, 'entity_id');
                if ($this->model->getEntityId()) {
                    $this->model->setPersonalizationJsonData($template_data);
                } else {
                    $this->model->setPersonalizationJsonData($template_data);
                    $this->model->setEntityId($templateID);
                }
            }

            $saveData = $this->model->save();

            if ($uniqId) {
                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $uniqueIdImagePath = $mediaDirectory->getAbsolutePath('Personalization/productimage/' . $uniqId);

                if ($this->isFileExist($uniqueIdImagePath)) {

                    $movedPath = $mediaDirectory->getAbsolutePath('Personalization/productimage/' . $saveData->getEntityId());

                    $this->file->checkAndCreateFolder($movedPath);

                    if (is_dir($uniqueIdImagePath) && !$this->personalizeProduct->dir_is_empty($uniqueIdImagePath)) {
                        $this->recurse_copy(
                            $uniqueIdImagePath,
                            $movedPath
                        );

                        $this->personalizeProduct->removeDirectory($uniqueIdImagePath);

                        $setTemplateData = $this->serializer->unserialize(($saveData->getPersonalizationJsonData()));
                        foreach ($setTemplateData as $key => $data) {
                            if (array_key_exists("image", $data)) {
                                $template_new_image =  str_replace($uniqId, $saveData->getEntityId(), $data['image']);
                                $setTemplateData[$key]['image'] = $template_new_image;
                            }
                        }
                        $this->model->load($uniqId, 'unique_id');
                        if ($this->model->getEntityId()) {
                            $this->model->setPersonalizationJsonData($this->serializer->serialize($setTemplateData));
                        }
                        $this->model->save();
                    }
                }
            }

            if ($saveData) {
                $jsonFactory->setData(['uniqId' => $uniqId, 'error' => false]);
                return $jsonFactory;
            } else {
                $jsonFactory->setData(['error' => true]);
                return $jsonFactory;
            }
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }
    }

    private function isFileExist($quotePath)
    {
        return file_exists($quotePath);
    }

    private function recurse_copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
