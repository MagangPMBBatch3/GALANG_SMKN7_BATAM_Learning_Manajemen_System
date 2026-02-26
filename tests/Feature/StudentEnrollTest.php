<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentEnrollTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_course_enroll_with_payment_sets_currency_idr(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['price' => 100000]);

        // Disable CSRF middleware for API testing in this environment
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $response = $this->actingAs($user)
            ->postJson('/student/api/enroll', [
                'course_id' => $course->id,
                'payment' => [
                    'amount' => 100000,
                    'currency' => 'IDR',
                    'method' => 'fake_card',
                    'card_last4' => '4242',
                ],
            ]);

        if ($response->status() !== 201) {
            $this->fail('Enroll API failed: ' . $response->getContent());
        }

        $response->assertJsonFragment(['message' => 'Successfully enrolled in course']);

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'currency' => 'IDR',
        ]);
    }
}
