<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfStreamingTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected Course $course;
    protected Lesson $lesson;
    protected Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user (student)
        $this->student = User::factory()->create();
        $this->student->roles()->attach(
            \App\Models\Role::firstOrCreate(['name' => 'student'], ['description' => 'Student'])
        );

        // Create test course
        $this->course = Course::factory()->create();

        // Create module
        $module = CourseModule::factory()->create([
            'course_id' => $this->course->id
        ]);

        // Create PDF lesson - create actual test PDF file
        $pdfPath = 'test-pdfs/sample.pdf';
        $this->createTestPdf($pdfPath);

        $this->lesson = Lesson::factory()->create([
            'module_id' => $module->id,
            'media_url' => "/storage/{$pdfPath}",
            'media_type' => 'pdf'
        ]);

        // Enroll student
        $this->enrollment = Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);
    }

    /**
     * Create a simple test PDF file
     */
    protected function createTestPdf(string $path): void
    {
        $dir = storage_path("app/public/" . dirname($path));
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Create minimal PDF file
        $pdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj xref 0 4 0000000000 65535 f 0000000009 00000 n 0000000058 00000 n 0000000115 00000 n trailer<</Size 4/Root 1 0 R>>startxref 193 %%EOF";
        
        file_put_contents(storage_path("app/public/{$path}"), $pdfContent);
    }

    /**
     * Test PDF streaming returns 200 status code
     */
    public function test_pdf_streaming_returns_200_ok(): void
    {
        $response = $this->actingAs($this->student)
            ->get("/student/api/media/{$this->lesson->id}");

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test PDF has correct Content-Type header
     */
    public function test_pdf_has_correct_content_type(): void
    {
        $response = $this->actingAs($this->student)
            ->get("/student/api/media/{$this->lesson->id}");

        $this->assertEquals(
            'application/pdf',
            $response->headers->get('Content-Type')
        );
    }

    /**
     * Test PDF has Content-Disposition inline (not attachment)
     */
    public function test_pdf_has_inline_disposition(): void
    {
        $response = $this->actingAs($this->student)
            ->get("/student/api/media/{$this->lesson->id}");

        $contentDisposition = $response->headers->get('Content-Disposition');
        
        $this->assertStringStartsWith('inline;', $contentDisposition);
        $this->assertStringContainsString('filename=', $contentDisposition);
    }

    /**
     * Test PDF has Content-Length header
     */
    public function test_pdf_has_content_length_header(): void
    {
        $response = $this->actingAs($this->student)
            ->get("/student/api/media/{$this->lesson->id}");

        $this->assertNotNull(
            $response->headers->get('Content-Length')
        );
        
        $this->assertGreaterThan(0, $response->headers->get('Content-Length'));
    }

    /**
     * Test PDF streaming has body content
     */
    public function test_pdf_has_binary_content(): void
    {
        $response = $this->actingAs($this->student)
            ->get("/student/api/media/{$this->lesson->id}");

        // PDF should start with %PDF magic number
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    /**
     * Test unauthenticated user cannot stream PDF
     */
    public function test_unauthenticated_user_cannot_stream_pdf(): void
    {
        $response = $this->get("/student/api/media/{$this->lesson->id}");

        // Should redirect to login or return 401
        $this->assertTrue(
            $response->status() === 302 || $response->status() === 401
        );
    }

    /**
     * Test user not enrolled cannot stream PDF
     */
    public function test_unenrolled_user_cannot_stream_pdf(): void
    {
        $otherStudent = User::factory()->create();
        $otherStudent->roles()->attach(
            \App\Models\Role::firstOrCreate(['name' => 'student'], ['description' => 'Student'])
        );

        $response = $this->actingAs($otherStudent)
            ->get("/student/api/media/{$this->lesson->id}");

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * Test non-existent lesson returns 404
     */
    public function test_nonexistent_lesson_returns_404(): void
    {
        $response = $this->actingAs($this->student)
            ->get("/student/api/media/99999");

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Test PDF streaming with Accept-Ranges header (for pause/resume)
     */
    public function test_pdf_supports_range_requests(): void
    {
        $response = $this->actingAs($this->student)
            ->get("/student/api/media/{$this->lesson->id}");

        $this->assertEquals(
            'bytes',
            $response->headers->get('Accept-Ranges')
        );
    }

    /**
     * Test PDF has proper caching headers
     */
    public function test_pdf_has_cache_control_header(): void
    {
        $response = $this->actingAs($this->student)
            ->get("/student/api/media/{$this->lesson->id}");

        $cacheControl = $response->headers->get('Cache-Control');
        
        $this->assertNotNull($cacheControl);
        $this->assertStringContainsString('max-age', $cacheControl);
    }

    /**
     * Test response is not 204 No Content
     */
    public function test_response_is_not_204_no_content(): void
    {
        $response = $this->actingAs($this->student)
            ->get("/student/api/media/{$this->lesson->id}");

        // This was the original bug - response was 204
        $this->assertNotEquals(204, $response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Cleanup after tests
     */
    protected function tearDown(): void
    {
        // Clean up test PDF files
        $testPdfDir = storage_path('app/public/test-pdfs');
        if (is_dir($testPdfDir)) {
            array_map('unlink', glob("$testPdfDir/*"));
            rmdir($testPdfDir);
        }

        parent::tearDown();
    }
}
