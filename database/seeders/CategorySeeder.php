    <?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Learn modern web development technologies and frameworks',
            ],
            [
                'name' => 'Mobile Development',
                'slug' => 'mobile-development',
                'description' => 'Master iOS and Android app development',
            ],
            [
                'name' => 'Data Science',
                'slug' => 'data-science',
                'description' => 'Explore data analysis, machine learning, and AI',
            ],
            [
                'name' => 'Cloud Computing',
                'slug' => 'cloud-computing',
                'description' => 'Deploy and manage applications on cloud platforms',
            ],
            [
                'name' => 'DevOps',
                'slug' => 'devops',
                'description' => 'Learn CI/CD, containerization, and infrastructure as code',
            ],
            [
                'name' => 'Database Design',
                'slug' => 'database-design',
                'description' => 'Master SQL, NoSQL, and database optimization',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
