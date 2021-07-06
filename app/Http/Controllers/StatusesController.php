<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Status;

class StatusesController extends Controller
{
    public function __construct(){
        // store 和 destroy 动作将用于对微博的创建和删除，
        // 这两个动作都需要用户登录，
        // 因此让我们借助 Auth 中间件来为这两个动作添加过滤请求。
        $this->middleware('auth');
    }
    public function store(Request $request){
        $this->validate($request,[
            'content'=>'required|max:140'
        ]);

        Auth::user()->statuses()->create([
            'content'=>$request['content']
        ]);
        session()->flash('success','发布成功!');
        return redirect()->back();
    }
    public function destroy(Status $status)
    {
        // blade 中使用 can 利用授权策略
        // 控制器中使用 authroize() 方法,参数倒是一致
        // 做删除授权的检测，不通过会抛出 403 异常。
        $this->authorize('destroy', $status);
        // 调用 Eloquent 模型的 delete 方法对该微博进行删除
        $status->delete();
        session()->flash('success', '微博已被成功删除！');
        return redirect()->back();
    }


}
