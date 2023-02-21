<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;
use App\Models\Campaign;
use App\Models\CampaignAuthorAndCategory;
use File;

class CampaignSeeder extends Seeder
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

        $author = $json->where('author', "Sabahattin Ali")->pluck('author')->unique();

        $findAuthor = Author::firstOrCreate([
            'name' => $author
        ], [
            'name' => $author
        ]);

             Campaign::updateOrCreate([
                 'name' => '3 Al 1 Öde',
                 'campaign_min_quantity' => 2,
             ], [
                'name' => '3 Al 1 Öde',
                'campaign_min_quantity' => 2,
            ]);

            CampaignAuthorAndCategory::create([
                'author_id' => 3,
                'category_id' => 1,
                'campaign_id' => 1
            ]);
    }
}
