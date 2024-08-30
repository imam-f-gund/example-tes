<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index()
    {
        $date = Carbon::today();
        
        $totalAmountToday = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereDate('orders.created_at', $date)
            ->sum(DB::raw('order_items.quantity * order_items.price'));
    
        if (request()->ajax()) {
            $orders = OrderItem::with('order.customer', 'product')
                ->select(['id', 'order_id', 'product_id', 'quantity', 'price', 'subtotal', 'created_at'])
                ->whereHas('order', function($query){
                    $query->where('customer_id', auth()->user()->id_cust);
                });
        
            return DataTables::eloquent($orders)
                ->addColumn('product_name', function ($orderItem) {
                    return $orderItem->product->name;
                })
                ->addColumn('product_category', function ($orderItem) {
                    return $orderItem->product->category;
                })
                ->editColumn('price', function ($orderItem) {
                    return 'Rp ' . number_format($orderItem->price, 2);
                })
                ->editColumn('subtotal', function ($orderItem) {
                    return 'Rp ' . number_format($orderItem->subtotal, 2);
                })
                ->editColumn('created_at', function ($orderItem) {
                    return $orderItem->created_at->format('Y-m-d H:i:s');
                })
                ->filterColumn('product_name', function($query, $keyword) {
                    $query->whereHas('product', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('product_category', function($query, $keyword) {
                    $query->whereHas('product', function($q) use ($keyword) {
                        $q->where('category', 'like', "%{$keyword}%");
                    });
                })
                ->orderColumn('product_name', function ($query, $order) {
                    $query->join('products', 'order_items.product_id', '=', 'products.id')
                        ->orderBy('products.name', $order);
                })
                ->make(true);
        }
            
        $products = Product::all();
    
        return view('orders/index', compact('products', 'totalAmountToday'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // Buat pesanan baru
        $order = Order::create([
            'customer_id' => auth()->user()->id_cust, 
            'total_amount' => 0, 
        ]);

        $total = 0;

        // Simpan setiap item pesanan
        foreach ($request->products as $product) {
            $productDetail = Product::find($product['id']);
            $subtotal = $productDetail->price * $product['quantity'];

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productDetail->id,
                'quantity' => $product['quantity'],
                'price' => $productDetail->price,
                'subtotal' => $subtotal,
            ]);

            $total += $subtotal;
        }

        // Update total pesanan
        $order->update(['total_amount' => $total]);

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }
}
