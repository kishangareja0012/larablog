<?php

namespace App\Http\Controllers;

use App\Events\LikePost;
use App\Events\ReadMessage;
use App\Events\ReceiveMessage;
use App\Events\SendMessage;
use App\Http\Requests\Post as RequestsPost;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Post;
use App\Models\Subcomment;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Auth::user()->post;
        return view('post.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('post.create');
    }

    public function home()
    {
        $posts = Post::with('user')->where('user_id', '!=', Auth::id())->get();
        $likes = array();
        if (Auth::check()) {
            $likes = explode(",", Auth::user()->like_posts);
        }
        return view('home', compact('posts', 'likes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestsPost $request)
    {
        $user = User::find(Auth::id());
        $post = new Post();
        $post->title = $request->title;
        $post->desc = $request->desc;
        $post->post_desc = $request->content;
        // dd($request->all());
        $run = $user->post()->save($post);
        if ($run) {
            return redirect('post')->with('msg', 'Post successfully added....')->with('type', 'success');
        } else {
            return back()->with('msg', 'Something went wrong....')->with('type', 'danger');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Auth::user()->post->find($id);
        if ($post) {
            return view('post.edit', compact('post'));
        } else {
            return redirect(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RequestsPost $request, $id)
    {
        $post = Auth::user()->post->find($id);
        if ($post) {
            $post->title = $request->title;
            $post->desc = $request->desc;
            $post->post_desc = $request->content;
            $post->save();
            return redirect('post')->with('msg', 'Post successfully updated....')->with('type', 'success');
        } else {
            return back()->with('msg', 'Something went wrong....')->with('type', 'danger');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Post::destroy($id);

        return back()->with('msg', 'Post Deleted')->with('type', 'danger');
    }

    public function postView(Request $request, $id)
    {

        $post = Post::with('user', 'comment.user', 'comment.subcomment.user')->find($id);
        if (count($post->visit)) {
            $ip = $post->visit->where('ip', '!=', $request->ip())->first();
            // dd($ip);
            if ($ip == NULL) {
                $ip = false;
            } else {
                $ip = true;
            }
        } else {
            $ip = true;
        }

        if ($ip) {
            $visit = new Visitor();
            $visit->ip = $request->ip();
            $post->visit()->save($visit);
        }
        return view('post.view', compact('post'));
    }

    public function likePost($id)
    {
        $user = User::whereId(Auth::id())->first();
        $message = ucfirst($user->name) . " liked your Post";
        $user_id = Post::find($id)->user_id;
        if ($user->like_posts == null) {
            $user->like_posts = $id;
            $user->save();
            event(new LikePost($message, $user_id, "like", $id));
            return true;
        } else {
            $post_ids = explode(",", $user->like_posts);
            if (in_array($id, $post_ids)) {
                $key = array_search($id, $post_ids);
                unset($post_ids[$key]);
                $user->like_posts = implode(",", $post_ids);
                $user->save();
                return false;
            } else {
                array_push($post_ids, $id);
                $user->like_posts = implode(",", $post_ids);
                $user->save();
                event(new LikePost($message, $user_id, "like", $id));
                return true;
            }
        }
    }

    public function profile($id)
    {
        $user = User::with('post')->find($id);
        if (Auth::check()) {
            $likes = explode(",", Auth::user()->like_posts);
        } else {
            $likes = array();
        }
        return view('post.profile', compact('user', 'likes'));
    }

    public function likePostView()
    {
        $likePosts = explode(",", Auth::user()->like_posts);

        $likePosts = Post::with('user')->whereIn('id', $likePosts)->get();
        $likes = array();
        if (Auth::check()) {
            $likes = explode(",", Auth::user()->like_posts);
        }
        return view('post.like', ['posts' => $likePosts, 'likes' => $likes]);
    }

    public function softDeleteShow()
    {
        $posts = Post::whereRaw("user_id = " . Auth::id())->onlyTrashed()->get();

        return view('post.delete', compact('posts'));
    }

    public function softDeleteRestore(int $id)
    {
        $trashedData = Post::whereRaw("user_id = " . Auth::id())->onlyTrashed()->find($id);
        if ($trashedData) {
            $trashedData->restore();
            return back()->with('type', 'success')->with('msg', 'Post Restored....');
        }
        return back()->with('type', 'danger')->with('msg', 'Something went wrong....');
    }

    public function forceDelete($id)
    {
        $trashedData = Post::whereRaw("user_id =" . Auth::id())->onlyTrashed()->find($id);
        if ($trashedData) {
            $trashedData->forceDelete();
            return back()->with('type', 'success')->with('msg', 'Post Deleted permanently....');
        }
        return back()->with('type', 'danger')->with('msg', 'Something went wrong....');
    }

    public function commentSubmit(Request $request)
    {
        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->post_id = $request->post_id;
        $comment->comment = $request->comment;
        $comment->save();

        return response()->json($comment);
    }

    public function commentDelete(Request $request, $id)
    {
        $trashed = Comment::find($id);
        if ($trashed->user_id == Auth::id()) {
            $trashed->delete();
            return back()->with('type', 'success')->with('msg', 'Comment Deleted....');
        }
        return back()->with('type', 'danger')->with('msg', 'Something went wrong....');
    }

    public function replyComment(Request $request)
    {
        $comment = new Subcomment();
        $comment->user_id = Auth::id();
        $comment->comment_id = $request->comment_id;
        $comment->reply_comment = $request->reply;
        $comment->save();

        return response()->json($comment);
    }

    public function subcommentDelete($id)
    {
        $delete = Subcomment::whereRaw("user_id = " . Auth::id())->first();
        if ($delete) {
            $delete->delete();
            return back()->with('type', 'success')->with('msg', 'Comment Deleted....');
        }
        return back()->with('type', 'danger')->with('msg', 'Something went wrong....');
    }

    public function messageView($id)
    {
        $user = User::find($id);

        $unreadMessage = Message::where(function ($query) use ($id) {
            return $query->where('sender_id', $id)->where('receiver_id', Auth::id());
        })->where('status', 0)->pluck('id')->toArray();
        event(new ReadMessage($id, Auth::id(), $unreadMessage));

        Message::whereIn('id', $unreadMessage)->update([
            'status' => 1
        ]);

        $messages = Message::where(function ($query) use ($id) {
            return $query->where('sender_id', Auth::id())->where('receiver_id', $id);
        })->orWhere(function ($query) use ($id) {
            return $query->where('sender_id', $id)->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'ASC')->get();
        return view('post.chat', compact('user', 'messages'));
    }

    public function sendMessage(Request $request)
    {
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->id,
            'message' => $request->message
        ]);
        event(new SendMessage(Auth::id(), $request->id, $message->id));
        event(new LikePost(ucfirst(Auth::user()->name) . " sent you Message", $request->id, "message", Auth::id()));

        return response()->json([
            'id' => $message->id,
            'message' => $message->message,
            'createDate' => date_format($message->created_at, 'g:i A'),
            'status' => $message->status
        ]);
    }

    public function receiveMessage(Request $request)
    {
        $message = Message::find($request->message_id);
        if($request->isRead) {
            $message->status = 1;
            $message->save();
            event(new ReceiveMessage($message->sender_id, $message->receiver_id, $message->id, $message->status));
        }
        return response()->json([
            'message' => $message->message,
            'createDate' => date_format($message->created_at, 'g:i A')
        ]);
    }

    public function chatView(Request $request)
    {
        if (isset($request->name) && !empty($request->name)) {
            $users = User::where('id', '!=', Auth::id())->where('name', 'LIKE', '%' . $request->name . '%')->get();
        } else {
            $users = User::where('id', '!=', Auth::id())->get();
        }

        foreach ($users as $user) {
            $id = $user->id;
            $user->unreadMessage = Message::where('sender_id', $id)->where('receiver_id', Auth::id())->where('status', 0)->count();
            $user->lastMessage = Message::select('*')->where(function ($query) use ($id) {
                return $query->where('sender_id', Auth::id())->where('receiver_id', $id);
            })->orWhere(function ($query) use ($id) {
                return $query->where('sender_id', $id)->where('receiver_id', Auth::id());
            })->orderBy('created_at', 'DESC')->first();
        }
        
        return view('post.chat-list', compact('users'));
    }
}
