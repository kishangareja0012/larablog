@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            @forelse ($posts as $post)
            <div class="card mb-4">
                <div class="card-header">
                    <a href="{{ url('post/view/'.$post->id) }}"># {{ $post->title }}</a>
                </div>
                <div class="card-body">
                    <p>{{ $post->desc }}</p>
                    <div class="text-right pt-4 font-weight-bold">
                        <div class="d-flex justify-content-end align-items-center">
                            By <a href="{{ url('profile/'.$post->user->id) }}" class="mr-4">{{ $post->user->name }}</a>
                            <button onclick="likePost('{{ $post->id }}')" id="liked{{ $post->id }}" class="btn btn-sm {{ in_array($post->id, $likes) ? 'btn-success' : 'btn-secondary' }}">{{ in_array($post->id, $likes) ? 'Liked' : 'Like' }}</button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="card mb-4">
                <div class="card-header">
                    No Like post found
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