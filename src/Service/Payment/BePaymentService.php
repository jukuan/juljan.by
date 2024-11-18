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
        BeGatewaySettings::$shopKey = '7d0c5a45f43392651836797e7ec0ab441972a94e182989b88206e5540f738fbb';

        // Shop secret key issued by your payment provider
        BeGatewaySettings::$shopPubKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAya7jwGs1ShA54vUqa+R7LWpa97PqOKx3ud8GoxVTCAeb0ifp6VBubU9aCfmN6bwl8J0NCvomDsBmiYfKhKOAgauvSm4bfUuPC921vnCCjPV1Df68sje4IqbeEqDNr+CMHFqweeMemfB0GRMA1UJa2LTkJ0R2fJSfgo6+jNO51c3rrNs8O+vU28X8oFFjDt86yHb80qn21KvKp7Xra8NR9aydr0bband/YjX33fxo+ifu5ayEoVUiZth/K1a33YL3c5D0z820ZZRblRKWubFkqVuh9WCkMPX1USdJ+B6fRep9InVXFTKZRYDz3pcYJ3hrGQ8oPxRbng3tqMy1uK1/xwIDAQAB';

        // Checkout URL of your payment provider. Confirm it with support team or refer
        // to your payment provider API documentation
        BeGatewaySettings::$checkoutBase = 'https://checkout.bepaid.by';

        // Gateway URL of your payment provider. Confirm it with support team or refer
        // to your payment provider API documentation
        BeGatewaySettings::$gatewayBase = 'https://gateway.bepaid.by';

        // API URL of your payment provider. Confirm it with support team or refer
        // to your payment provider API documentation
        BeGatewaySettings::$apiBase = 'https://api.bepaid.by';
    }


    private function generatePaymentObject(Payment $payment): BeGatewayProduct
    {
        $transaction = new BeGatewayProduct;
        $transaction->money->setAmount($payment->getSum());
        $transaction->money->setCurrency('BYN');
        $transaction->setName($payment->getService());
        $transaction->setDescription(sprintf('Date: %s', date('Y-m-d H:i')));

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
        }

        $this->em->persist($payment);
        $this->em->flush();

        return $payLink;
    }
}