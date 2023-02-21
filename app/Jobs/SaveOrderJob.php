<?php
namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $total;
    protected $kargoPrice;
    protected $basketProducts;

    public function __construct($total, $kargoPrice, $basketProducts)
    {
        $this->total = $total;
        $this->kargoPrice = $kargoPrice;
        $this->basketProducts = $basketProducts;
    }

    public function handle()
    {
        // Siparişi kaydetme işlemi
        $order = Order::create([
            'total' => $this->total,
            'kargo_price' => $this->kargoPrice,
        ]);

        // Sepet ürünleri kaydetme işlemi
        $basketProducts = collect($this->basketProducts)->map(function ($item) use($order) {
            return $item + ['order_id' => $order->id];
        })->toArray();

        foreach($basketProducts as $basketProduct) {
            OrderProduct::create($basketProduct);
        }
    }
}
