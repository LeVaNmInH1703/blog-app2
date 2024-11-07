<?php

namespace App\Http\Controllers;

use App\Events\HasNewMessageEvent;
use App\Models\FileMessage;
use App\Models\FriendShips;
use App\Models\GroupChat;
use App\Models\GroupChatDetail;
use App\Models\LastMessages;
use App\Models\Message;
use App\Models\NewMessage;
use App\Models\RoleInBlog;
use App\Models\RoleInGroupChat;
use App\Models\SeenMessageDetial;
use App\Models\User;
use App\View\Components\ChatItemComponent;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Str;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class MessageController extends FriendShipController
{
    public $GROUPCONTROLLER;
    public function __construct(GroupChatController $groupChatController)
    {
        $this->GROUPCONTROLLER = $groupChatController;
    }
    public function countGroupHasNewMessage()
    {
        $result = 0;
        foreach (Auth::user()->groups as $group)
            // dd(($group->aaaa));
            if ($group->pivot->role_id != 4 && $this->checkNewMessage($group->id, Auth::id()))
                $result++;
        return $result;
    }
    public function message()
    {
        $groups = $this->getGroups();
        $friends = $this->getFriend();
        foreach ($groups as $group) {
            $group->timeAgo = strtr(now()->subSeconds(strtotime(now()) - strtotime($group->last_activity_at))->diffForHumans(), ['before' => 'ago']);
            $group->hasNewMessage = $group->pivot->role_id != 4 && $this->checkNewMessage($group->id, Auth::id());
        }
        $newGroup = session('newGroup');
        if ($newGroup) {
            session(['messageWithContact' => $newGroup->getId()]);
        }
        $group = GroupChat::find(session('messageWithContact'));
        if (
            $group &&
            $group->isChatWithSomeone &&
            !$this->isFriendWithAnotherInGroup($group)
        )
            session()->forget('messageWithContact');
        return view("pages.message", compact("groups", 'friends'));
    }
    public function takeLastMessage($group_id, $user_id)
    {
        return LastMessages::where([
            ['group_id', $group_id],
            ['user_id', $user_id]
        ]);
    }
    public function checkNewMessage($group_id, $user_id)
    {
        $latestMessage = $this->getLatestMessageInGroup($group_id);
        $latestMessageOfAuth = $this->takeLastMessage($group_id, $user_id)->first();
        if ($latestMessage == null)
            return false;
        return $latestMessage->id > ($latestMessageOfAuth->last_message_id ? $latestMessageOfAuth->last_message_id : -1);
    }
    public function getLatestMessageInGroup($group_id)
    {
        return Message::where([['group_id_receive', $group_id]])->latest()->first();
    }
    public function getMessageInGroup($group_id)
    {
        return Message::where([['group_id_receive', $group_id]]);
    }
    public function updateSeenMessageInGroup($group_id)
    {
        $group = GroupChat::find($group_id);
        if (!$group || !$this->GROUPCONTROLLER->isInGroup(User::find(Auth::id()), $group))
            return abort(404);
        return $this->useTransaction(function () use ($group) {
            $latestMessage = $this->getLatestMessageInGroup($group->id);
            $this->takeLastMessage($group->id, Auth::id())->update([
                'last_message_id' => $latestMessage ? $latestMessage->id : null,
            ]);
        });
    }
    public function isShowTimeOfMessage($previousMessage, $message)
    {
        return $previousMessage->user_id_send != $message->user_id_send || $previousMessage->created_at->diffInSeconds($message->created_at) > 60 * 3;
    }
    public function chatHistory(GroupChat $group, $lang = 15)
    {
        if (!$group || !$this->GROUPCONTROLLER->isInGroup(User::find(Auth::id()), $group)) {
            return abort(404);
        }
        if ($group->isChatWithSomeone) {
            $this->setGroupAsUser($group);
        }
        $isBlocked = $group->users->firstWhere('id', Auth::id())->pivot->role_id == 4;
        if ($isBlocked)
            $lang = 0;
        session(['messageWithContact' => $group->id]);
        $group->messages = Message::where([['group_id_receive', $group->id]])->orderBy('created_at', 'desc')->take($lang)->get()->reverse();
        $group->timeAgo = strtr(now()->subSeconds(strtotime(now()) - strtotime($group->last_activity_at))->diffForHumans(), ['before' => 'ago']);
        $previousMessage = null;
        foreach ($group->messages as $message) {
            if ($previousMessage !== null)
                $message->isShowTime = $this->isShowTimeOfMessage($previousMessage, $message);
            $previousMessage = $message;
        }
        $group->isBlocked = $isBlocked;
        $this->updateSeenMessageInGroup($group->id);
        $isCanContinueRender = $lang < $this->getMessageInGroup($group->id)->count();
        // dd($user);
        if ($isBlocked)
            $isCanContinueRender = false;
        return [
            'view' => view('components.chat-history-component', compact('group', 'isCanContinueRender'))->render(),
            'isCanContinueRender' => $isCanContinueRender
        ];
    }
    /**
     * check 2 người trong group riêng có phải bạn không
     * @param \App\Models\GroupChat $group
     * @return bool
     */
    public function isFriendWithAnotherInGroup(GroupChat $group)
    {
        return Auth::user()->friends->contains('id', $group->users->firstWhere('id', '!=', Auth::id())->id);
    }
    private function scanForVirus($filePath)
    {
        return 'OK';
    }
    private function isHasVirus($files)
    {
        $isVirus = false;
        if ($files)
            foreach ($files as $file)
                $isVirus |= $this->scanForVirus($file->getRealPath()) != 'OK';

        return $isVirus;
    }
    public function sendMessage(Request $request)
    {
        if ($request->message == "" && $request->mediaFiles == '' && $request->otherFiles == '')
            return;
        // dd($request->all());
        $request->validate([
            'mediaFiles.*' => 'file|mimes:jpeg,png,gif,mp4,webm,ogg|max:40690',
            'otherFiles.*' => 'file|max:40690'
        ]);

        if ($this->isHasVirus($request->file('mediaFiles')))
            return redirect()->back()->withErrors(['mediaFiles' => __('public.The media files are danger, can not upload')]);

        if ($this->isHasVirus($request->file('otherFiles')))
            return redirect()->back()->withErrors(['otherFiles' => __('public.The files are danger, can not upload')]);

        $message = new Message;
        $user = User::find(Auth::id());
        $group = GroupChat::find(session('messageWithContact'));
        if (
            !($user &&
                $group &&
                $group->users->contains('id', $user->id) &&
                $group->users->firstWhere('id', Auth::id())->pivot->role_id != 4)
        ) return;
        if ($group->isChatWithSomeone && !$this->isFriendWithAnotherInGroup($group))
            return;
            
        if ($this->createMessage($request, $message))
            event(new HasNewMessageEvent($message));

        if (!$message->group->isChatWithSomeone)
            $message->group->update([
                'last_activity_at' => now(),
            ]);
        return response()->json([
            'status'=>200
        ]);
    }
    public function updateLastActivity($request)
    {
        return $this->useTransaction(function () use ($request) {
            GroupChat::find($request->group_id_receive)->update([
                'last_activity_at' => now()
            ]);
        });
    }
    public function getChatItemPatialView(Request $request, Message $message)
    {
        if (!$message || !($message->group->users->contains('id', Auth::id())))
            return abort(404);
        $id = '';
        $previousMessage = Message::where('group_id_receive', $message->group_id_receive)
            ->where('created_at', '<', $message->created_at)
            ->orderBy('created_at', 'desc')
            ->first();
        $message->isShowTime = $previousMessage && $this->isShowTimeOfMessage($previousMessage, $message);

        return (new ChatItemComponent($message))->render();
    }
    public function createMessage(Request $request, &$newMessage)
    {
        return $this->useTransaction(function () use ($request, &$newMessage) {
            $newMessage = Message::create([
                'user_id_send' => Auth::id(),
                'group_id_receive' => session('messageWithContact'),
                'content' => $request->message
            ]);
            $files = $request->file('otherFiles');
            if ($files)
                foreach ($files as $file) {
                    if (!$file) continue;
                    $fileMessageName = 'message_file_' . $newMessage->id . Str::random(20) . '.' . $file->extension();
                    $file->move(public_path('files'), $fileMessageName);
                    FileMessage::create([
                        'file_name' => $fileMessageName,
                        'message_id' => $newMessage->id,
                        'old_name' => $file->getClientOriginalName()
                    ]);
                }

            $files = $request->file('mediaFiles');
            if ($files)
                foreach ($files as $file) {
                    $mimeType = $file->getMimeType();
                    if (!$file) continue;
                    if (str_starts_with($mimeType, 'image/')) {
                        $fileMessageName = 'message_image_' . $newMessage->id . Str::random(20) . '.' . $file->extension();

                        // resize chú ý dùng đúng driver
                        $manager = new ImageManager(new Driver());
                        $temp = $manager->read($file);
                        if ($temp->width() > 300)
                            $temp->resize(300, 300 * $temp->height() / $temp->width());
                        $temp->save(public_path('images_resize') . '/' . $fileMessageName);

                        //move
                        $file->move(public_path('images'), $fileMessageName);
                        FileMessage::create([
                            'file_name' => $fileMessageName,
                            'message_id' => $newMessage->id,
                            'old_name' => $file->getClientOriginalName()
                        ]);
                    } else if (str_starts_with($mimeType, 'video/')) {
                        $fileMessageName = 'message_video_' . $newMessage->id . Str::random(20) . '.' . $file->extension();
                        $file->move(public_path('videos'), $fileMessageName);
                        FileMessage::create([
                            'file_name' => $fileMessageName,
                            'message_id' => $newMessage->id,
                            'old_name' => $file->getClientOriginalName()
                        ]);
                    }
                }
        });
    }
}
