<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NewFollower;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FollowController extends Controller
{
    public function toggle(Request $request, User $user)
    {
        $authUser = $request->user();

        // Нельзя подписаться на себя
        if ($authUser->id === $user->id) {
            return back();
        }

        $isFollowing = $authUser->following()->where('following_id', $user->id)->exists();

        if ($isFollowing) {
            $authUser->following()->detach($user->id);
        } else {
            $authUser->following()->attach($user->id);
            // Уведомить пользователя, на которого подписались
            $user->notify(new NewFollower($authUser));
        }

        return back();
    }

    public function followers(Request $request, string $username): Response
    {
        $profileUser = User::where('username', $username)->firstOrFail();
        $authUser    = $request->user();

        $myFollowingIds = $authUser
            ? $authUser->following()->pluck('users.id')->toArray()
            : [];

        $users = $profileUser->followers()
            ->paginate(20)
            ->through(function (User $u) use ($myFollowingIds, $authUser) {
                $u->is_following = $authUser && in_array($u->id, $myFollowingIds);
                return $u;
            });

        return Inertia::render('Profile/Followers', [
            'profileUser'    => $profileUser,
            'users'          => $users,
            'tab'            => 'followers',
            'followersCount' => $profileUser->followers()->count(),
            'followingCount' => $profileUser->following()->count(),
        ]);
    }

    public function following(Request $request, string $username): Response
    {
        $profileUser = User::where('username', $username)->firstOrFail();
        $authUser    = $request->user();

        $myFollowingIds = $authUser
            ? $authUser->following()->pluck('users.id')->toArray()
            : [];

        $users = $profileUser->following()
            ->paginate(20)
            ->through(function (User $u) use ($myFollowingIds, $authUser) {
                $u->is_following = $authUser && in_array($u->id, $myFollowingIds);
                return $u;
            });

        return Inertia::render('Profile/Followers', [
            'profileUser'    => $profileUser,
            'users'          => $users,
            'tab'            => 'following',
            'followersCount' => $profileUser->followers()->count(),
            'followingCount' => $profileUser->following()->count(),
        ]);
    }
}
