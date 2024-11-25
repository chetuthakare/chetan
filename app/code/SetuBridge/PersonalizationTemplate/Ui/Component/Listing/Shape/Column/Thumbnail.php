<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Ui\Component\Listing\Shape\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'thumbnail';

    const ALT_FIELD = 'name';


    private $_getModel;
    
    private $editUrl;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationShape\Image $imageHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $filename = $item['shape_image'];
                $item[$fieldName . '_src'] = $this->imageHelper->getBaseUrl().$filename;
                $item[$fieldName . '_orig_src'] = $this->imageHelper->getBaseUrl().$filename;
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'personalization/shape/addshape',
                    ['id' => $item['shape_id'], 'store' => $this->context->getRequestParam('store')]
                );
            }
        }
        return $dataSource;
    }
    protected function getAlt($row)
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }
}