<?php

namespace App\Policies;

use App\Models\User;
use App\Models\GiftItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class GiftItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_gift::item');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GiftItem $giftItem): bool
    {
        return $user->can('view_gift::item');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_gift::item');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GiftItem $giftItem): bool
    {
        return $user->can('update_gift::item');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GiftItem $giftItem): bool
    {
        return $user->can('delete_gift::item');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_gift::item');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, GiftItem $giftItem): bool
    {
        return $user->can('force_delete_gift::item');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_gift::item');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, GiftItem $giftItem): bool
    {
        return $user->can('restore_gift::item');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_gift::item');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, GiftItem $giftItem): bool
    {
        return $user->can('replicate_gift::item');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_gift::item');
    }
}
