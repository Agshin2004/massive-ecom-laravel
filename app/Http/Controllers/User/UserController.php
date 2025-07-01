<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ReviewResource;

class UserController extends Controller
{
    /**
     * Reviews by the user.
     */
    public function userReviews(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);
        if (!$user) {
            return $this->errorResponse('User not found');
        }
        $reviews = $user->reviews()->paginate(10);

        return $this->successResponse([
            'reviews' => ReviewResource::collection($reviews),
        ]);
    }

    public function userReviewById(string $reviewId)
    {
        $userId = Auth::id();
        $user = User::find($userId);
        if (!$user) {
            return $this->errorResponse('User not found');
        }

        $review = $user->reviews()->where('id', $reviewId)->get();

        return $this->successResponse([
            'review' => $review,
        ]);
    }

    /**
     * Get all items that are in user's cart.
     */
    public function userCartItems()
    {
        $user = Auth::user();

        $cartItems = $user->cart?->items ?? collect();  // if no items just returning empty collection

        return $this->successResponse([
            'cart_items' => $cartItems,
        ]);
    }
}
