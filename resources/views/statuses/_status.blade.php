    
<li class="media mt-4 mb-4">
    <a href="{{ route('users.show', $user->id )}}">
        <img src="{{ $user->gravatar() }}" alt="{{ $user->name }}" class="mr-3 gravatar"/>
    </a>
    <div class="media-body">
        <h5 class="mt-0 mb-1">
            {{-- diffForHumans()  将日期进行友好化处理--}}
            {{-- 比如将具体的时候改为 XX 小时前 --}}
           {{--  Carbon 是 PHP 知名的日期和时间操作扩展，
            diffForHumans 是 Carbon 对象提供的方法 --}}
            {{ $user->name }} <small> / {{ $status->created_at->diffForHumans() }}</small>
        </h5>
        {{ $status->content }}
    </div>
</li>