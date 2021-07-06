<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function gravatar($size='100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    /*
        boot 方法会在用户模型类完成初始化之后进行加载，
        因此我们对事件的监听需要放在该方法中。
    */
    static function boot(){
        parent::boot();
        static::creating(function($user){
            $user->activation_token=Str::random(10);
        });
    }
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }
    public function feed(){
        // return $this->statuses()->orderBy('created_at','desc');

        // 11.6 动态流
        $user_ids = $this->followings->pluck('id')->toArray();
        array_push($user_ids,$this->id); // 将 $this->id 压入数组,因为关注列表没有自己,所以要压入
        // 使用 Laravel 提供的 查询构造器 whereIn 方法
        // 取出所有用户的微博动态并进行倒序排序
        // -------------
        // 我们使用了 Eloquent 关联的 预加载 with 方法，
        // 预加载避免了 N+1 查找的问题，大大提高了查询效率。
        // N+1 问题 的例子可以阅读此文档 Eloquent 模型关系预加载 。
        // https://learnku.com/docs/laravel/6.x/eloquent-relationships/5177#012e7e
        return Status::whereIn('user_id',$user_ids)->with('user')->orderBy('created_at','desc');
    }
    public function followers(){
        // 通过 followers 来获取粉丝关系列表
        return $this->belongsToMany(User::class,'followers','user_id','follower_id');
    }
    public function followings(){
        // 过 followings 来获取用户关注人列表
        return $this->belongsToMany(User::class,'followers','follower_id','user_id');
    }
    public function follow($user_ids){
        if(! is_array($user_ids)){
            $user_ids=compact('user_ids');
        }
        // 在用户的 关注人列表里 增加人
        $this->followings()->sync($user_ids,false);
    }
    public function unfollow($user_ids){
        if(! is_array($user_ids)){
            $user_ids=compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }
    public function isFollowing($user_id){
        return $this->followings()->get()->contains($user_id);
    }
}
