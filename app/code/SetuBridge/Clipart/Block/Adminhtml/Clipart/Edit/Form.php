<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Clipart\Block\Adminhtml\Clipart\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_systemStore;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \SetuBridge\Clipart\Model\Status $options,
        \SetuBridge\Clipart\Model\ClipartCategoryoptions $categoryoptions,
        array $data = []
    ) {
        $this->categoryoptions = $categoryoptions;
        $this->_options = $options;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('row_data');
        $form = $this->_formFactory->create(
            ['data' => [
                'id' => 'edit_form',
                'enctype' => 'multipart/form-data',
                'action' => $this->getData('action'),
                'method' => 'post'
                ]
            ]
        );

        $form->setHtmlIdPrefix('clipart_');
        if ($model->getEntityId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('General Information'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('clipart_id', 'hidden', ['name' => 'clipart_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('General Information'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'category',
            'select',
            [
                'name' => 'category',
                'label' => __('Category'),
                'id' => 'category',
                'title' => __('Category'),
                'values' => $this->categoryoptions->toOptionArray(),
                'class' => 'category',
                'required' => true,
        ]);

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $fieldset->addField(
            'clipart_image',
            'image',
            [
                'name' => 'clipart_image',
                'label' => __('Image'),
                'title' => __('Image'),
                'required'  => true

            ]
        )->setAfterElementHtml('
            <script>
            require([
            "jquery",
            ], function($){
            $(document).ready(function () {
            if($(".small-image-preview").length){
            $("#clipart_clipart_image").removeClass("required-file");
            }else{
            $("#clipart_clipart_image").addClass("required-file");
            }
            $( "#clipart_clipart_image" ).attr( "accept", "image/x-png,image/gif,image/jpeg,image/jpg,image/png" );
            });
            });
            </script>
        ');

        $fieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Position'),
                'id' => 'position',
                'title' => __('Position')
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'id' => 'status',
                'title' => __('Status'),
                'values' => $this->_options->getOptionArray(),
                'class' => 'status',
                'required' => true,
            ]
        );
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
