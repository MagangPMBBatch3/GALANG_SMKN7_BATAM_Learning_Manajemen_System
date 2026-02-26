<?php

namespace Database\Factories;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition()
    {
        return [
            'status' => 'active',
            'progress_percent' => 0,
            'price_paid' => 0,
            'currency' => 'IDR',
        ];
    }
}
