<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Payment;

final readonly class DeletePayment
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $paymentId = $args['id'];

        $payment = Payment::findOrFail($paymentId);
        $payment->delete();

        return true;
    }
}
