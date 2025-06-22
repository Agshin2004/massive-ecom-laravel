<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Reviews by the user
     */
    public function userReviews(Request $request, $userId)
    {
        $user = User::find($userId);
        if (! $user) {
            return $this->errorResponse('User not found');
        }
        $reviews = $user->reviews()->paginate(10);
        return $this->successResponse([
            'reviews' => ReviewResource::collection($reviews)
        ]);
    }

    public function userReviewById($userId, $reviewId)
    {
        $user = User::find($userId);
        if (! $user) {
            return $this->errorResponse('User not found');
        }

        $review = $user->reviews()->where('id', $reviewId)->get();
        return $this->successResponse([
            'review' => $review,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'body' => ['min:3'],  // making not required since review can only be rating
            'product_id' => ['required', 'numeric', 'exists:App\Models\Product,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5']
        ]);

        $review = Review::create([
            'body' => $request->input('body'),  // will default to null if body not present
            'product_id' => $request->input('product_id'),
            'rating' => $request->input('rating'),
            'user_id' => auth()->id()
        ]);

        return $this->successResponse(new ReviewResource($review));
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return $this->successResponse($review);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $request->validate([
            'body' => ['min:3'],  // making not required since review can only be rating
            'product_id' => ['numeric', 'exists:App\Models\Product,id'],
            'rating' => ['numeric', 'min:1', 'max:5'],
            'user_id' => ['prohibited']
        ]);

        $review->update($request->all());
        return $this->successResponse(new ReviewResource($review));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();
        return $this->noContent();
    }
}
