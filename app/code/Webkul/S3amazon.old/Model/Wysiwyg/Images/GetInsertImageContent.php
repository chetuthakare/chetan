<?php declare(strict_types=1);
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_S3amazon
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software protected Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\S3amazon\Model\Wysiwyg\Images;

use Webkul\S3amazon\Helper\Data;
use Magento\Cms\Helper\Wysiwyg\Images as ImagesHelper;
use Magento\Catalog\Helper\Data as CatalogHelper;

/**
 * @inheritdoc
 */
class GetInsertImageContent
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var S3Storage
     */
    protected $s3storage;

    /**
     * @var ImagesHelper
     */
    protected $imagesHelper;

    /**
     * @var CatalogHelper
     */
    protected $catalogHelper;

    /**
     * @param Data $helper
     * @param ImagesHelper $imagesHelper
     * @param CatalogHelper $catalogHelper
     * @param \Webkul\S3amazon\Model\MediaStorage\File\Storage\S3storage $s3storage
     */
    public function __construct(
        Data $helper,
        ImagesHelper $imagesHelper,
        CatalogHelper $catalogHelper,
        \Webkul\S3amazon\Model\MediaStorage\File\Storage\S3storage $s3storage
    ) {
        $this->helper = $helper;
        $this->s3storage = $s3storage;
        $this->imagesHelper = $imagesHelper;
        $this->catalogHelper = $catalogHelper;
    }

    /**
     * @inheritdoc
     */
    public function aroundExecute(
        \Magento\Cms\Model\Wysiwyg\Images\GetInsertImageContent $subject,
        callable $proceed,
        string $encodedFilename,
        bool $forceStaticPath,
        bool $renderAsTag,
        int $storeId = 0
    ) {
        if ($this->helper->getIsEnable()) {
            $filename = $this->imagesHelper->idDecode($encodedFilename);
            $this->catalogHelper->setStoreId($storeId);
            $this->imagesHelper->setStoreId($storeId);
            $currentUrl = $this->imagesHelper->getCurrentUrl();
            $currentPath = $this->imagesHelper->getCurrentPath();
            $pos = 10 +strrpos($currentPath, "pub/media/");
            $folderName = substr($currentPath, $pos);
            $this->s3storage->saveFile($folderName.'/'.$filename);
            return $this->imagesHelper->getImageHtmlDeclaration($filename, $renderAsTag);
        } else {
            $returnValue = $proceed($encodedFilename, $forceStaticPath, $renderAsTag, $storeId);
            return $returnValue;
        }
    }
}
