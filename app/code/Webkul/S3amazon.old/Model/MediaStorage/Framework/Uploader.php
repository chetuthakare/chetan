<?php declare(strict_types=1);
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_S3amazon
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\S3amazon\Model\MediaStorage\Framework;

use Webkul\S3amazon\Helper\Data;

/**
 * @inheritdoc
 */
class Uploader
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var S3Storage
     */
    private $s3storage;

    /**
     * @var \Magento\MediaStorage\Helper\File\Media
     */
    private $_mediaHelper;

    /**
     * @param Data $helper
     * @param \Webkul\S3amazon\Model\MediaStorage\File\Storage\S3storage $s3storage
     * @param \Magento\MediaStorage\Helper\File\Media $mediaHelper
     */
    public function __construct(
        Data $helper,
        \Webkul\S3amazon\Model\MediaStorage\File\Storage\S3storage $s3storage,
        \Magento\MediaStorage\Helper\File\Media $mediaHelper
    ) {
        $this->helper = $helper;
        $this->s3storage = $s3storage;
        $this->_mediaHelper = $mediaHelper;
    }

    /**
     * After Save
     *
     * @param Magento\Framework\File\Uploader $subject
     * @param string $result
     * @return mixed
     */
    public function afterSave($subject, $result)
    {
        if ($this->helper->checkMediaStorageIsS3()) {
            $client = $this->helper->getClient();
            $bucket = $this->helper->getConfigValue('s3_amazon/general_settings/bucket');
            if (!empty($result) && !empty($result['path']) && !empty($result['file'])) {
                $filePath = $result['path'].'/'.$result['file'];
                if (strpos($filePath, 'pub/media') !== false) {
                    $key = substr($filePath, strpos($filePath, 'pub/media/')+strlen('pub/media/'));
                    if ($this->s3storage->fileExists($key)) {
                        $result['file'] = '/'.$result['file'];
                        return $result;
                    }
                }
                $file = $this->_mediaHelper->collectFileInfo($this->s3storage->getMediaBaseDirectory(), $key);
                $cachingInfo = $this->helper->getFileCacheInfo();
                $ext = $this->helper->getFileExtension($result['file']);
                $time = Data::DEFAULT_CACHE_TIME;
                if (!empty($cachingInfo[$ext])) {
                    $time = $cachingInfo[$ext];
                } else {
                    $time = $cachingInfo['other'];
                }

                $uploadResult = $client->putObject(
                    [
                        'Body' => $file['content'],
                        'Bucket' => $bucket,
                        'ContentType' => \GuzzleHttp\Psr7\MimeType::fromFilename($file['filename']),
                        'Key' => $key,
                        'ACL'          => 'public-read',
                        'CacheControl' => "max-age={$time}"
                    ]
                );

                $result['file'] = '/'.$result['file'];
            }
        }
        return $result;
    }
}
