<?php

/** Setubridge Technolabs
 * http://www.setubridge.com/
 * @author SetuBridge
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 **/

namespace SetuBridge\Personalization\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\ProductRepository;

class Editor extends \Magento\Framework\App\Action\Action
{
    public $productRepository;
    public $helperData;

    public function __construct(
        Context $context,
        ProductRepository $productRepository,
        \SetuBridge\Personalization\Helper\Data $helperData
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->helperData = $helperData;
    }
    public function execute()
    {

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        $productId = $this->getRequest()->getParam('product');
        if ($productId) {
            try {
                $product = $this->productRepository->getById($productId);
                if ($product && $product->getPersonalization() && $this->helperData->checkedCustomization($product)) {
                    $this->_view->loadLayout();
                    $title = __('Product Designer');
                    $this->_view->getPage()->getConfig()->getTitle()->set($title);
                    $this->_view->renderLayout();
                } else {
                    $this->messageManager->addErrorMessage("Attention: Something went wrong.");
                    return $resultRedirect;
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage("Attention: Something went wrong.");
                return $resultRedirect;
            }
        } else {
            $this->messageManager->addErrorMessage("Attention: Something went wrong.");
            return $resultRedirect;
        }
    }
}
