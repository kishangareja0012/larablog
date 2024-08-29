@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @auth
            <div class="card mb-4">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    {{ auth()->user()->name.__(' is logged in!') }}
                </div>
            </div>
            @endauth

            @forelse ($posts as $post)
            <div class="card mb-4">
                <div class="card-header">
                    <a href="{{ url('post/view/'.$post->id) }}"># {{ $post->title }}</a>
                </div>
                <div class="card-body">
                    <p>{{ $post->desc }}</p>
                    <div class="text-right">
                        @auth
                        <button onclick="likePost('{{ $post->id }}')" id="liked{{ $post->id }}" class="btn btn-sm {{ in_array($post->id, $likes) ? 'btn-success' : 'btn-secondary' }}">{{ in_array($post->id, $likes) ? 'Liked' : 'Like' }}</button>
                        @endauth
                    </div>
                    <div class="text-right pt-4 font-weight-bold">
                        By <a href="{{ url('profile/'.$post->user->id) }}">{{ $post->user->name }}</a>
                        <p>{{ Carbon\Carbon::parse($post->created_at)->diffForHumans() }}</p>
                        <p>Seen by {{ $post->visit->count() }} User</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="card mb-4">
                <div class="card-header">
                    No post found
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<script>
    function likePost(id) {
        $.ajax({
            url: `{{ url('liked/${id}') }}`,
            type: 'GET',
            success: function(res) {
                if (res) {
                    $(`#liked${id}`).toggleClass("btn-secondary btn-success").html("Liked");
                } else {
                    $(`#liked${id}`).toggleClass("btn-secondary btn-success").html("Like");

                }
            }
        })
    }
</script>
@endsection