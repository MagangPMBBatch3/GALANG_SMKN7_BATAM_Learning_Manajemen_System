<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition()
    {
        $title = $this->faker->sentence(3);
        return [
            'instructor_id' => \App\Models\User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->randomNumber(4),
            'short_description' => $this->faker->sentence(),
            'full_description' => $this->faker->paragraph(),
            'price' => $this->faker->numberBetween(0, 150000),
            'is_published' => true,
            'status' => 'published',
            'level' => 'beginner',
            'duration_minutes' => $this->faker->numberBetween(10, 600),
        ];
    }
}
