<?php

namespace Database\Seeders;
use App\Models\ContentType;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
           $types = ['Article', 'Video', 'PDF'];
    foreach ($types as $type) {
        ContentType::create(['name' => $type]);
    }
    }
}
