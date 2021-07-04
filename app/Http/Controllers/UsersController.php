<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class UsersController extends controller
{
	public function create()
	{
		return view('users.create');
	}
}