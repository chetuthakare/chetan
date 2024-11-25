<?php
declare(strict_types=1);

namespace Swissup\Pagespeed\Model\Image;

use Magento\Framework\Image;
use Magento\Framework\Image\Factory as ImageFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Resizer
{
    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var string
     */
    private $origin;

    /**
     * @var string
     */
    private $destination;

    /**
     * @var array
     */
    private $params = [];

    public function __construct(ImageFactory $imageFactory)
    {
        $this->imageFactory = $imageFactory;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setOrigin($path)
    {
        $this->origin = $path;
        return $this;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setDestination($path)
    {
        $this->destination = $path;
        return $this;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $params = $this->params;
        $image = $this->makeImage($this->origin, $params);
        if (!empty($params['image_width']) && !empty($params['image_height'])) {
            $image->resize($params['image_width'], $params['image_height']);
        }

        $image->save($this->destination);
        unset($image);
    }

    /**
     * Make image
     * @param string $origin
     * @param array $params
     * @return Image
     */
    private function makeImage(string $origin, array $params): Image
    {
        $image = $this->imageFactory->create($origin);
        $image->keepAspectRatio($params['keep_aspect_ratio']);
        $image->keepFrame($params['keep_frame']);
        $image->keepTransparency($params['keep_transparency']);
        $image->constrainOnly($params['constrain_only']);
        $image->backgroundColor($params['background']);
        $image->quality($params['quality']);
        return $image;
    }
}
