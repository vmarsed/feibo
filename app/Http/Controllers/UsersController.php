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
            'except'=>['index','show','create','store']
        ]);
        /*
            auth:{
                已登录用户:可访问 所有方法
                未登录用户:可访问 show, create, store
                测试: 不登录访问show feibo.test/users/1 => 可以访问
                测试: 不登录访问edit feibo.test/users/1/edit => redirect to login

                因为已登录 已登录用户也可以访问 注册页create
                为此加上下面的 middleware.guest
                表示, create 方法仅 guest 访客可访问
                已登录用户无法访问
                测试: 登录后访问 feibo.test/signup

                注意,guest middleware是这个页面仅访客才能访问
                而不是,访客只能访问这个页面

                而 auth middleware 就真的是限制 访客可以访问哪些方法 和 登录后可访问哪些方法
            }
        */
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
    function index()
    {
        // $users=User::all();//这是不分页的写法
        $users=User::paginate(6);
        return view('users.index',compact('users'));
    }
    function destroy(User $user)
    {
        引用删除策略
        $this->authorize('destroy', $user);
        // 首先会根据路由发送过来的用户 id 进行数据查找，
        // 查找到指定用户之后再调用 Eloquent 模型提供的 delete 方法
        // 对用户资源进行删除
        $user->delete();
        // 成功删除后在页面顶部进行消息提示。
        session()->flash('success','成功删除用户!');
        // 最后将用户重定向到上一次进行删除操作的页面，即用户列表页。
        return back();
    }
}