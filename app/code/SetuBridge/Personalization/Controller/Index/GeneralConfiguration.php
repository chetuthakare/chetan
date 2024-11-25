<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
class GeneralConfiguration extends \Magento\Framework\App\Action\Action
{

    public function __construct(
        Context $context,
        \SetuBridge\Personalization\Helper\Data $helperData
    ) 
    {
        parent::__construct($context);
        $this->helperData = $helperData;

    }

    public function execute()
    {    
        $jsonFactory=$this->resultFactory->create(ResultFactory::TYPE_JSON);
        $jsonFactory->setData(['clipart_active' => $this->helperData->getClipartActive(),'pattern_active'=>$this->helperData->getPatternActive(),'quote_active'=>$this->helperData->getQuoteActive(),'font_active'=>$this->helperData->getFontActive(),'price_art'=>$this->helperData->getPricePerArt(),'price_text'=>$this->helperData->getPricePerText(),'price_quote'=>$this->helperData->getPricePerQuote(),'price_pattern'=>$this->helperData->getPricePerPattern()]);
        return $jsonFactory;
    }

} 

