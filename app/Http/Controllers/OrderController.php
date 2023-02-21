<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\Product;
Use App\Models\Order;
use App\Models\OrderProduct;
use App\Jobs\SaveOrderJob;
use App\Models\Campaign;
use App\Rules\ValidProductArrayRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\CampaignAuthorAndCategory;

class OrderController extends Controller
{

    /**
 * @OA\Info(title="My First API", version="0.1")

 * Sipariş.
 *
 * @OA\Post(
 *     path="/api/order/create",
 *     tags={"Sipariş"},
 *     summary="Sipariş oluşturma endpointi.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"products"},
 *             @OA\Property(
 *                 property="products",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"product_id", "quantity"},
 *                     @OA\Property(
 *                         property="product_id",
 *                         type="integer"
 *                     ),
 *                     @OA\Property(
 *                         property="quantity",
 *                         type="integer"
 *                     )
 *                 )
 *             )
 *         )
 *     ),

 *     @OA\Response(
 *         response="200",
 *         description="Order Info"
 *     )
 * )
     */

    public function create(Request $request) {
        $total = 0;
        $kargoPrice = 0;
        $basketProducts = [];

        //Product arrayini validate ediyoruz.
        $validated = $request->validate([
            'products' => ['required', 'array', new ValidProductArrayRule],
        ]);

        //Validate edilmiş datayı Laravel Collection'a çevirip sadece product_idlerini alıyoruz.
        $orderData = collect($validated['products']);
        $productIds = $orderData->pluck('product_id');

        //Parse edilmiş ID's arrayini sorgulayıp ürünleri alıyoruz ve boşsa hata döndürüyoruz.
        $products = Product::whereIn('id', $productIds)->get();
        if($products->isEmpty()) {
            return response()->json(['message' => 'Ürün bulunamadı.'], 500);
        }

        //Ürünlerin içinden müşteriye en fazla kârı sağlaması üzere en pahalı ürünün
        // kampanyası var mı yok mu kontrol ediyoruz, kampanyası olmadığı takdirde bir diğer
        // pahalı ürünü sorguluyoruz.
        $mostExpensiveProductPrice = Product::whereIn('id', $productIds)
        ->where(function ($query) {
            $query->whereExists(function ($subquery) {
                $subquery->select(DB::raw(1))
                    ->from('author_category_campaign')
                    ->whereRaw('author_category_campaign.author_id = products.author_id');
            })->whereExists(function ($subquery) {
                $subquery->select(DB::raw(1))
                    ->from('author_category_campaign')
                    ->whereRaw('author_category_campaign.category_id = products.category_id');
            });
        })->max('list_price');
        $mostExpensiveProductId = $products->where('list_price', $mostExpensiveProductPrice)->pluck('id')->first();


        // En pahalı ürünü bulduk, productları for dönüyoruz ve hesaplama vs. işlemlere başlıyoruz.
        foreach($products as $product) {
            // Burada daha önce sorguladığımız Laravel Collectionunun içinden first komutuyla
            // adet ve ücret kısımları için sepet karşılığını alıyoruz.
            $basketProduct = $orderData->where('product_id', $product->id)->first();


            // Burada $basketProductQuantity ve basketProduct['quantity'] olarak iki adet değeri mevcut,
            // bunu yapmamızın sebebi ürünün 4 al 3 öde kampanyasına karşılık eğer ürünün kampanyası varsa
            // basketProduct kısmına kampanya için kullandığımız adedi değil gerçek sepet değerini yazmamız gerekli.
            // Fakat ürünlerin fiyatını hesaplarken kampanya dahilinde ise ürünü 1 adet eksilttik ve fiyatı ona göre
            // hesapladık.
            $basketProductQuantity = $basketProduct['quantity'];

            if($basketProduct['quantity'] > $product->stock_quantity) {
                return response()->json(['message' => "$product->title" . ' ürünü için stok miktarını geçtiniz.'], 500);
            }

            if($mostExpensiveProductPrice && $mostExpensiveProductId && $product->id == $mostExpensiveProductId) {
                $campaignProduct = Product::where('id', $mostExpensiveProductId)->first();

                //Burada yazar ve kategori kampanyası kurgumuz olduğundan, database'den ilgili yazarın
                // ve kitaplarının kategorisine ait herhangi bir kampanya var mı yokmu onu sorguluyoruz.
                $campaignPivot = CampaignAuthorAndCategory::
                where('author_id', $campaignProduct->author_id)
                ->where('category_id' , $campaignProduct->category_id)
                ->first();


                // Yukarıda eriştiğimiz campaign id'sini burada sorguladık ve kampanyaya eriştik.
                $campaign = Campaign::where('id', $campaignPivot->campaign_id)->first();


                if($campaign) {
                    // Bir kampanyaya dahil olabilmek için kampanyanın minimum sepet değerini
                    // karşılamak gerekli. Burada tam olarak bu işlem yapılıyor. Yoksa
                    // database sütununu kampanya uygulanmadı olarak güncelliyoruz.
                    if($basketProduct['quantity'] > $campaign->campaign_min_quantity) {
                        $basketProductQuantity = $basketProductQuantity - 1;
                        $basketProduct['campaign_name'] = $campaign->name;
                    }
                }
            }else {
                $basketProduct['campaign_name'] = 'Kampanya Uygulanmadı';
            }

            // Burada ürünün liste fiyatı ve adedini çarpıp totale erişeceğiz,
            // eğer ürün kampanya dahilindeyse sepet adedi 1 eksiltilmiş,
            // 4 al 3 öde kampanyası uygulanmıştı.
            $price = $product->list_price * $basketProductQuantity;
            $noDiscountPrice = $product->list_price * $basketProduct['quantity'];
            $total += $price;

            // İskonto fiyatını ve liste fiyatını basketProducts array'inin içine push ediyoruz.
            $basketProduct['discount_price'] = (float)number_format($price, 2);
            $basketProduct['list_price'] = (float)number_format($noDiscountPrice, 2);
            $basketProduct['product_id'] = $product->id;
            $basketProducts[] = $basketProduct;
        }



        // Burada sepet tutarı 100 TL'den büyükse %5 iskonto uyguluyoruz
        if($total >= 100){
            $total = $total - ($total * (5 / 100));
        }

        // Burada da sepet tutarı 200 TL'den büyükse kargo ücretini 0'a eşitliyoruz, aksi takdirde 75 TL ödeniyor.
        if($total >= 200) {
            $kargoPrice = 0;
        } else {
            $kargoPrice = 75;
            $total += $kargoPrice;
        }

        //Siparişleri kuyruklama sistemine sokup kaydediyoruz.
        SaveOrderJob::dispatch($total, $kargoPrice, $basketProducts)->onQueue('siparisler');

        // İşlem tamamlandı.
        return response(['Sipariş Kaydedildi.'], 200);
    }


    /**
     * @OA\Get(
     *     path="/api/order/{id}",
     *     tags={"Sipariş"},
     *     summary="Bir Siparişi Döner",
     *   @OA\Response(
     *     response= "default",
     *     description="Success: Order",
     *   )
     * )
     */
    public function show($id)
    {

        if(!is_numeric($id)) {
            return 'Sayı Değeri Giriniz';
        }
        $order = Cache::remember('order_' . $id, 86400, function () use($id) {
            return Order::with(['products' =>  function($q) {
                $q->with('product');
            }])->findOrFail($id);
        });

        return $order;
    }
}
