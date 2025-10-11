<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AdsCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdsCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_ads::category');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AdsCategory $adsCategory): bool
    {
        return $user->can('view_ads::category');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_ads::category');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AdsCategory $adsCategory): bool
    {
        return $user->can('update_ads::category');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AdsCategory $adsCategory): bool
    {
        return $user->can('delete_ads::category');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_ads::category');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, AdsCategory $adsCategory): bool
    {
        return $user->can('force_delete_ads::category');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_ads::category');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, AdsCategory $adsCategory): bool
    {
        return $user->can('restore_ads::category');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_ads::category');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, AdsCategory $adsCategory): bool
    {
        return $user->can('replicate_ads::category');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_ads::category');
    }
}
