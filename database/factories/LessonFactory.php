<?php

namespace Database\Factories;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(4),
            'content_type' => 'video',
            'content' => $this->faker->paragraph(),
            'media_url' => 'https://example.com/video/' . $this->faker->unique()->numberBetween(1000,9999),
            'duration_seconds' => $this->faker->numberBetween(60, 1800),
            'is_downloadable' => $this->faker->boolean(30),
            'position' => 1,
        ];
    }
}
