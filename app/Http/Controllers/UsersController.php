<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
    function __construct()
    {
        $this->middleware('auth',[
            // except 除了以下方法，其他都要先登录
            // only 仅这些方法需要登录
            // 未登录的，默认重定向到 /login 页面
            // 注意，此时未登录的编辑不了，已登录的是可以改别人的 用 /users/id/edit
            'except'=>['show','create','store']
        ]);
        $this->middleware('guest',[
            'only'=>['create']
        ]);
    }
    function create()
    {
        return view('users.create');
    }
    function show(User $user)
    {
        return view('users.show',compact('user'));
    }
    function store(Request $request)
    {
        # validate 方法是父类提供的
        # 注意 unique:users 用的表名(复数),而不是模式名(单数)
        $this->validate($request,[
            'name'=>'required|unique:users|max:50',
            'email'=>'required|email|unique:users|max:225',
            'password'=>'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);

        Auth::login($user);
        session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show',[$user]);

    }
    function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }
    function update(User $user, Request $request)
    {
        $this->authorize('update', $user);
        $this->validate($request,[
            'name'=>'required|max:50',
            'password'=>'nullable|confirmed|min:6'
        ]);
        $data=[];
        $data['name']=$request->name;
        if($request->password){
            $data['password']=bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success','个人资料更新成功！');
        return redirect()->route('users.show',$user->id);
    }
}