<?php
namespace Swissup\Pagespeed\Image\Adapter;

class ImageMagick extends \Magento\Framework\Image\Adapter\ImageMagick
{
    use OptimizerTrait;

    /**
     * Save image to specific path.
     * If some folders of path does not exist they will be created
     *
     * @param null|string $destination
     * @param null|string $newName
     * @return void
     * @throws \Exception  If destination path is not writable
     */
    public function save($destination = null, $newName = null)
    {
        parent::save($destination, $newName);

        $filename = $this->_prepareDestination($destination, $newName);
        if (!empty($filename)) {
            $this->optimize($filename);
        }
    }
}
