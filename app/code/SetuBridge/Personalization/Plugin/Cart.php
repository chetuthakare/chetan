<?php

/** Setubridge Technolabs
 * http://www.setubridge.com/
 * @author SetuBridge
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 **/

namespace SetuBridge\Personalization\Plugin;

use Magento\Framework\DataObject;

class Cart
{

    protected $personalizeProduct;
    public $_request;
    public $serializer;

    public function __construct(
        \SetuBridge\Personalization\Model\PersonalizeProduct $personalizeProduct,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->personalizeProduct = $personalizeProduct;
        $this->_request = $request;
        $this->serializer = $serializer;
    }

    public function beforeAddProduct(\Magento\Checkout\Model\Cart $subject, $productInfo, $requestInfo = null)
    {



        if ($this->_request->getFullActionName() == "sales_order_reorder") {

            if ($requestInfo instanceof \Magento\Framework\DataObject) {
                $request = $requestInfo;
            } elseif (is_numeric($requestInfo)) {
                $request = new \Magento\Framework\DataObject(['qty' => $requestInfo]);
            } elseif (is_array($requestInfo)) {
                $request = new \Magento\Framework\DataObject($requestInfo);
            }

            if ($request) {
                $salesOrderItemId = $request->getSalesOrderItemId();
                $salesOrderId = $request->getSalesOrderId();
                if ($salesOrderId && $salesOrderItemId) {
                    $this->personalizeProduct->setSessionPersonalizationReorderOrderId($request->getSalesOrderId());
                    $this->personalizeProduct->setSessionPersonalizationReorderItemId($request->getSalesOrderItemId());


                    $additionalPrice = $request->getAdditionalPrice();

                    if ($additionalPrice) {
                        $this->personalizeProduct->setOptionsPrice($additionalPrice);
                    }

                    $optionColorValue = $request->getPersonalizationProductColor();
                    if ($optionColorValue) {
                        if ($this->personalizeProduct->getColorNameByCode($optionColorValue)) {
                            $options['Color'] = $this->personalizeProduct->getColorNameByCode($optionColorValue);
                        }
                    }

                    $optionValue = $request->getPersonlizationOptions();
                    if ($optionValue) {
                        $options['Comment'] = $optionValue;
                    }

                    $productOptions = [];
                    if (!empty($options)) {
                        foreach ($options as $label => $value) {
                            $productOptions[] = ['label' => $label, 'value' => $value];
                        }
                        $productInfo->addCustomOption('additional_options', $this->serializer->serialize($productOptions));
                    }
                }
            }
        }
    }

    public function afterAddProduct(\Magento\Checkout\Model\Cart $subject, $result)
    {
        if ($this->_request->getFullActionName() == "sales_order_reorder") {
            if ($this->personalizeProduct->getSessionPersonalizationReorderItemId()) {
                $this->personalizeProduct->unsSessionPersonalizationReorderOrderId();
                $this->personalizeProduct->unsSessionPersonalizationReorderItemId();
            }
        }
        return $result;
    }
}
