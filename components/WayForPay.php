<?php

namespace dench\cart\components;

use dench\cart\models\WfpLog;
use dench\cart\models\Order;
use WayForPay\SDK\Collection\ProductCollection;
use WayForPay\SDK\Credential\AccountSecretCredential;
use WayForPay\SDK\Credential\AccountSecretTestCredential;
use WayForPay\SDK\Domain\Client;
use WayForPay\SDK\Domain\PaymentSystems;
use WayForPay\SDK\Domain\Product;
use WayForPay\SDK\Exception\WayForPaySDKException;
use WayForPay\SDK\Handler\ServiceUrlHandler;
use WayForPay\SDK\Wizard\PurchaseWizard;
use Yii;
use yii\base\Component;

class WayForPay extends Component
{
    public $test = false;
    public $merchantDomainName = 'domain';
    public $account;
    public $secret;
    public $returnUrl;
    public $serviceUrl;
    public $currency = 'UAH';
    public $clientCountry = 'UKR';

    private $credential;

    public function init()
    {
        parent::init();

        // TODO: InvalidConfigException

        if ($this->test) {
            $this->credential = new AccountSecretTestCredential();
        } else {
            $this->credential = new AccountSecretCredential($this->account, $this->secret);
        }
    }

    public function button(Order $order, $return = null)
    {
        $cartItemCount = $order->cartItemCount;
        $cartItemPrice = $order->cartItemPrice;

        $products = [];

        foreach ($order->products as $product) {
            $products[] = new Product(
                $product->product->name . ($product->name ? ', ' . $product->name : null),
                $cartItemPrice[$product->id] * $cartItemCount[$product->id],
                $cartItemCount[$product->id]
            );
        }

        $form = PurchaseWizard::get($this->credential)
            ->setOrderReference($order->id . ($this->test ? '_test_' . time() : ''))
            ->setAmount($order->amount)
            ->setCurrency($this->currency)
            ->setOrderDate((new \DateTime())->setTimestamp($order->created_at))
            ->setMerchantDomainName($this->merchantDomainName)
            ->setClient(new Client(
                null,
                null,
                $order->buyer->email,
                $order->buyer->phone,
                $this->clientCountry
            ))
            ->setProducts(new ProductCollection($products))
            ->setReturnUrl($this->returnUrl)
            ->setServiceUrl($this->serviceUrl)
            ->setPaymentSystems(new PaymentSystems([
                PaymentSystems::CARD,
                PaymentSystems::MASTER_PASS,
                PaymentSystems::VISA_CHECKOUT,
                PaymentSystems::GOOGLE_PAY,
                PaymentSystems::APPLE_PAY,
            ]))
            ->setReturnUrl($return)
            ->getForm()
            ->getAsString(Yii::t('app', 'Оплатить'), 'btn btn-primary btn-lg');

        return $form;
    }

    public function serviceUrl()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $handler = new ServiceUrlHandler($this->credential);
            $transaction = $handler->parseRequestFromArray($data)->getTransaction();
            $order_id = $this->getOrderId($transaction->getOrderReference());
            WfpLog::log($order_id, $data);
            if ($order = Order::findOne($order_id)) {
                if ($transaction->isStatusApproved()) {
                    $order->status = Order::STATUS_PAID;
                    $order->update();
                } elseif ($transaction->isStatusDeclined()) {
                    $order->status = Order::STATUS_ERROR;
                    $order->update();
                }
            }
            return $handler->getSuccessResponse($transaction);
        } catch (WayForPaySDKException $e) {
            return "WayForPay SDK exception: " . $e->getMessage();
        }
    }

    /**
     * @param $orderReference
     * @return integer
     */
    public function getOrderId($orderReference)
    {
        if ($this->test) {
            $exp = explode('_', $orderReference);
            return $exp[0];
        }

        return (int)$orderReference;
    }
}