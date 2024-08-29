@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card mb-4">
                <div class="card-header"><p class="mb-0 font-weight-bold">{{ $user->name }}</p> {{ $user->email }}</div>   
                @auth    
                    @if(!($user->id == auth()->id()))
                    <a href="{{ url('message/'.$user->id) }}" class="btn btn-sm btn-link">Message</a>
                    @endif
                @endauth
            </div>

            @forelse ($user->post as $post)
            <div class="card mb-4">
                <div class="card-header">
                    <a href="{{ url('post/view/'.$post->id) }}"># {{ $post->title }}</a>
                </div>
                <div class="card-body">
                    <p>{{ $post->desc }}</p>
                    <div class="text-right">
                        <a href="{{ url('post/view/'.$post->id) }}" class="btn btn-sm btn-primary">View</a>
                        @auth
                        @if(!($post->user->id == auth()->id()))
                        <button onclick="likePost({{ $post->id }})" id="liked{{ $post->id }}" class="btn btn-sm {{ in_array($post->id, $likes) ? 'btn-success' : 'btn-secondary' }}">{{ in_array($post->id, $likes) ? 'Liked' : 'Like' }}</button>
                        @endif
                        @endauth
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
    function likePost($id) {
        $.ajax({
            url: `{{ url('liked/${$id}') }}`,
            type: 'GET',
            success: function(res) {
                if (res) {
                    $(`#liked${$id}`).toggleClass("btn-secondary btn-success").html("Liked");
                } else {
                    $(`#liked${$id}`).toggleClass("btn-secondary btn-success").html("Like");

                }
            }
        })
    }
</script>
@endsection