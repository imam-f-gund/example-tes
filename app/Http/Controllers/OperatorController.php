<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class OperatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $date = Carbon::today();
        
        $totalAmountToday = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereDate('orders.created_at', $date)
            ->sum(DB::raw('order_items.quantity * order_items.price'));
    
        if (request()->ajax()) {
            $orders = OrderItem::with('order.customer', 'product')
                ->select(['id', 'order_id', 'product_id', 'quantity', 'price', 'subtotal', 'created_at']);
    
            return DataTables::of($orders)
                ->addColumn('customer_name', function ($orderItem) {
                    return $orderItem->order->customer->name;
                })
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
                ->filterColumn('customer_name', function($query, $keyword) {
                    $query->whereHas('order.customer', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
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
                ->orderColumn('customer_name', function ($query, $order) {
                    $query->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->join('customers', 'orders.customer_id', '=', 'customers.id')
                        ->orderBy('customers.name', $order);
                })
                ->orderColumn('product_name', function ($query, $order) {
                    $query->join('products', 'order_items.product_id', '=', 'products.id')
                        ->orderBy('products.name', $order);
                })
                ->make(true);
        }
    
        $customers = Customer::all();
        $products = Product::all();
    
        return view('orders/operator_index', compact('customers', 'products', 'totalAmountToday'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'customer' => 'required',
        ]);

        DB::beginTransaction();

        try {
            // Buat pesanan baru
            $order = Order::create([
                'customer_id' => $request->customer,
                'total_amount' => 0,
            ]);

            $total = 0;

            // Simpan setiap item pesanan
            foreach ($request->products as $product) {
                $productDetail = Product::findOrFail($product['id']); // Gunakan findOrFail untuk menghandle ModelNotFoundException
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

            DB::commit();

            return redirect()->route('operator')->with('success', 'Order created successfully.');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('operator')->with('error', 'Product not found.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('operator')->with('error', 'Failed to create order. Please try again.');
        }
    }

    public function report(Request $request)
    {
        // Ambil parameter tanggal dari request
        $date = $request->input('date', Carbon::today()->toDateString()); // Default ke hari ini jika tidak ada input

        // Menentukan jenis laporan berdasarkan tanggal
        $startDate = Carbon::parse($date)->startOfDay();
        $endDate = Carbon::parse($date)->endOfDay();

        // Jika tipe laporan adalah mingguan
        if ($request->has('type') && $request->input('type') == 'week') {
            $startDate = Carbon::parse($date)->startOfWeek();
            $endDate = Carbon::parse($date)->endOfWeek();
        }

        // Jika tipe laporan adalah bulanan
        if ($request->has('type') && $request->input('type') == 'month') {
            $startDate = Carbon::parse($date)->startOfMonth();
            $endDate = Carbon::parse($date)->endOfMonth();
        }

        // Total amount dalam periode yang dipilih
        $totalAmount = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->selectRaw('SUM(order_items.quantity * order_items.price) as total')
            ->first();

        // Detail penjualan per produk dalam periode yang dipilih
        $itemsSold = DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->whereBetween('orders.created_at', [$startDate, $endDate])
        ->select('products.name', 
                 DB::raw('SUM(order_items.quantity) as quantity_sold'), 
                 DB::raw('SUM(order_items.subtotal) as total_price'))
        ->groupBy('products.name')
        ->orderBy('quantity_sold', 'desc') // Tambahkan pengurutan jika diperlukan
        ->get();

        $totalQuantity = $itemsSold->sum('quantity_sold');
        $totalPrice = $itemsSold->sum('total_price');

        return view('report.index', compact(
            'totalAmount', 
            'itemsSold', 
            'startDate', 
            'endDate', 
            'totalQuantity', 
            'totalPrice'
        ));
    }

}
