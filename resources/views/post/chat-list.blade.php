@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    Find Users
                </div>
                <form action="">
                    <div class="card-body">
                        <input type="text" class="form-control" name="name" value="{{ request()->name }}" placeholder="Enter name here....">
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-primary">Find</button>
                    </div>
                </form>
            </div>
            <div class="card">
                <div class="card-header">Chats</div>

                <div class="card-body p-0">
                    @foreach ($users as $user)
                    <a href="{{ url('message/'.$user->id) }}" id="userChat{{ $user->id }}" class="d-flex align-items-center justify-content-between py-2 px-3 border-bottom overflow-hidden">
                        <div class="d-flex align-items-center">
                            <div>
                                <img width="50" src="https://thumbs.dreamstime.com/b/default-avatar-profile-icon-vector-social-media-user-image-182145777.jpg" alt="">
                            </div>
                            <div class="pl-4">
                                <p class="mb-1 font-weight-bold">{{ $user->name }}</p>
                                <p class="text-secondary mb-0 messageBoxArea" id="messageBoxArea">
                                    @if(isset($user->lastMessage->message))
                                    @if($user->lastMessage->receiver_id != auth()->id())
                                    <i class="fa fa-check mr-1 {{ ($user->lastMessage->status == 1) ? 'text-success' : '' }}" id="typeMessage{{ $user->lastMessage->id }}" style="font-size:13px"></i>
                                    @endif
                                    {{ $user->lastMessage->message }}
                                    @else
                                    No conversation found
                                    @endif
                                </p>
                                <p class="mb-0 font-weight-bold text-success d-none" id="messageTypeArea">typing...</p>
                            </div>
                        </div>
                        <div>
                            @if($user->unreadMessage)
                            <p class="badge badge-success mb-0 rounded-circle" id="unreadMessageCount" style="font-size:13px;z-index:100">{{ $user->unreadMessage }}</p>
                            @else
                            <p class="badge badge-success mb-0 rounded-circle invisible" id="unreadMessageCount" style="font-size:13px;z-index:100">0</p>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts')
<script>
    // Typing Indicate
    let userTypingInterval;
    Echo.private('type').listenForWhisper('typing', (e) => {
        if (e.toId == "{{ auth()->id() }}") {
            clearTimeout(userTypingInterval)

            $(`#userChat${e.fromId} #messageBoxArea`).addClass('d-none');
            $(`#userChat${e.fromId} #messageTypeArea`).addClass('d-block');

            userTypingInterval = setTimeout(function() {
                $(`#userChat${e.fromId} #messageBoxArea`).removeClass('d-none');
                $(`#userChat${e.fromId} #messageTypeArea`).removeClass('d-block');
            }, 1500);
        }
    })

    Echo.private('send-message.{{ auth()->id() }}').listen('SendMessage', (e) => {
        if (e.to_id == "{{ auth()->id() }}") {
            $.ajax({
                url: "{{ url('receive-message')}}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    message_id: e.message_id
                },
                success: function(res) {
                    $(`#userChat${e.from_id} #messageBoxArea`).html(res.message);
                    let count = $(`#userChat${e.from_id} #unreadMessageCount`).html();
                    $(`#userChat${e.from_id} #unreadMessageCount`).removeClass('invisible').html(parseInt(count) + 1)
                }
            })
        }
    })

    Echo.private('read-message.{{ auth()->id() }}').listen('ReadMessage', (e) => {
        $.each(e.unreadMessages, function(value, index) {
            $(`#typeMessage${index}`).addClass('text-success')
        })
    })
</script>
@endsection
@endsection