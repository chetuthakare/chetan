<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Block\Form;

class Login extends \Magento\Framework\View\Element\Template
{
   
    protected $customerSession;

    
    protected $httpContext;

    
    protected $registration;

    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Registration $registration,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->registration = $registration;
    }

    
    public function getRegistration()
    {
        return $this->registration;
    }

    
    public function getPostActionUrl()
    {
        return $this->getUrl('personalization/customer/login');
    }

    
    public function isAutocompleteDisabled()
    {
        return (bool)!$this->_scopeConfig->getValue(
            \Magento\Customer\Model\Form::XML_PATH_ENABLE_AUTOCOMPLETE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    
    public function customerIsAlreadyLoggedIn()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    
    public function getCustomerRegistrationUrl()
    {
        return $this->getUrl('customer/account/create');
    }
}
