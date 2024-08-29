@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session('msg'))
            <x-alert :type="session('type')" :message="session('msg')" />
            @endif
            @forelse ($posts as $post)
            <div class="card mb-4">
                <div class="card-header">
                    <span># {{ $post->title }}</span>
                </div>
                <div class="card-body">
                    <p>{{ $post->desc }}</p>
                    <div class="d-flex justify-content-end pt-4 font-weight-bold">
                        <a href="{{ url('restore/'.$post->id) }}" class="btn btn-sm btn-success">Restore</a>
                        <form action="{{ url('force/delete/'.$post->id) }}" class="inline-block" method="POST" onsubmit="return confirm('Are you Sure?')">
                            @csrf
                            <button class="btn btn-sm btn-danger ml-1" type="submit">Permanent Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="card mb-4">
                <div class="card-header">
                    No Deleted post found
                </div>
            </div>
            @endforelse

        </div>
    </div>
</div>
@endsection