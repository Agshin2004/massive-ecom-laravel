<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromoRequest;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promos = Promo::all();
        return $this->successResponse($promos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePromoRequest $request)
    {
        $allowedFields = $request->only([
            'code',
            'discount_percent',
            'discount_amount',
            'valid_till',
            'usage_limit',
            'per_usage_limit',
        ]);

        if (count($request->all()) !== count($allowedFields)) {
            abort(400, 'Unexpected fields');
        }

        $promo = Promo::create($request->all());

        return $this->successResponse([
            'promo' => $promo
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Promo $promo)
    {
        return $this->successResponse([
            'promo' => $promo
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promo $promo)
    {
        if (empty($request->all())) {
            abort(400, 'At least on field must be present');
        }

        // Note: not checking if is_active present since later I will implemented only admins update/create promos
        $promo->update($request->all());
        return $this->successResponse([
            'promo' => $promo
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promo $promo)
    {
        $promo->delete();

        return $this->noContent();
    }
}
