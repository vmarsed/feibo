@extends('layouts.default')
@section('title', '所有用户')

@section('content')
<div class="offset-md-2 col-md-8">
    <h2 class="mb-4 text-center">所有用户</h2>
    <div class="list-group list-group-flush">
        @foreach ($users as $user)
{{--             <div class="list-group-item">
                <img class="mr-3" src="{{ $user->gravatar() }}" alt="{{ $user->name }}" width=32>
                <a href="{{ route('users.show', $user) }}">
                    {{ $user->name }}
                </a>
            </div> --}}
            @include('users._user')
        @endforeach
    </div>
    <div class="mt-3">
        {{-- 说是必需用!!写法,这样生成 HTML 链接才不会被转义。 --}}
        {{-- 实际上我试了一下好像都可以,不知道转义个啥,决定不用!!等出错 --}}
        {{-- {!! $users->render() !!} --}}
        {{ $users->render() }}
    </div>
</div>
@stop