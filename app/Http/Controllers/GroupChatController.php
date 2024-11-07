<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupChatRequest;
use App\Http\Requests\GroupRequest;
use App\Models\GroupChat;
use App\Models\GroupChatDetail;
use App\Models\LastMessages;
use App\Models\Message;
use App\Models\RoleInGroupChat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Imagick\Driver;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use PHPUnit\Framework\Attributes\Group;

class GroupChatController extends FriendShipController
{
    public $ROLEINGROUPCHATCONTROLLER;
    public function __construct(RoleInGroupChatController $roleInGroupChatController)
    {
        $this->ROLEINGROUPCHATCONTROLLER = $roleInGroupChatController;
    }


    /**
     * to update attibutes of group
     * @param mixed $group_id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGroup(GroupChat $group, Request $request)
    {
        if (!$group || !$this->isManager($group, User::find(Auth::id())))
            abort(404);
        $imageAvatarName = 'groupDefault.png';
        $image = $request->file('avatar');
        if ($image) {
            // dd('a');
            $imageAvatarName = 'group_' . $group->id . Str::random(20) . '.' . $image->extension();

            //resize
            $manager = new ImageManager(new Driver());
            $manager->read($image)->resize(250, 250)->save(public_path('images_resize') . '/' . $imageAvatarName);

            //move
            $image->move(public_path('images'), $imageAvatarName);
        }
        $this->useTransaction(function () use ($group, $request, $imageAvatarName) {
            GroupChat::find($group->id)->update([
                'name' => $request->nameGroup,
                'url_avatar' => asset('images_resize/') . '/' . $imageAvatarName
            ]);
        });
        return redirect()->back()->with('message', __('public.Update success'));
        // $group = Group::find($group_id);
    }
    public function chatWith(User $user)
    {
        if (!$user && !Auth::user()->friends->contains('id', $user->id) || Auth::id() == $user->id)
            abort(404);
        $name =$this->createNameGroup($user,Auth::user());
        // dd($name);
        $newGroup = GroupChat::where([['name', $name]])->first();
        if (!GroupChat::where([["isChatWithSomeone", true], ['name', $name]])->exists()) {
            $this->useTransaction(function () use ($name, $user, &$newGroup) {
                $newGroup = GroupChat::create([
                    "isChatWithSomeone" => true,
                    'name' => $name
                ]);
                LastMessages::create([
                    'user_id' => Auth::id(),
                    'group_id' => $newGroup->id
                ]);
                LastMessages::create([
                    'user_id' => $user->id,
                    'group_id' => $newGroup->id
                ]);
                $this->ROLEINGROUPCHATCONTROLLER->checkAndMake();
                //add to group
                $this->createGroupChatDetail($newGroup->id, Auth::id(), 'admin');
                // dd($name, $user_id,$group);
                $this->createGroupChatDetail($newGroup->id, $user->id, 'admin');
            });
        }
        return redirect()->route('message')->with('newGroup', $newGroup);
    }
    
    public function createGroupChatDetail($group_id, $user_id, $role_name)
    {
        $this->useTransaction(function () use ($group_id, $user_id, $role_name) {
            GroupChatDetail::create([
                'group_id' => $group_id,
                'user_id' => $user_id,
                'role_id' => RoleInGroupChat::where('name', $role_name)->first()->id,
            ]);
        });
    }
    public function addNewGroupChat(GroupChatRequest $request)
    {
        if (!$this->createGroupChat($request))
            return redirect()->back()->with("message", __('public.create fail'));
        return redirect()->back();
    }
    public function createGroupChat(Request $request)
    {
        $this->ROLEINGROUPCHATCONTROLLER->checkAndMake();
        $ids = explode(' ', $request->idsChecked);
        return $this->useTransaction(function () use ($request, $ids) {
            $groupChat = GroupChat::create([
                "name" => $request->nameGroup ?? $request->nameGroup2,
                'url_avatar' => asset('images_resize/groupDefault.png'),
                'last_activity_at' => now(),
            ]);
            foreach ($ids as $id)
                $this->addMemberToGroup($id, $groupChat->id);
        });
    }
    public function addMemberToGroup($user_id, $group_id)
    {
        return $this->useTransaction(function () use ($user_id, $group_id) {
            GroupChatDetail::create([
                "user_id" => $user_id,
                'group_id' => $group_id,
                'role_id' => RoleInGroupChat::where('name', ($user_id != Auth::id() ? 'member' : 'admin'))->first()->id,
            ]);
            LastMessages::create([
                'user_id' => $user_id,
                'group_id' => $group_id
            ]);
        });
    }
    public function removeMemberInGroup($user_id, $group_id)
    {
        return $this->useTransaction(function () use ($user_id, $group_id) {
            GroupChatDetail::where([["user_id", $user_id], ['group_id', $group_id]])->delete();
            LastMessages::where([["user_id", $user_id], ['group_id', $group_id]])->delete();
        });
    }
    public function aboutGroup(GroupChat $group)
    {
        $this->ROLEINGROUPCHATCONTROLLER->checkAndMake();
        $friends = Auth::user()->friends;
        foreach ($friends as $friend) {
            $friend->isInGroup = $this->isInGroup($friend, $group);
        }
        foreach ($group->users as $user) {
            $user->roleId = $this->getRoleId($group, $user);
        }
        Auth::user()->roleId = $this->getRoleId($group, User::find(Auth::id()));
        return view('pages.showGroup', compact('group', 'friends'));
    }
    public function disovleGroup(GroupChat $group)
    {
        if (!$group)
            abort(404);
        if (!$this->isAdmin($group, User::find(Auth::id())))
            return redirect()->back()->with('message', __("public.Only the admin can disovle group"));
        $result = $this->useTransaction(function () use ($group) {
            GroupChatDetail::where('group_id', $group->id)->delete();
            LastMessages::where('group_id', $group->id)->delete();
            $group->delete();
        });
        if ($result)
            return redirect()->route('home')->with('message', __('public.Disovle group success'));
        else
            return redirect()->back()->with('message', __('public.Disovle group fail'));
    }
    public function addMember(GroupChat $group,User $user)
    {
        if (!$group || !$user)
            abort(404);
        $this->ROLEINGROUPCHATCONTROLLER->checkAndMake();
        $this->addMemberToGroup($user->id, $group->id);
        $this->makeMessageInfo($group,__('public.Someone was added by someone', ['name1' => Auth::user()->name,'name2' => $user->name]));    
        return redirect()->back();
    }
    public function kickMember(GroupChat $group, User $user)
    {
        if (!$group || !$user)
            abort(404);
        if (!$this->isAdmin($group, User::find(Auth::id())))
            return redirect()->back()->with('message', __("public.Only the admin can kick member"));
        $this->removeMemberInGroup($user->id, $group->id);
        $this->makeMessageInfo($group,__('public.Someone was removed from the group', ['name' => $user->name]));    
        return redirect()->back();
    }
    public function makeMessageInfo(GroupChat $group,$content){
        Message::create([
            'user_id_send' => Auth::id(),
            'group_id_receive' => $group->id,
            'type'=>'info',
            'content' => $content
        ]);
    }
    public function leaveGroup(GroupChat $group)
    {
        if (!$group || !$this->isInGroup(User::find(Auth::id()), $group))
            abort(404);
        if ($this->removeMemberInGroup(Auth::id(), $group->id))
            return redirect()->route('home')->with('message', __("public.Leave group success"));
        $this->makeMessageInfo($group,__('public.Someone left the group', ['name' => Auth::user()->name]));    
        return redirect()->back()->with('message', __("public.Leave group fail"));
    }
    public function isAdmin(GroupChat $group, User $user)
    {
        return $this->getRoleId($group, $user) <= 1;
    }
    public function isManager(GroupChat $group, User $user)
    {
        return $this->getRoleId($group, $user) <= 2;
    }

    public function getRoleId(GroupChat $group, User $user)
    {
        if (!$this->isInGroup($user, $group))
            return '';
        return $group->users->firstWhere('id', $user->id)->pivot->role_id;
    }
    public function isInGroup(User $user, GroupChat $group)
    {
        return $group->users->contains('id', $user->id);
    }
    public function updateRoleForUser(GroupChat $group, User $user, $roleName)
    {
        return $this->useTransaction(function () use ($group, $user, $roleName) {
            GroupChatDetail::where([['group_id', $group->id], ['user_id', $user->id]])->update([
                'role_id' => RoleInGroupChat::where('name', $roleName)->first()->id,
            ]);
        });
    }
}
