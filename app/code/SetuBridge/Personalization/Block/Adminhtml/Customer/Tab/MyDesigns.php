<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/ 
namespace SetuBridge\Personalization\Block\Adminhtml\Customer\Tab;

class MyDesigns extends \Magento\Backend\Block\Widget\Grid\Extended
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \SetuBridge\Personalization\Model\SaveCustomerProductFactory $saveCustomerCollectionFactory,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationTemplate $attachModel,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->saveCustomerCollectionFactory = $saveCustomerCollectionFactory;
        $this->attachModel = $attachModel;
        parent::__construct($context, $backendHelper, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('customer_save_id');
        $this->setDefaultSort('customer_save_id', 'desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
    }


    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('customer_save_id');
        $this->getMassactionBlock()->setFormFieldName('selected');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('personalization/customer/DeletesMyDesigns',['id'=>$this->getRequest()->getParam('id')]),
                'confirm' => __('Are you sure?')
            ]
        );

        return $this;
    }

    public function _prepareCollection()
    {
        $collection = $this->saveCustomerCollectionFactory->create()->getCollection();
        $collection->addFieldToFilter('customer_id',$this->getRequest()->getParam('id'));
        $collection->addFieldToSelect('customer_save_id');
        $collection->addFieldToSelect('title');
        $collection->addFieldToSelect('created_at');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function _prepareColumns()
    {
        $model = $this->attachModel;

        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'customer_save_id',
            [
                'header' => __('Images'),
                'index' => 'customer_save_id',
                'class' => 'mydesigns-images',
                'renderer'  => '\SetuBridge\Personalization\Block\Adminhtml\Customer\Grid\Renderer\Image',
            ]
        );
        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return '';
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return true;
    }
}
