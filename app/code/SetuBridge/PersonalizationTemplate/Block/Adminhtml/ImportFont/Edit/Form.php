<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\PersonalizationTemplate\Block\Adminhtml\ImportFont\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_systemStore;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        
        array $data = []
    ) 
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
    
        $form = $this->_formFactory->create(
            ['data' => [
                'id' => 'edit_form', 
                'enctype' => 'multipart/form-data', 
                'action' => $this->getData('action'), 
                'method' => 'post'
                ]
            ]
        );

        $form->setHtmlIdPrefix('personalization_template_importfont_');

        $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __(''), 'class' => 'fieldset-wide']
            );

        $fieldset->addField(
            'csv',
            'file',
            [
                'name' => 'csv',
                'label' => __('Import'),
                'id' => 'csv',
                'title' => __('Import Fonts'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        
        $fieldset->addField(
            'file_directory',
            'text',
            [
                'name' => 'file_directory',
                'label' => __('Images File Directory'),
                'id' => 'file_directory',
                'value' => 'pub/media/Personalization/import',
                'title' => __('Images File Directory'),
                'after_element_html' => '<small>For Type "Local Server" use relative path to Magento installation, e.g. pub/media/export, pub/media/import, pub/media/import/some/dir</small>',
                'required' => false,
            ]
        );
        
        $fieldset->addType('csvfile', '\SetuBridge\PersonalizationTemplate\Block\Adminhtml\ImportFont\Csvfile');

        $fieldset->addField(
            'file',
            'csvfile',
            [
                'name'  => 'csvfile',
                'label' => __('csvfile'),
                'title' => __('csvfile')
            ]
        );


        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}