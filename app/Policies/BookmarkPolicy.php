<?php

namespace App\Policies;

use App\User;
use App\bookmark;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookmarkPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the bookmark.
     *
     * @param  \App\User  $user
     * @param  \App\bookmark  $bookmark
     * @return mixed
     */
    public function view(User $user, bookmark $bookmark)
    {
        //
    }

    /**
     * Determine whether the user can create bookmarks.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the bookmark.
     *
     * @param  \App\User  $user
     * @param  \App\bookmark  $bookmark
     * @return mixed
     */
    public function update(User $user, bookmark $bookmark)
    {
        return $user->id == $bookmark->uid;
    }

    /**
     * Determine whether the user can delete the bookmark.
     *
     * @param  \App\User  $user
     * @param  \App\bookmark  $bookmark
     * @return mixed
     */
    public function delete(User $user, bookmark $bookmark)
    {
        return $user->id == $bookmark->uid;
    }
}
