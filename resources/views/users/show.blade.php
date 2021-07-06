@extends('layouts.default')
@section('title',$user->name)
@section('content')
    <div class="row">
        <div class="offset-md-2 col-md-8">
            <div class="offset-md-2 col-md-8">

                <section class="user_info">
                    @include('shared._user_info',['user'=>$user])
                </section>

                {{-- U11.5 Begin --}}
                @if(Auth::check())
                    @include('users._follow_form')
                @endif
                {{-- U11.5 End --}}

                {{-- U11.3 Begin --}}
                <section class="stats mt-2">
                    @include('shared._stats', ['user' => $user])
                </section>
                <hr>
                {{-- U11.3 End --}}

                <section class="status">
                  @if ($statuses->count() > 0)
                    <ul class="list-unstyled">
                      @foreach ($statuses as $status)
                        @include('statuses._status')
                      @endforeach
                    </ul>
                    <div class="mt-5">
                      {!! $statuses->render() !!}
                    </div>
                  @else
                    <p>没有数据！</p>
                  @endif
                </section>

            </div>
        </div>
    </div> {{-- end row --}}
@stop