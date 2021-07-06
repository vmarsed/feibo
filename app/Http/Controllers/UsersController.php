<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    function __construct(){
        // 注册限流, 10次每小时
        $this->middleware('throttle:10,60',[
            'only'=>['store']
        ]);
        $this->middleware('auth',[
            // except 除了以下方法，其他都要先登录
            // only 仅这些方法需要登录
            // 未登录的，默认重定向到 /login 页面
            // 注意，此时未登录的编辑不了，已登录的是可以改别人的 用 /users/id/edit
            'except'=>['show','create','store','index','confirmEmail']
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
    function create(){
        return view('users.create');
    }
    function show(User $user){
        // return view('users.show',compact('user'));
        $statuses = $user->statuses()->orderBy('created_at', 'desc')->paginate(30);
        return view('users.show', compact('user', 'statuses'));
    }
    function store(Request $request){
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
        
        // Auth::login($user);
        // session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
        // return redirect()->route('users.show',[$user]);
        $this->sendEmailConfirmationTo($user);
        session()->flash('success','验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');

    }
    function edit(User $user){
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }
    function update(User $user, Request $request){
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
    function index(){
        // $users=User::all();//这是不分页的写法
        $users=User::paginate(6);
        return view('users.index',compact('users'));
    }
    function destroy(User $user){
        // 引用删除策略
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
    function confirmEmail($token){
        /*
            where 方法接收两个参数，第一个参数为要进行查找的字段名称，
            第二个参数为对应的值，查询结果返回的是一个数
        */
        $user=User::where('activation_token',$token)->firstOrFail();
        $user->activated=true;
        $user->activation_token=null;
        $user->save();
        Auth::login($user);
        session()->flash('success','恭喜你,激活成功!');
        return redirect()->route('users.show',[$user]);   
        return view('emails.confirm',compact($user));
    }
    protected function sendEmailConfirmationTo($user){
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'summer@example.com';
        $name = 'Summer';
        $to = $user->email;
        $subject = "感谢注册 Feibo 应用！请确认你的邮箱。";

        Mail::send($view,$data,function($message)use($from,$name,$to,$subject){
            // $message->from($from,$name)->to($to)->subject($subject); // log邮件写法
            $message->to($to)->subject($subject);
        });
    }

    // 11.4 制作 粉丝列表页 followers, 关注人列表页 followings
    // Route::get('/users/{user}/followers','UsersController@followers')->name('users.followers');
    // Route::get('/users/{user}/followings','UsersController@followings')->name('users.followings');
    // 以下粉丝列表和关注人列表都是用的同一个 view 只是参数不同
    public function followers(User $user){
        $users = $user->followers()->paginate(30);
        $title = $user->name.'的粉丝';
        return view('users.show_follow',compact('users','title'));
    }
    public function followings(User $user){
        $users = $user->followings()->paginate(30);
        $title = $user->name.'关注的人';
        return view('users.show_follow',compact('users','title'));
    }

}