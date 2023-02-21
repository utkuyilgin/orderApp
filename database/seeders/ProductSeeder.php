<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Author;
use File;

class ProductSeeder extends Seeder
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

        $authorNames = collect($json)->pluck('author')->unique()->toArray();
        $authors = Author::whereIn('name', $authorNames)->get()->keyBy('id');

        foreach($json as $product) {
            $author = $authors->where('name', $product->author)->first();

            if(!$author) {
                Author::create([
                    'name' => $product->author,
                ]);
            }
            Product::updateOrCreate([
                'id' => $product->product_id,
            ], [
                'id' => $product->product_id,
                'title' => $product->title,
                'author_id' => $author->id,
                'category_id' => $product->category_id,
                'list_price' => $product->list_price,
                'stock_quantity' => $product->stock_quantity
            ]);
        }


    }
}
