<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Block\Adminhtml\Grid\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_systemStore;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \SetuBridge\PersonalizationTemplate\Model\Status $options,
        \Magento\Framework\Filesystem $filesystem,
        array $data = []
    ) 
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_options = $options;
        $this->_filesystem = $filesystem;
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

        $form->setHtmlIdPrefix('personalization_template_grid_');
        if ($model->getEntityId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('General Information'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
            $model->setData('edit_image_path', $this->getImagePath());
        } else {
            $model->setData('unique_id', Date("Ymdhis"));
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('General Information'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Name'),
                'id' => 'title',
                'title' => __('Name'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $id = $this->getRequest()->getParam('id');
        if($id){
            $fieldset->addField('edit_image_path', 'hidden',
                array(
                    'name'      => 'edit_image_path',
                    'label'     => 'edit_image_path',
                    'class'     => 'image-path',
                    'required'  => false,
                    'value' => $this->getImagePath(),
                    'readonly'  => true
            ));
        }

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $fieldset->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Status'),
                'id' => 'is_active',
                'title' => __('Status'),
                'values' => $this->_options->getOptionArray(),
                'class' => 'status',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'unique_id',
            'hidden',
            [
                'name' => 'unique_id',
                'label' => __('Unique Id'),
                'id' => 'unique_id',
                'title' => __('Unique Id')
            ]
        );
        
        $fieldset->addField(
            'customization_template',
            'button',
            [
                'name' => 'customization_template',
                'label' => __(''),
                'id' => 'customization_template',
                'value' => 'Customization Template',
                'title' => __('Start Designing')
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();

    }
}