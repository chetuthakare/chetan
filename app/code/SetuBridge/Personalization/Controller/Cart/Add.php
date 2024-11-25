<?php

/** Setubridge Technolabs
 * http://www.setubridge.com/
 * @author SetuBridge
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 **/

namespace SetuBridge\Personalization\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Checkout\Controller\Cart
{
    protected $productRepository;

    public $storeManager;
    public $personalizeProduct;
    public $product;
    public $serializer;
    public $resolverInterface;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \SetuBridge\Personalization\Model\PersonalizeProduct $personalizeProduct,
        \Magento\Framework\Locale\ResolverInterface $resolverInterface
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->personalizeProduct = $personalizeProduct;
        $this->product = $product;
        $this->serializer = $serializer;
        $this->resolverInterface = $resolverInterface;
    }

    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('productid');
        if ($productId) {
            try {
                return $this->product->create()->load($productId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    public function execute()
    {

        $response = ['error' => true, 'message' => __('We can\'t add this item to your shopping cart right now.')];

        if ($this->_formKeyValidator->validate($this->getRequest())) {

            $parameters = $this->getRequest()->getParams();

            try {


                if (isset($parameters["options"])) {
                    $mutiOptions = $parameters["options"];
                    $this->personalizeProduct->setPersonalizationCartData($this->getRequest());
                    foreach ($mutiOptions as $optionKey => $options) {

                        if ($options) {
                            $product = $this->_initProduct();

                            if ($product) {

                                $params = $this->getRequest()->getParams();


                                if (isset($options["personalization_product_color"])) {
                                    $params['personalization_product_color'] = $options["personalization_product_color"];
                                }

                                if (isset($options["personalization_product_qty"])) {
                                    $params['qty'] = $options['personalization_product_qty'];
                                }

                                if (isset($params['qty'])) {

                                    $filter = new \Magento\Framework\Filter\LocalizedToNormalized(
                                        ['locale' => $this->resolverInterface->getLocale()]
                                    );
                                    $params['qty'] = $filter->filter($params['qty']);
                                } else {
                                    $params['qty'] = 1;
                                }

                                if (isset($options["personalization_product_color"])) {
                                    $this->personalizeProduct->registerPersonlizationOptions($product, $this->getRequest(), $options["personalization_product_color"]);
                                } else {
                                    $this->personalizeProduct->registerPersonlizationOptions($product, $this->getRequest());
                                }


                                $related = $this->getRequest()->getParam('related_product');
                                $additionalPrice = $this->getRequest()->getParam('additional_price');

                                if ($additionalPrice) {
                                    $this->personalizeProduct->setOptionsPrice($additionalPrice);
                                }
                                if (isset($options['personalize_image'])) {
                                    unset($options['personalize_image']);
                                }

                                if (isset($options['personalization_product_qty'])) {
                                    unset($options['personalization_product_qty']);
                                }

                                if (isset($params['personalize_image'])) {
                                    unset($params['personalize_image']);
                                }
                                if (isset($params['canvasAreaData'])) {
                                    unset($params['canvasAreaData']);
                                }
                                if (isset($params['backgroundColor'])) {
                                    unset($params['backgroundColor']);
                                }
                                if (isset($params['canvasAreaSvgData'])) {
                                    unset($params['canvasAreaSvgData']);
                                }
                                if (isset($params['fontFamilies'])) {
                                    unset($params['fontFamilies']);
                                }
                                if (isset($params['fullImageSvgData'])) {
                                    unset($params['fullImageSvgData']);
                                }
                                if (isset($params['exportjson'])) {
                                    unset($params['exportjson']);
                                }
                                if (isset($params['personlization_options'])) {
                                    unset($params['personlization_options']);
                                }
                                if (isset($params['productType'])) {
                                    unset($params['productType']);
                                }
                                if (isset($params['additional_price'])) {
                                    unset($params['additional_price']);
                                }

                                if (empty($params['options'])) {
                                    $product->setSkipCheckRequiredOption(true);
                                    $product->setHasOptions(false);
                                }


                                if (isset($options['personalization_product_color'])) {
                                    unset($options['personalization_product_color']);
                                }

                                if ($parameters['productType'] == 'configurable') {

                                    if (isset($params['options'])) {
                                        unset($params['options']);
                                    }

                                    $params['super_attribute'] = $options;
                                } else {
                                    if (empty($options)) {
                                        unset($params['options']);
                                    } else {
                                        $params['options'] = $options;
                                    }
                                }



                                if (!isset($params['product'])) {
                                    $params['product'] = $product->getid();
                                }

                                $product->setData("is_personalization_product", true);

                                $this->cart->addProduct($product, $params);
                                if (!empty($related)) {
                                    $this->cart->addProductsByIds(explode(',', $related));
                                }
                            }
                        }
                    }

                    $this->cart->save();

                    $this->personalizeProduct->unsPersonalizationCartData();
                    $this->personalizeProduct->unsPersonalizationSourceDataSavedId();

                    if (isset($product) && $product) {
                        $this->_eventManager->dispatch(
                            'checkout_cart_add_product_complete',
                            ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
                        );

                        if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                            if (!$this->cart->getQuote()->getHasError()) {
                                $response = ['price' => $additionalPrice, 'error' => false, 'message' => __('You added %1 to your shopping cart.', $product->getName()), 'redirect_url' => $this->_url->getUrl('checkout/cart')];

                                if ($product->getTypeId() == 'configurable') {
                                    $item = $this->getLastAddedItem();
                                    if ($item) {
                                        $response['itemId'] = __($item->getId());
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if (isset($product) && $product && !$product->getIsSalable()) {
                    $response['message'] = __('Requested quantity doesn\'t available for this product');
                }

                if ($this->personalizeProduct->getPersonalizationCartData()) {
                    $this->personalizeProduct->unsPersonalizationCartData();
                }
                $response['message'] = $e->getMessage();
            } catch (\Exception $e) {
                if ($this->personalizeProduct->getPersonalizationCartData()) {
                    $this->personalizeProduct->unsPersonalizationCartData();
                }
                $response['message'] = $e->getMessage();
            }
        }

        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $jsonResult->setData($response);
    }

    private function getLastAddedItem()
    {
        $max = 0;
        $lastItem = null;
        foreach ($this->cart->getQuote()->getAllItems() as $item) {
            if (!$item->getParentItemId()) {
                if ($item->getId() > $max) {
                    $max = $item->getId();
                    $lastItem = $item;
                }
            }
        }
        return $lastItem;
    }
}
