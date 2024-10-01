<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;


class ProductController extends Controller
{

    public function show(){
        $products = Product::all();
        return response()->json([
            'data' => $products,
            'success' => true,
        ]);
    }
    public function topProducts()
    {
        $products = Product::with('orderDetail')->get();
        return response()->json([
            'data' => $products,
            'success' => true,
        ]);
    }

    public function addProductToOrder(Request $request, string $id){
        $user = $request->user();
        
        // Tìm đơn hàng của người dùng với status = 0
        $order = Order::where('user_id', $user->id)->where('status', 0)->first();
        $product = Product::find($id);

        // Nếu không có đơn hàng với status = 0
        if (!$order) {
            // Kiểm tra xem có đơn hàng nào với status = 0 hay không
            $existingOrders = Order::where('user_id', $user->id)->where('status', 1)->get();
            $Orders = Order::all();
            // Nếu tất cả các đơn hàng đều có status = 1, tạo đơn hàng mới
            if ($existingOrders->count() > 0 || $existingOrders->count() == 0) {
                $order = new Order();
                $order->user_id = auth()->id();
                $order->total = $product->price;
                $order->status = 0;
                $order->save();

                $orderDetail = new OrderDetail();
                $orderDetail->order_id = $order->id;
                $orderDetail->product_id = $id;
                $orderDetail->quantity = 1;
                $orderDetail->save();
            }
        } else {
            $orderDetail = OrderDetail::where('order_id', $order->id)->where('product_id', $id)->first();
           
            if ($orderDetail) {
                // Nếu chi tiết đơn hàng đã tồn tại, tăng quantity lên 1
                $orderDetail->quantity += 1;
                $orderDetail->save(); // Lưu lại
            } else {
                $orderDetail = new OrderDetail();
                $orderDetail->order_id = $order->id;
                $orderDetail->product_id = $id;
                $orderDetail->quantity = 1; // Khởi tạo quantity
                $orderDetail->save();
            }
            $order->total =  $order->total +  $product->price;
            $order->save();
        }

        return response()->json([
            'data' => $order,
            'success' => true,
        ]);
    }

    public function getAllOrders()
    {
    
          // Truy vấn để lấy tất cả các đơn hàng và chi tiết từng đơn hàng
          $orders = Order::with(['orderDetails.product'])->where('status', 1)->get();

          $orderList = $orders->map(function ($order) {
          $totalOrderPrice = $order->orderDetails->sum(function ($detail) {
              return $detail->quantity * $detail->product->price;
          });

          return [
              'order_id' => $order->id,
              'total_order' => $totalOrderPrice,
              'order_details' => $order->orderDetails->map(function ($detail) {
                  return [
                      'product_name' => $detail->product->name,
                      'quantity' => $detail->quantity,
                      'unit_price' => $detail->product->price,
                      'total_price' => $detail->quantity * $detail->product->price,
                  ];
              })
          ];
      });
    
      return response()->json($orders);

    }

    public function placeOrder(string $id){
        $orderdetail = Order::with(['orderDetails.product'])->where('status', 1)->get();
        $order = Order::find($id);
        $order->status = 1;
       // $order->save();
        return response()->json([
            'success' => true,
            'message' => 'Đặt hàng thành công',
            'user' =>  $orderdetail,
        ]);
    }

}