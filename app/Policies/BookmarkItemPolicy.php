<?php

namespace App\Policies;

use App\User;
use App\BookmarkItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookmarkItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the bookmarkItem.
     *
     * @param  \App\User  $user
     * @param  \App\BookmarkItem  $bookmarkItem
     * @return mixed
     */
    public function view(User $user, BookmarkItem $bookmarkItem)
    {
        //
    }

    /**
     * Determine whether the user can create bookmarkItems.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the bookmarkItem.
     *
     * @param  \App\User  $user
     * @param  \App\BookmarkItem  $bookmarkItem
     * @return mixed
     */
    public function update(User $user, BookmarkItem $bookmarkItem)
    {
        //
    }

    /**
     * Determine whether the user can delete the bookmarkItem.
     *
     * @param  \App\User  $user
     * @param  \App\BookmarkItem  $bookmarkItem
     * @return mixed
     */
    public function delete(User $user, BookmarkItem $bookmarkItem)
    {
        //
    }
}
