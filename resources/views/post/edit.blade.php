@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session('msg'))
            <x-alert :type="session('type')" :message="session('msg')" />
            @endif
            <div class="card">
                <div class="card-header">Add new Post</div>
                <div class="card-body">
                    <form action="{{ url('post/'.$post->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title" value="{{ (old('title') ? old('title') : $post->title) }}" class="form-control" placeholder="Enter title...." />
                        </div>
                        @error('title')
                        <x-alert type="danger" :message="$message" />
                        @enderror
                        <div class="form-group">
                            <label for="desc">Description</label>
                            <textarea name="desc" id="title" class="form-control" placeholder="Enter title...." rows="3">{{ (old('desc') ? old('desc') : $post->desc) }}"</textarea>
                        </div>
                        @error('desc')
                        <x-alert type="danger" :message="$message" />
                        @enderror
                        <div class="form-group">
                            <label for="desc">Description</label>
                            <textarea id="example" rows="10" name="content" id="title" class="form-control" placeholder="Enter title...." rows="2">{{ (old('content') ? old('content') : $post->post_desc) }} </textarea>
                        </div>
                        @error('content')
                        <x-alert type="danger" :message="$message" />
                        @enderror
                        <button class="btn btn-primary" type="submit">Update Post</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/froala-editor@3.1.0/js/froala_editor.pkgd.min.js"></script>
<script>
    var editor = new FroalaEditor('#example', {
        height: '400px'
    })
</script>
@endsection