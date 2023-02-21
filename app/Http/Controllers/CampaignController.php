<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\CampaignAuthorAndCategory;

class CampaignController extends Controller
{
    public function index() {
        $campaigns = Campaign::all();

        return $campaigns;
    }

       /**
     * @OA\Post(
     *     path="/api/campaigns/create",
     *     summary="Bir kampanya ekle",
     *     tags={"Kampanyalar"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="min_quantity",
     *                     type="integer"
     *                 ),
     *                 example={
     *                      "name": "4 Al 3 Öde",
     *                      "campaign_min_quantity": 3,
     *                  },
     *             )
     *         ),
     *     ),
     *      @OA\Response(
     *         response="200",
     *         description="OK",
     *          ),
     *     )
     * )
     */
    public function create(Request $request) {

        $validated = $request->validate([
            'name' => 'required|max:255',
            'campaign_min_quantity' => 'required|integer',
        ]);
        $data = collect($validated);

        $campaign = Campaign::updateOrCreate(
            ['name' => $data['name']],
            ['name' => $data['name'], 'campaign_min_quantity' => $data['campaign_min_quantity']],
        );

        return $campaign;
    }

        /**
     * @OA\Post(
     *     path="/api/campaigns/author_category",
     *     summary="Bir Yazarı ve Kategoriyi Kampanyaya Dahil Et",
     *     tags={"Kampanyalar"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="author_id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="category_id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="campaign_id",
     *                     type="integer"
     *                 ),
     *                 example={
     *                      "author_id": 3,
     *                      "category_id": 1,
     *                      "campaign_id": 1,
     *                  },
     *             )
     *         ),
     *     ),
     *      @OA\Response(
     *         response="200",
     *         description="OK",
     *          ),
     *     )
     * )
     */
    public function authorAndCategory(Request $request) {
        $validated = $request->validate([
            'author_id' => 'required|integer',
            'category_id' => 'required|integer',
            'campaign_id' => 'required|integer',
        ]);
        $data = collect($validated);

        $campaign = CampaignAuthorAndCategory::updateOrCreate(
            ['author_id' => $data['author_id'], 'category_id' => $data['category_id'], 'campaign_id' => $data['campaign_id']],
            ['author_id' => $data['author_id'], 'category_id' => $data['category_id'], 'campaign_id' => $data['campaign_id']],
        );

        return $campaign;
    }
}
