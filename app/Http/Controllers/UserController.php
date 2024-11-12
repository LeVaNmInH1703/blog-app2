<?php

namespace App\Http\Controllers;

use App\Events\requestReloadPage;
use App\Models\Emoji;
use App\Models\FriendShips;
use App\Models\Message;
use App\Models\User;
use App\View\Components\CardInfoUserComponent;
use App\View\Components\UserListComponent;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UserController extends FriendShipController
{
    public $MESSAGECONTROLLER, $POSTCONTROLLER;
    public function __construct(MessageController $messageController, BlogController $postController)
    {
        $this->MESSAGECONTROLLER = $messageController;
        $this->POSTCONTROLLER = $postController;
    }
    public function createUser($request)
    {
        return $this->useTransaction(function () use ($request) {
            $newUser = User::create([
                "email" => $request->email,
                "password" => $request->password,
                'name' => $request->name,
            ]);
            $image = $request->file('fileAvatar');
            $imageAvatarName = '';
            if ($image) {
                $imageAvatarName = 'profile_' . $newUser->id . Str::random(20) . '.' . $image->extension();
                $this->resizeAndSaveImage($imageAvatarName, $image);
            } else {
                if ($request->avatar > 8 || $request->avatar < 1)
                    $request->avatar = 1;
                $imageAvatarName = 'avatar' . $request->avatar . '.png';
            }
            User::find($newUser->id)->update([
                'url_avatar' => asset('images_resize/') . '/' . $imageAvatarName,
            ]);
        });
    }
    public function search(Request $request)
    {
        if (!$request->search) return response('');
        $users = User::where('name', 'LIKE', '%' . strtolower($request->search) . '%')
            ->where('name', '!=', Auth::user()->name)
            ->get();
        return response((string)(new UserListComponent($users,"Search result"))->render());
    }

    public function resizeAndSaveImage($name, $image, $width = 200)
    {
        $ratioWidthHeight = getimagesize($image)[0] / getimagesize($image)[1];
        // resize
        $manager = new ImageManager(new Driver());
        $manager->read($image)->resize($width, $width / $ratioWidthHeight)
            ->save(public_path('images_resize') . '/' . $name);

        //move
        $image->move(public_path('images'), $name);
    }
    public function index()
    {
        $usersMayKnow = User::whereNotIn('id', Auth::user()->sendRequests->pluck('id')->toArray())
            ->whereNotIn('id', Auth::user()->receiveRequests->pluck('id')->toArray())
            ->where('id', '!=', Auth::id())->get();
        return view("pages.users", compact('usersMayKnow'));
    }
    public function profile(User $user = null)
    {
        if (!$user || Auth::id() == $user->id) {
            $user = Auth::user();
        } else {
            //show profile 
        }
        $temp = User::whereIn('id', $user->friends->pluck('id'))
            ->whereIn('id', Auth::user()->friends->pluck('id'));
        $user->commonFriends = $temp->take(7)->get();
        $user->commonFriendsCount = $temp->count();
        $userFiles = collect(); // Khởi tạo một Collection để lưu trữ file
        $user->blogs = $user->blogs->sortByDesc('created_at');
        foreach ($user->blogs as $blog) {
            $this->POSTCONTROLLER->setBlog($blog);
            $userFiles = $userFiles->merge($blog->files); // Thêm các file của blog vào collection
        }
        $user->files = $userFiles;
        return view("pages.profile", compact('user'));
    }
    // ______________edit profile
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'fileAvatar' => ['required', 'image', 'max:10000']
        ]);
        $image = $request->file('fileAvatar');
        if (!$image) return redirect()->back()->withErrors(['fileAvatar' => __('public.Please select an image for your avatar')]);

        $imageAvatarName = basename(parse_url(Auth::user()->url_avatar, PHP_URL_PATH)); // Lấy tên file từ đường dẫn
        if (strpos($imageAvatarName, 'avatar') === 0) {
            $imageAvatarName = 'profile_' . Auth::id() . Str::random(20) . '.' . $image->extension();
            $this->resizeAndSaveImage($imageAvatarName, $image);
            User::find(Auth::id())->update([
                'url_avatar' => asset('images_resize/') . '/' . $imageAvatarName,
            ]);
        } else {
            $this->resizeAndSaveImage($imageAvatarName, $image);
        }
        return redirect()->back()->with('message', __('public.Update success'));
    }
    public function updateName(Request $request)
    {
        $request->validate([
            'authName' => ['required', 'max:255']
        ]);
        User::find(Auth::id())->update([
            'name' => $request->authName
        ]);
        return redirect()->back()->with('message', __('public.Update success'));
    }
    public function updateIntroduce(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'gender' => ['in:male,female,other'], // Xác thực giá trị giới tính
            'education' => ['nullable'],
            'hometown' => ['nullable'],
            'birthDay' => ['date', 'before:today', 'nullable']
        ]);
        User::find(Auth::id())->update([
            'birth_day' => $request->birthDay,
            'gender' => $request->gender,
            'country' => $request->country,
            'education' => $request->education,
        ]);
        return redirect()->back()->with('message', __('public.Update success'));
    }
}
