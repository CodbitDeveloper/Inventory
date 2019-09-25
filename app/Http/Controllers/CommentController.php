<?php

namespace App\Http\Controllers;

use App\Comment;
use App\WorkOrder;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = Comment::with('user', 'work_order')->get();

        return view('all-comment')->with('comments', $comments);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'comment'       => 'required',
            'work_order_id' => 'required',
            'user_id'       => 'required'
        ]);

        $comment = new Comment();
        
        $comment->id            = md5($request->comment.microtime());
        $comment->comment       = $request->comment;
        $comment->work_order_id = $request->work_order_id;
        $comment->user_id       = $request->user_id;
        $comment->type          = $request->type;

        if($comment->save()) {
            $work_order = WorkOrder::with(['users', function($q) use ($comment){
                $q->where("id", "<>", $comment->user_id);
            }], 'user')->where('id', $comment->work_order_id)->first();

            $primary_user = $work_order->user;
            $work_order->users->push($primary_user);

            /*foreach($work_order->users as $user){
                if($user->id != $comment->user_id){
                    $user->notify(new CommentCreated($comment));
                }
            }*/
            Notification::send($work_order->users, new CommentCreated($comment));

            return response()->json([
                'error'   => false,
                'data'    => $comment,
                'message' => 'Comment created successfully'
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Could not create comment. Try again!'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'comment'       => 'required',
            'work_order_id' => 'required', 
            'user_id'       => 'required' 
        ]);

        $comment = Comment::where('id', $request->id)->first();

        $comment->comment       = $request->comment;
        $comment->work_order_id = $request->work_order_id;
        $comment->user_id       = $request->user_id;

        if($comment->update()) {
            return response()->json([
                'error'   => false,
                'data'    => $comment,
                'message' => 'Comment updated successfully'
            ]);
        }

        return response()->json([
            'error'   => true,
            'message' => 'Could not update comment. Try Again!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $status = $comment->delete();

        if($status) {
            return response()->json([
                'error'   => false,
                'message' => 'Comment deleted successfully.'
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Could not delete comment. Try Again!'
            ]);
        }
    }
}
