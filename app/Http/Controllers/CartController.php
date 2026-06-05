<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class CartController extends Controller
{
    // 1. Hiển thị trang giỏ hàng
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart', compact('cart'));
    }

    // 2. Thêm sách vào giỏ hàng
    public function add(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $cart = session()->get('cart', []);
        
        $quantityToAdd = $request->input('quantity', 1);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantityToAdd;
        } else {
            $cart[$id] = [
                "title" => $book->title,
                "quantity" => $quantityToAdd,
                "price" => $book->price,
                "image_url" => $book->image_url ?? 'default-book.jpg',
                "author" => $book->author
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Đã thêm sách vào giỏ hàng!');
    }

    // 3. Cập nhật số lượng sách trong giỏ hàng
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
            
            return redirect()->back()->with('success', 'Đã cập nhật số lượng giỏ hàng!');
        }

        return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong giỏ hàng.');
    }

    // 4. Xóa sản phẩm khỏi giỏ hàng
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            
            return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
        }

        return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong giỏ hàng.');
    }

    // 5. Làm trống toàn bộ giỏ hàng
    public function clear()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Đã làm trống giỏ hàng!');
    }
}