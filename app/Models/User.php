<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'url_avatar',
        'last_activity_at',
        'google_id',
        'email_verified_at',
        'birth_day',
        'country',
        'education',
        'gender',
        'token_verify_email'
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function sendRequests()
    {
        return $this->belongsToMany(User::class, 'friend_ships', 'user_id1', 'user_id2');
    }
    public function receiveRequests()
    {
        return $this->belongsToMany(User::class, 'friend_ships', 'user_id2', 'user_id1');
    }
    public function friends()
    {
        return $this->sendRequests()->whereIn('user_id2', $this->receiveRequests()->pluck('user_id1'));
    }
    public function sendRequestWithoutReceive(){
        return $this->sendRequests()->whereNotIn('user_id2', $this->receiveRequests()->pluck('user_id1'));
    }
    public function receiveWithoutSendRequest(){
        return $this->receiveRequests()->whereNotIn('user_id1', $this->sendRequests()->pluck('user_id2'));
    }
    public function groups()
    {
        return $this->belongsToMany(GroupChat::class, 'group_chat_details', 'user_id', 'group_id')
            ->withPivot('role_id');// Lấy thêm trường role từ bảng trung gian //group->pivot->role_id
    }
    public function messages()
    {
        return $this->hasMany(Message::class, 'user_id_send');
    }
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'user_id');
    }
    public function notifications(){
        return $this->hasMany(Notification::class, 'user_id_receive');

    }
    public function getTempAttribute(){
        //Accessor test
        // user->temp=return ...;
        return "{$this->name} {$this->email}";
    }
    public function setNameAttribute($value)
    {
        // test Mutator
        // cấu hình cách mà Name được lưu
        // $user = User::create([
        //     'first_name' => 'JOHN',
        //     'last_name' => 'DOE',
        // ]);
        $this->attributes['name'] = $value;// ví dụ strtolower($value);
    }
}
