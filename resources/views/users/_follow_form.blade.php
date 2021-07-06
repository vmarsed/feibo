@can('follow',$user)
    <div class="text-center mt-2 mb-4">
        @if(Auth::user()->isFollowing($user->id))
            <form action="{{ route('followers.destroy',$user->id) }}" method="post">
                <button type="submit" class="btn btn-sm btn-outline-primary">取消关注</button>
                {{  csrf_field() }}
                {{ method_field('DELETE') }}
            </form>
        @else
            <form action="{{ route('followers.store',$user->id) }}" method="post">
                <button type="submit" class="btn btn-sm btn-primary">关注</button>
                {{  csrf_field() }}
            </form>
        @endif
    </div>
@endcan