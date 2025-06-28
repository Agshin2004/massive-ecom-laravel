<?php

namespace App\Policies;

use App\Enums\Role;
use App\Enums\SellerStatus;
use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function before(User $user): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }


    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        if ($user->role !== Role::Seller->value) {
            return Response::deny('Only sellers can create products.');
        }

        if (!$user->seller) {
            return Response::deny('You must have a seller profile to create products.');
        }

        if ($user->seller->status !== SellerStatus::Approved->value) {
            return Response::deny('Your seller account must be approved to create products.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): Response
    {
        if ($user->role !== Role::Seller->value) {
            return Response::deny('Only sellers can update products.');
        }

        if (!$user->seller) {
            return Response::deny('You must have a seller profile to update products.');
        }

        if ($user->seller->status !== SellerStatus::Approved->value) {
            return Response::deny('Your seller account must be approved to update products.');
        }

        if ($product->seller_id !== $user->seller->id) {
            return Response::deny('You can only update your own products.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): Response
    {
        if ($user->role !== Role::Seller->value) {
            return Response::deny('Only sellers can delete products.');
        }

        if (!$user->seller) {
            return Response::deny('You must have a seller profile to delete products.');
        }

        if ($user->seller->status !== SellerStatus::Approved->value) {
            return Response::deny('Your seller account must be approved to delete products.');
        }

        if ($product->seller_id !== $user->seller->id) {
            return Response::deny('You can only delete your own products.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return false;
    }
}
