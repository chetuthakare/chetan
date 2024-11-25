<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\TemplateDesigns\Block\Adminhtml\TemplateDesigns\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_systemStore;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \SetuBridge\TemplateDesigns\Model\TemplateDesignsStatus $options,
        array $data = []
    ) 
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_options = $options;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('templatedesigns_data');

        $form = $this->_formFactory->create(
            ['data' => [
                'id' => 'edit_form', 
                'enctype' => 'multipart/form-data', 
                'action' => $this->getData('action'), 
                'method' => 'post'
                ]
            ]
        );

        $form->setHtmlIdPrefix('templatedesigns_template_templatedesigns_');

        if ($model->getTemplateDesignsId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('General Information'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('designs_id', 'hidden', ['name' => 'designs_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('General Information'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'designs_name',
            'text',
            [
                'name' => 'designs_name',
                'label' => __('Name'),
                'id' => 'templatedesigns_name',
                'title' => __('Name'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $fieldset->addField(
            'designs_image',
            'image',
            [
                'name' => 'designs_image',
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
            $("#templatedesigns_template_templatedesigns_designs_image").removeClass("required-file");
            }else{
            $("#templatedesigns_template_templatedesigns_designs_image").addClass("required-file");
            }
            $( "#templatedesigns_template_templatedesigns_designs_image" ).attr( "accept", "image/x-png,image/gif,image/jpeg,image/jpg,image/png" );
            });
            });
            </script>
        ');

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