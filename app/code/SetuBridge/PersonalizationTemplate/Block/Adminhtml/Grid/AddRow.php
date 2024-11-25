<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Block\Adminhtml\Grid;

class AddRow extends \Magento\Backend\Block\Widget\Form\Container
{

    protected $_coreRegistry = null;


    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) 
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'row_id';
        $this->_blockGroup = 'SetuBridge_PersonalizationTemplate';
        $this->_controller = 'adminhtml_grid';
        parent::_construct();
        $this->addButton(
            'saveAndContinueEdit',
            [
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ]
                ] ,
                'class' => 'Save and Continue Edit',
                'level' => -1
            ]
        );

        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->remove('reset');
    }

    public function getHeaderText()
    {
        return __('Add Template Data');
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }

        return $this->getUrl('*/*/save');
    }    
}