<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AdAsset;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdAssetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_ad::asset');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AdAsset $adAsset): bool
    {
        return $user->can('view_ad::asset');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_ad::asset');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AdAsset $adAsset): bool
    {
        return $user->can('update_ad::asset');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AdAsset $adAsset): bool
    {
        return $user->can('delete_ad::asset');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_ad::asset');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, AdAsset $adAsset): bool
    {
        return $user->can('force_delete_ad::asset');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_ad::asset');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, AdAsset $adAsset): bool
    {
        return $user->can('restore_ad::asset');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_ad::asset');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, AdAsset $adAsset): bool
    {
        return $user->can('replicate_ad::asset');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_ad::asset');
    }
}
