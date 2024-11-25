<?php
namespace Swissup\Pagespeed\Model\Config\Backend\File;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Dir;

class Rjs extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    private $moduleReader;

    /**
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    private $readFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->moduleReader = $moduleReader;
        $this->readFactory = $readFactory;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     *
     * @inherit
     */
    protected function _afterLoad()
    {
        $value = (string) $this->getValue();
        if (empty($value)) {
            $etcPath = $this->moduleReader->getModuleDir(
                Dir::MODULE_ETC_DIR,
                'Swissup_Pagespeed'
            );
            $reader = $this->readFactory->create($etcPath);
            $filename = 'rjs.json';
            if ($reader->isExist($filename) &&
                $reader->isFile($filename) &&
                $reader->isReadable($filename)
            ) {
                $value = $reader->readFile($filename);
                if (!empty($value)) {
                    $this->setValue($value);
                }
            }
        }
        return $this;
    }
}
