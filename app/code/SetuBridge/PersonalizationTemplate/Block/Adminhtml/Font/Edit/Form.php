<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Block\Adminhtml\Font\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_systemStore;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \SetuBridge\PersonalizationTemplate\Model\FontStatus $options,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationFontFactory $fontFactory,
        array $data = []
    ) 
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_options = $options;
        $this->personalizationFontFactory = $fontFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('font_data');

        $form = $this->_formFactory->create(
            ['data' => [
                'id' => 'edit_form', 
                'enctype' => 'multipart/form-data', 
                'action' => $this->getData('action'), 
                'method' => 'post'
                ]
            ]
        );

        $form->setHtmlIdPrefix('personalization_template_font_');

        if ($this->getRequest()->getParam('id')) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('General Information'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('font_id', 'hidden', ['name' => 'font_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('General Information'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'font_name',
            'text',
            [
                'name' => 'font_name',
                'label' => __('Name'),
                'id' => 'font_name',
                'title' => __('Name'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        if($this->isFontFileExist()){
            $fieldset->addField(
                'font_data',
                'hidden',
                [
                    'name' => 'font_data',
                ]
            );   
        }

        $fieldset->addField(
            'font_file',
            'file',
            [
                'name' => 'font_file',
                'label' => __('File'),
                'title' => __('File'),
                'after_element_html' => '<br><small>Suppoted ex. files : otf,ttf , eot , woff, woff2, fon, fnt</small>'
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

    public function isFontFileExist()
    {
        $id = $this->getRequest()->getParam('id');
        $personalizationFont   = $this->personalizationFontFactory->create();
        if ($id) {
            $personalizationFont->load($id);
            if($personalizationFont->getFontFile()){
                return true;
            }
        }
        return false;
    }
}