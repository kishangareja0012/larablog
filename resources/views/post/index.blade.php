@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session('msg'))
                <x-alert :type="session('type')" :message="session('msg')"/>
            @endif    
            @forelse ($posts as $post)
            <div class="card mb-4">
                <div class="card-header">
                    <a href="{{ url('post/view/'.$post->id) }}"># {{ $post->title }}</a>
                </div>
                <div class="card-body">
                    <p>{{ $post->desc }}</p>
                    <div class="d-flex justify-content-end">
                        <a href="{{ url('post/'.$post->id.'/edit') }}" class="btn btn-sm btn-primary mr-2">Edit</a>
                        <form  class="inline-block" action="{{ url('post/'.$post->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="card mb-4">
                <div class="card-header">
                    <a href="{{ url('post/create') }}">No post found</a>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection