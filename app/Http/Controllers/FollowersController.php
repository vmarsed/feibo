<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;

class FollowersController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function store(User $user){
        // blade 中使用 can 利用授权策略
        // 控制器中使用 authroize() 方法,参数倒是一致
        // 做删除授权的检测，不通过会抛出 403 异常。
        $this->authorize('follow',$user);

        if(! Auth::user()->isFollowing($user->id)){
            Auth::user()->follow($user->id);
        }
        return redirect()->route('users.show',$user->id);
    }
    public function destroy(User $user){
        $this->authorize('follow',$user);
        if(Auth::user()->isFollowing($user->id)){
            Auth::user()->unfollow($user->id);
        }
        return redirect()->route('users.show',$user->id);
    }
}
