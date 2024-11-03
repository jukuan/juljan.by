<?php

namespace App\Service\Payment;

use App\Entity\Payment;

interface IPaymentService
{
    public function submitPaymentRequest(Payment $payment): string;
}