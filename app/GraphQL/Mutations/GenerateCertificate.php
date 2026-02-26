<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Support\Str;

final readonly class GenerateCertificate
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): Certificate
    {
        $courseId = $args['course_id'];
        $userId = auth()->id();

        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('status', 'completed')
            ->firstOrFail();

        $certificate = Certificate::create([
            'enrollment_id' => $enrollment->id,
            'user_id' => $userId,
            'course_id' => $courseId,
            'issued_at' => now(),
            'cert_number' => 'CERT-' . Str::upper(Str::random(10)),
            'pdf_url' => null, // Generate PDF later
            'digital_signature' => 'signature-placeholder',
            'data' => json_encode(['course' => $enrollment->course->title, 'user' => $enrollment->user->name]),
        ]);

        return $certificate;
    }
}
