@extends('layouts.default')

@section('content')
	@IF(Auth::check())
		<div class="row">
			
			<div class="col-md-8">
				<section class="status_form">
					@include('shared._status_form')
				</section>
				<h4>微博列表</h4>
				<hr>
				@include('shared._feed')
			</div>

			<aside class="col-md-4">
				<section class="user_info">
					@include('shared._user_info',['user'=>Auth::user()])
				</section>

				{{-- 11.3 新增 1 session --}}
				{{-- 把当前用户 Auth::user() 发过去, 子View显示当前用户的关注和粉丝信息 --}}
				<section class="stats mt-2">
					@include('shared._stats',['user'=>Auth::user()])
				</section>
			</aside>

		</div> {{-- end row --}}
	@else
		<div class="jumbotron">
		  <h1>Hello Laravel</h1>
		  <p class="lead">
		    你现在所看到的是 <a href="https://learnku.com/courses/laravel-essential-training">Laravel 入门教程</a> 的示例项目主页。
		  </p>
		  <p>
		    一切，将从这里开始。
		  </p>
		  <p>
		    <a class="btn btn-lg btn-success" href="{{route('signup')}}" role="button">现在注册</a>
		  </p>
		</div>
	@endif
@stop