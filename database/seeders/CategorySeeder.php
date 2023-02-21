<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use File;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = json_decode(File::get('Case_Products.json'));

        $json = collect($json);
        $categoryNameAndId = $json->pluck('category_title', 'category_id');

        foreach($categoryNameAndId as $id => $title) {
            Category::updateOrCreate([
                'id' => $id,
                'title' => $title,
            ], [
                'id' => $id,
                'title' => $title
            ]);
        }
    }
}
