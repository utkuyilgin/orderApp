<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;
use File;

class AuthorSeeder extends Seeder
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
        $authorNames = $json->pluck('author');

        foreach($authorNames as $authorName) {

            Author::updateOrCreate([
                'name' => $authorName,
            ], [
                'name' => $authorName
            ]);
        }
    }
}
