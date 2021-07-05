<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    function __construct(){
        $this->middleware('guest',[
            'only'=>['create']
        ]);
    }
    // Route::get('login','SessionsController@create')->name('login');
    // Route::post('login','SessionsController@store')->name('login');
    // Route::delete('logout','SessionsController@destroy')->name('logout');

    function create(){
        return view('sessions.create');
        
    }
    function store(Request $request){
        // validate 返回验证过的字段
        // 如果此处只验证 email 则
        // credentials 是个只有 email 的数组
        $credentials = $this->validate($request,[
            'email'=>'required|email|max:255',
            'password'=>'required',
        ]);
        if(Auth::attempt($credentials,$request->has('remember'))){
            if(Auth::user()->activated){
                session()->flash('success','欢迎回来');
                // Auth::user() 方法来获取 当前登录用户 的信息，并将数据传送给路由。
                // users.show 路由需要的参数是用户的 id,这里应该是默认读取id
                $fallback=route('users.show',Auth::user());
                // return redirect()->route('users.show',[Auth::user()]);
                return redirect()->intended($fallback);
            }else{
                Auth::logout();
                session()->flash('warning','你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }
            
        }else{
            session()->flash('danger','很抱歉,您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
        }

    }
    function destroy(){
        Auth::logout();
        session()->flash('success','您已成功退出');
        return redirect('login');
    }
}
