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
		$this->validate($request,[
			'name'=>'required|unique:users|max:50',
			'email'=>'required|email|unique:users|max:225',
			'password'=>'required|confirmed|min:6',
		]);
		return;
	}
}