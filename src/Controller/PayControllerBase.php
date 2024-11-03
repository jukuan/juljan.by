<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\Product;
use App\Form\PaymentFormType;
use App\Service\Payment\BePaymentService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use BeGateway\PaymentOperation;
use BeGateway\Product as BeGatewayProduct;

class PayControllerBase extends BaseFrontController
{
    private string $lang = self::DEFAULT_LANG;

    #[Route('/pay/{page}', name: 'pay_page')]
    public function payPage(
        BePaymentService $paymentService,
        string $page,
    ): Response
    {
        $this->detectLanguage();
        $payment = $this->generatePaymentEntity();

        $request = $this->getRequest();
        $form = $this->createForm(PaymentFormType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entity = $form->getData();
            $this->em->persist($entity);
            $this->em->flush();

            if ($payLink = $paymentService->submitPaymentRequest($payment)) {
                return $this->redirect($payLink);
            }

            return $this->redirectToRoute('pay_page', [
                'page' => $page,
                'created' => $entity->getId()
            ]);
        }

        $parameters = [
            'lang' => $this->lang,
            'paymentForm' => $form,
        ];

        return $this->render(sprintf('pay/%s.html.twig', $page), $parameters);
    }

    private function generatePaymentEntity(): Payment
    {
        $payment = new Payment();
        $product = null;

        if ($pid = $this->getRequest()->get('product')) {
            $product = $this->em->getRepository(Product::class)->find($pid);

            if ($product) {
                $payment->setProduct($product);
            }
        } else if ($price = $this->getRequest()->get('price')) {
            $payment->setSum((string)$price);

            if (!$payment->getService()) {
                $payment->setService('Доработка элементов web-сайта');
            }
        }

        $payment->setTrackingId('j'.sprintf('YmdHi%s', $product?->getId() ?? '0'));

        return $payment;
    }

    private function detectLanguage(): void
    {
        $lang = $this->getLang();

        if ($lang) {
            $this->lang = $lang;
        }
    }
}
