<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;

class AdminCartController extends Controller
{
    /**
     * Display all carts with pagination
     */
    public function index(Request $request)
    {
        $query = Cart::with(['user', 'product']);

        // Search by user name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $carts = $query->paginate(15);
        $totalCarts = Cart::count();

        return view('admin.carts.index', compact('carts', 'totalCarts'));
    }

    /**
     * Delete cart item
     */
    public function destroy($id)
    {
        $cart = Cart::find($id);

        if (!$cart) {
            return response()->json([
                'message' => 'Cart item not found'
            ], 404);
        }

        $cart->delete();

        return response()->json([
            'message' => 'Cart item deleted successfully'
        ], 200);
    }

    /**
     * Clear user's entire cart
     */
    public function clearUserCart($userId)
    {
        Cart::where('user_id', $userId)->delete();

        return response()->json([
            'message' => 'User cart cleared successfully'
        ], 200);
    }
}
