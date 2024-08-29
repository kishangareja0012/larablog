@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session('msg'))
            <x-alert :type="session('type')" :message="session('msg')" />
            @endif
            <h3 class="pb-4">{{ $post->title }}</h3>
            <p class="pb-4">{{ $post->desc }}</p>
            <div style="max-height:30vh;overflow:auto" class="mb-5">
                {!! $post->post_desc !!}
            </div>

            <div class="p-3 border d-flex justify-content-between mb-4" id="commentSection"><span>{{ $post->visit->count() }} Views</span></div>
            <div class="p-3 border d-flex justify-content-between mb-4" id="commentSection"><span>{{ count($post->comment) }} Comments</span> @auth<a href="javascript:void(0)" id="addComment">+ Add Comment</a> @endauth</div>

            @auth
            <!-- <div class="card mb-4" id="commentBody">
                <div class="card-body">
                    <input type="text" name="comment" class="form-control mb-4" id="commentInput" placeholder="Write comment here...." />
                    <button id="commentSubmitButton" class="btn btn-sm btn-primary">Add</button>
                </div>
            </div> -->
            @endauth

            @forelse ($post->comment as $comment)
            <div class="card mb-4" id="comment{{ $comment->id }}">
                <div class="card-body border border-bottom" id="commentBodyData{{ $comment->id }}">{{ $comment->comment }}</div>
            
                @foreach ($comment->subcomment as $subcomment)
                <div class="card-body pl-4 border border-bottom" id="comment{{ $comment->comment_id }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="">{{ $subcomment->reply_comment }}</span>
                        @if($subcomment->user->id == auth()->id())
                        <div class="d-flex align-items-center"><span>reply by <a href="{{ url('profile/'.$subcomment->user->id) }}" class="btn-link">You</a></span>
                            <form action="{{ url('subcomment/delete/'.$subcomment->id) }}" method="post"> @csrf <button type="submit" class="btn text-danger">delete</button> </form>
                        </div>
                        @else
                        <span>reply by <a href="{{ url('profile/'.$subcomment->user->id) }}" class="btn-link">{{ $subcomment->user->name }}</a></span>
                        @endif
                        </span>
                    </div>
                    <p class="mb-0">{{ Carbon\Carbon::parse($subcomment->created_at)->diffForHumans() }}</p>
                </div>
                @endforeach
                <div class="card-footer">
                    <div class="d-flex @auth justify-content-between @else justify-content-end @endauth" id="replyBody{{ $comment->id }}">
                        @auth
                        <a href="javascript:void(0)" onclick="replyComment('{{ $comment->id }}')" class="btn-link">Reply</a>
                        @endauth

                        @if ($comment->user->id == auth()->id())
                        <div class="d-flex align-items-center">
                            <form action="{{ url('comment/delete/'.$comment->id) }}" method="POST">
                                @csrf
                                <button class="btn py-0 text-danger">Delete</button>
                            </form>
                            <a href="javascript:void(0)" class="text-dark">by You</a>
                        </div>
                        @else
                        <a href="{{ url('profile/'.$comment->user->id) }}" class="text-dark">by {{ $comment->user->name }}</a>
                        @endif
                    </div>
                    <p class="mb-0">{{ Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="card mb-4">
                <div class="card-body">No Comment found</div>
            </div>
            @endforelse
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/froala-editor@3.1.0/js/froala_editor.pkgd.min.js"></script>
<script>
    var editor = new FroalaEditor('#example', {
        height: '400px'
    })
</script>

<script type="text/template" id="addCommentHtml">
    <div class="card mb-4" id="commentBody">
        <div class="card-body">  
            <input type="text" name="comment" class="form-control mb-4" id="commentInput" placeholder="Write comment here...."/>
            <button id="commentSubmitButton" class="btn btn-sm btn-primary">Add</button>
        </div>
    </div>
</script>

<script>
    $("#addComment").on("click", function() {
        if (typeof $("#commentBody").html() == "undefined") {
            $("#commentSection").after(`
            <div class="card mb-4" id="commentBody">
                <div class="card-body">  
                    <input type="text" name="comment" class="form-control mb-4" id="commentInput" placeholder="Write comment here...."/>
                    <button id="commentSubmitButton" class="btn btn-sm btn-primary">Add</button>
                </div>
            </div>
            `);
        }
    })


    $(document).on("click", "#commentSubmitButton", function() {
        var CSRF_TOKEN = $("meta[name='csrf-token']").attr('content');
        console.log(CSRF_TOKEN);
        $.ajax({
            url: "{{ url('comment/submit') }}",
            method: 'POST',
            data: {
                _token: CSRF_TOKEN,
                comment: $("#commentInput").val(),
                post_id: "{{ $post->id }}"
            },
            success: function(res) {
                console.log(res);
                $("#commentBody").after(
                    `<div class="card mb-4 commentRes" id="comment${res.id}">
                        <div class="card-body border border-bottom" >${res.comment}</div>
                        <div class="card-footer d-flex justify-content-end">
                            <div class="d-flex align-items-center">
                            <form action="{{ url('comment/delete/${res.id}') }}" method="POST">
                                @csrf
                                <button class="btn py-0 text-danger">Delete</button>
                            </form>
                            <a href="javascript:void(0)" class="text-dark">by You</a>
                        </div>
                    </div>
                    
                `);
                setTimeout(function() {
                    $(`#comment${res.id}`).removeClass('commentRes');
                }, 2000)
            }
        })
    });

    function replyComment($id) {
        if (typeof $("#replyInput").html() == "undefined") {
            $(`#replyBody${$id}`).after(`
            <div class="d-flex mt-2" id="replyContent${$id}">
            <input type="hidden" id="commentId" value="${$id}" class="form-control"/>
            <input type="text" id="replyInput" placeholder="Write reply here...." class="form-control"/>
            <button class="btn btn-primary" id="submitReply">Comment</button>
            </div>
            `)
        }
    }

    $(document).on("click", "#submitReply", function() {
        console.log($("#replyInput").val(), $("meta[name='csrf-token']").attr('content'));

        $.ajax({
            url: "{{ url('comment/reply') }}",
            method: 'POST',
            data: {
                _token: $("meta[name='csrf-token']").attr('content'),
                reply: $("#replyInput").val(),
                comment_id: $("#commentId").val()
            },
            success: function(res) {
                $(`#commentBodyData${res.comment_id}`).after(`
                    <div class="card-body border border-bottom d-flex justify-content-between" id="comment${res.comment_id}">
                        <span class="pl-4">${res.reply_comment}</span>
                        <div class="d-flex align-items-center"><span>reply by <a href="{{ url('profile/${res.user_id}') }}" class="btn-link">You</a></span>
                            <form action="{{ url('subcomment/delete/${res.id}') }}" method="post"> @csrf <button type="submit" class="btn text-danger">delete</button> </form>
                        </div>
                    </div>
                `)

                $(`#replyContent${res.comment_id}`).remove();
            }
        })
    })
</script>
@endsection