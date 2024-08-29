@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    {{ $user->name }}
                </div>
                <div class="card-body position-relative overflow-auto" id="messageArea" style="height:75vh;">
                    <div id="messageContent">
                        @forelse ($messages as $message)
                        <div id="messageBox{{$message->id}}" class="{{ ($message->sender_id == auth()->id()) ? 'text-right' : '' }}">
                            <div class="message-bar">
                                <p class="mb-1 pb-1 message">{{ $message->message }}</p>
                                <p class="text-secondary text-right mb-0 px-1" style="font-size:10px;">
                                    @if($message->sender_id == auth()->id())
                                    <i class="fas fa-check mr-2 {{ ($message->status == 1) ? 'text-success' : '' }}" id="isRead"></i>
                                    @endif
                                    {{ date_format($message->created_at, 'g:i A') }}
                                </p>
                            </div>
                        </div>
                        @empty
                        <div id="noResultStatus">
                            <p class="mb-0 text-center">No Conversation found</p>
                        </div>
                        @endforelse
                    </div>
                    <div id="typingIndicate" style="display:none">
                        <div class="message-bar messageTyping">
                            <p class="mb-1 pb-1 message">Typing...</p>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex">
                        <input type="text" id="messageInputBox" class="form-control" placeholder="Type message here..." name="message">
                        <button id="messageSendBox" class="btn btn-sm btn-primary ml-3">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        scrollToBottom();
    })

    function scrollToBottom() {
        let boxHeight = 0;
        $("[id*='messageBox']").each(function() {
            boxHeight += $(this).height();
        })
        $("#messageArea").scrollTop(boxHeight)
    }
</script>
<script>
    
</script>
@section('scripts')
<script>

    // Typing Indicate
    let typingInterval;
    Echo.private('type').listenForWhisper('typing', (e) => {
        if (e.toId == "{{ auth()->id() }}" && e.fromId == "{{ $user->id }}") {
            clearInterval(typingInterval)
            let scrollHeight = $("#messageArea").scrollTop()
            $("#typingIndicate").show()
            $("#messageArea").scrollTop(scrollHeight + 49)
            typingInterval = setTimeout(function() {
                $("#typingIndicate").hide()
            }, 1500);
        }
    })

    $("#messageInputBox").on("keyup", function() {
        Echo.private('type').whisper('typing', {
            fromId: "{{ auth()->id() }}",
            toId: "{{ $user->id }}"
        })
    })

    // Sending Message 
    $("#messageSendBox").on('click', function() {
        const inputValue = $("#messageInputBox").val().trim()
        if (inputValue != '') {
            $.ajax({
                url: "{{ url('send/message')}}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    message: inputValue,
                    id: "{{ $user->id }}"
                },
                success: function(res) {
                    $("#noResultStatus").remove();
                    $('#messageContent').append(`
                        <div id="messageBox${res.id}" class="text-right">
                            <div class="message-bar">
                                <p class="mb-1 pb-1 message">${res.message}</p>
                                <p class="text-secondary text-right mb-0 px-1" style="font-size:10px;">
                                    <i class="fas fa-check mr-2 ${ (res.status == 1) ? 'text-success' : '' }" id="isRead"></i>
                                    ${res.createDate}
                                </p>
                            </div>
                        </div>
                    `)
                    scrollToBottom();
                }
            })
        }
    })

    //Receive Message
    Echo.private('send-message.{{ auth()->id() }}').listen('SendMessage', (e) => {
        if (e.from_id == "{{ $user->id }}" && e.to_id == "{{ auth()->id() }}") {
            $("#noResultStatus").remove();
            $.ajax({
                url: "{{ url('receive-message')}}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    message_id: e.message_id,
                    isRead: true
                },
                success: function(res) {
                    $('#messageContent').append(`
                        <div id="messageBox">
                            <div class="message-bar">
                                <p class="mb-1 pb-1 message">${res.message}</p>
                                <p class="text-secondary text-right mb-0 px-1" style="font-size:10px;">
                                    ${res.createDate}
                                </p>
                            </div>
                        </div>
                    `)
                    scrollToBottom();
                }
            })
        }
    })

    //Receive Message Status
    Echo.private('receive-message.{{ auth()->id() }}').listen('ReceiveMessage', (e) => {
        $(`#messageBox${e.message_id} #isRead`).addClass('text-success')
    })


    Echo.private('read-message.{{ auth()->id() }}').listen('ReadMessage', (e) => {
        $.each(e.unreadMessages, function(value, index) {
            $(`#messageBox${index} #isRead`).addClass('text-success')
        })
    })
</script>
@endsection
<script>
</script>
@endsection