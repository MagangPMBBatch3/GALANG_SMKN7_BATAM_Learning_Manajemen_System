<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'amount' => $this->faker->numberBetween(10000, 200000),
            'currency' => 'IDR',
            'method' => 'fake_card',
            'status' => 'paid',
            'transaction_ref' => 'tx_' . $this->faker->unique()->regexify('[A-Za-z0-9]{8}'),
            'payment_data' => [],
            'paid_at' => now(),
        ];
    }
}
