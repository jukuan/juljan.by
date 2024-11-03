<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Payment;
use BeGateway\Product as BeGatewayProduct;
use BeGateway\Settings as BeGatewaySettings;
use Doctrine\ORM\EntityManagerInterface;

class BePaymentService implements IPaymentService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
        self::initialize();;
    }

    private static function initialize(): void
    {
        BeGatewaySettings::$shopId = 29364;

        // Shop secret key issued by your payment provider
        BeGatewaySettings::$shopKey = 'f1042563396c06acf26aa96dc5aba674541c87db8bd88cebb0a1a8952e5883fe';

        // Shop secret key issued by your payment provider
        BeGatewaySettings::$shopPubKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAw9WNfzN1mE1x2sIhi403UBnFV8iZuWv3VBL9d9YvY2ad1QtMHPCjS1JHe1VpZ19LhUlvpa1AvWSXHb+zml0LSh/Kv//zrrnn95aNP8jbsr6XUhTgRcPbRyO96nKwQiSL5yWK8w3C8mfALhe6UlkRb7+C5NDAPDwQg4lMoEtKLazCcAPva99+6s9F1y4qC0dHhfsfxhBUa7n83WZVGDNL4DX8rKr3clAi/kadpE+24h3BhDRtR+1y9rSdelVfgd/ZqclS+RGCBHhTLTik7LxUnXKNY1b1wxDELZdf1tBGex+NjiBFjbo0tdU9l7jmZ7Z2qBXQYDrcSsGh19H/zKPG/QIDAQAB';

        // Checkout URL of your payment provider. Confirm it with support team or refer
        // to your payment provider API documentation
        BeGatewaySettings::$checkoutBase = 'https://checkout.bepaid.by';

        // Gateway URL of your payment provider. Confirm it with support team or refer
        // to your payment provider API documentation
        BeGatewaySettings::$gatewayBase = 'https://gateway.bepaid.by';

        // API URL of your payment provider. Confirm it with support team or refer
        // to your payment provider API documentation
        BeGatewaySettings::$apiBase = 'https://api.begateway.com';
    }


    private function generatePaymentObject(Payment $payment): BeGatewayProduct
    {
        $transaction = new BeGatewayProduct;
        $transaction->money->setAmount($payment->getSum());
        $transaction->money->setCurrency('BYN');
        $transaction->setName($payment->getService());
        $transaction->setDescription(sprintf('Date: %s', date('Y-m-d H:i')));

//        $transaction->setTestMode(true);///

        return $transaction;
    }

    public function submitPaymentRequest(Payment $payment): string
    {
        $transaction = $this->generatePaymentObject($payment);
        $response = $transaction->submit();

        if ($response->isSuccess()) {
            $payment->addKeyParams('payment', [
                'id' =>  $response->getId(),
                'link' =>  $response->getPayLink(),
                'url' =>  $response->getPayUrl(),
            ]);
            $payLink = $response->getPayLink();
        } else {
            $payment->addKeyParams('payment', $response->getResponseArray());
            $payLink = '';
            dd($response->getResponseArray()); // tmp
        }

        $this->em->persist($payment);
        $this->em->flush();

        return $payLink;
    }
}