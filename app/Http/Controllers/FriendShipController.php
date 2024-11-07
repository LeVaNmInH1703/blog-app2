<?php

namespace App\Http\Controllers;

use App\Events\requestReloadPage;
use App\Models\FriendShips;
use App\Models\GroupChat;
use App\Models\GroupChatDetail;
use App\Models\Message;
use App\Models\User;
use App\View\Components\CardInfoUserComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FriendShipController extends Controller
{
    public function createFriendShip($user_id1, $user_id2)
    {
        return $this->useTransaction(function () use ($user_id1, $user_id2) {
            FriendShips::create([
                "user_id1" => $user_id1,
                "user_id2" => $user_id2,
            ]);
        });
    }
    public function deleteFriendShip($user_id1, $user_id2)
    {
        // dd($user_id1, $user_id2);
        return $this->useTransaction(function () use ($user_id1, $user_id2) {
            FriendShips::where([["user_id1", $user_id1], ['user_id2', $user_id2]])->delete();
        });
    }
    public function addFriend(Request $request, User $user)
    {
        if (!$user) abort(403);
        if ($user->id != Auth::id() && !Auth::user()->friends->contains('id', $user->id)) {
            $this->createFriendShip(Auth::id(), $user->id);
            event(new requestReloadPage('channelReloadUsersPage', $user->id));
        }
        return response(str((new CardInfoUserComponent($user,false))->render()));
    }
    public function acceptRequest(Request $request, User $user)
    {
        if (!$user) abort(403);
        if ($user->id != Auth::id()) {
            if (Auth::user()->receiveRequests->contains('id', $user->id)) {
                $this->createFriendShip(Auth::id(), $user->id);
            } else
                return redirect()->back()->with('message', __("public.This request has been cancelled"));
        }
        return response(str((new CardInfoUserComponent($user,false))->render()));
    }
    public function cancelRequest(Request $request, User $user)
    {
        if (!$user) abort(403);
        if ($user->id != Auth::id() && Auth::user()->sendRequests->contains('id', $user->id)) {
            $this->deleteFriendShip(Auth::id(), $user->id);
            event(new requestReloadPage('channelReloadUsersPage', $user->id));
        }
        return response(str((new CardInfoUserComponent($user,false))->render()));
    }
    public function unfriend(Request $request, User $user)
    {
        if (!$user) abort(403);
        if ($user->id != Auth::id() && Auth::user()->friends->contains('id', $user->id)) {
            $this->deleteFriendShip(Auth::id(), $user->id);
            $this->deleteFriendShip($user->id, Auth::id());
            event(new requestReloadPage('channelReloadUsersPage', $user->id));
        }
        return redirect()->back();
    }
    public function setGroupAsUser(&$group, $otherUser = null)
    {
        if (!$otherUser)
            $otherUser = $group->users->firstWhere('id', '!=', Auth::id());
        $group->url_avatar = $otherUser->url_avatar;
        $group->name = $otherUser->name;
        $group->last_activity_at = $otherUser->last_activity_at;
        $group->user_id = $otherUser->id;
    }
    public function getGroups()
    {
        return Auth::user()->groups->filter(function ($group) {
            if ($group->isChatWithSomeone) {
                $otherUser = $group->users->firstWhere('id', '!=', Auth::id());
                $this->setGroupAsUser($group, $otherUser);
                return Auth::user()->friends->contains('id', $otherUser->id);
            }
            return true;
        })->map(function ($group) {
            $newestMessage = $this->newestMessageInGroup($group->id);
            $group->latest_message_time = $newestMessage ? $newestMessage->created_at : null;
            return $group;
        })->sortByDesc('latest_message_time');
    }
    public function newestMessageInGroup($group_id)
    {
        return Message::where([
            ['group_id_receive', $group_id],
        ])->latest()->first();
    }
    public function getFriend()
    {
        return Auth::user()->friends->map(function ($friend) {
            $nameGroup = $this->createNameGroup($friend, Auth::user());
            $group = GroupChat::where([['name', $nameGroup]])->first();
            $newestMessage = null;
            if ($group)
                $newestMessage = $this->newestMessageInGroup($group->id);
            $friend->latest_message_time = $newestMessage ? $newestMessage->created_at : null;
            return $friend;
        })->sortByDesc('latest_message_time');
    }
    public function createNameGroup($user1, $user2)
    {
        return ($user1->id + $user2->id) . ($user1->id * $user2->id);
    }
}
