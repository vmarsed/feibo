<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
	public function create()
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
}