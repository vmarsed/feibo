<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Illuminate\Support\Str;
use DB;
use Mail;
use Carbon\Carbon;

class PasswordController extends Controller
{
    function showLinkRequestForm(){
        return view('auth.passwords.email');
    }
    function sendResetLinkEmail(Request $request){
        $request->validate(['email'=>'required|email']);
        $email=$request->email;

        $user = User::where("email",$email)->first();

        if(is_null($user)){
            session()->flash('danger','邮箱未注册');
            return redirect()->back()->withInput();
        }

        $token=hash_hmac('sha256',Str::random(40),config('app.key'));


        DB::table('password_resets')->updateOrInsert(['email'=>$email],[
            'email'=>$email,
            'token'=>Hash::make($token),
            'created_at'=>new Carbon,
        ]);



        Mail::send('emails.reset_link',compact('token'),function($message)use($email){
            $message->to($email)->subject("忘记密码");
        });
        // 注意, 发给用户的是原 token
        // 数据存储的是在原 token 上处理过的 token , Hash::make($token)

        session()->flash('success','重置邮件发送成功,请查收');
        return redirect()->back();
    }
    function showResetForm(Request $request){
        $token = $request->route()->parameter('token');
        return view('auth.passwords.reset',compact('token'));
    }
    function reset(Request $request){
        $request->validate([
            'token'=>'required',
            'email'=>'required|email',
            'password'=>'required|confirmed|min:8',
        ]);

        $email=$request->email;
        $token=$request->token;
        $expires=60*10;

        $user=User::where("email",$email)->first();
        if(is_null($user)){
            session()->flash('danger','邮箱未注册');
            return redirect()->back->withInput();
        }

        $record=(array) DB::table('password_resets')->where('email',$email)->first();
        if($record){
            if(Carbon::parse( $record['created_at'] )->addSeconds($expires)->isPast()){
                session()->flash('danger','链接已过期,请重新尝试');
                return redirect()->back();
            }
            if( !Hash::check($token,$record['token']) ){
                session()->flash('danger','令牌错误');
                return redirect()->back();
            }
            $user->update(['password'=>bcrypt($request->password)]);
            session()->flash('success','密码重置成功,请使用新密码登录');
            return redirect()->route('login');
        }

        session()->flash('danger','未发起重置请求');
        return redirect()->back();

        

        

        
    }
}
