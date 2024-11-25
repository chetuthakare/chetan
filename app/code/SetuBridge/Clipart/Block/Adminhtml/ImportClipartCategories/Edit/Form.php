<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
?>
<?php
namespace SetuBridge\Clipart\Block\Adminhtml\ImportClipartCategories\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_systemStore;

    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \SetuBridge\Clipart\Model\ClipartCategoriesStatus $options,
        array $data = []
    ) {
        $this->_options = $options;
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

        $form->setHtmlIdPrefix('personalization_template_importclipartcategories_');

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
                'title' => __('Import'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        
        $fieldset->addType('csvfile', '\SetuBridge\Clipart\Block\Adminhtml\ImportClipartCategories\Csvfile');

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