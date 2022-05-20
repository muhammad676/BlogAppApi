<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CommentsController extends Controller
{
    public function create(Request $request)
    {
        $comment = new Comment;
        $comment->user_id = Auth::user()->id;
        $comment->post_id = $request->id;
        $comment->comment = $request->comment;
        $comment->save();
        $comment->user;
        $comment->user->followings;
        $comment->user->followers;
        $comment->user['followingCount']= count($comment->user->followings);
        $comment->user['followerCount']= count($comment->user->followers);
        return response()->json([
            'success' => true,
            'message' => "Comment Added Successfully",
            'new_comment' => $comment,
            'status_code' => Response::HTTP_OK
        ], Response::HTTP_OK);

    }

    public function update(Request $request)
    {
        $comment = Comment::find($request->id);

        //check if user is editing his comment
        if (Auth::user()->id != $comment->user_id) {
            return response()->json([
                'success' => false,
                'message' => "Unauthorized Access",
                'status_code' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        } else {
            $comment->comment = $request->comment;
            $comment->update();
            return response()->json([
                'success' => true,
                'message' => "Comment Updated Successfully",
                'status_code' => Response::HTTP_OK
            ], Response::HTTP_OK);
        }
    }

    public function delete(Request $request)
    {
        $comment = Comment::find($request->id);

        //check if user is editing his comment
        if (Auth::user()->id != $comment->user_id) {
            return response()->json([
                'success' => false,
                'message' => "Unauthorized Access",
                'status_code' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        } else {
            $comment->delete();
            return response()->json([
                'success' => true,
                'message' => "Comment Deleted Successfully",
                'status_code' => Response::HTTP_OK
            ], Response::HTTP_OK);
        }
    }

    public function comments(Request $request)
    {
        $comments = Comment::where('post_id',$request->id)->get();
        //show user of each comment
        if($comments){
            foreach ($comments as $comment)
            {
                $comment->user;
                $comment->user->followings;
                $comment->user->followers;
                $comment->user['followingCount']= count($comment->user->followings);
                $comment->user['followerCount']= count($comment->user->followers);
            }
            return response()->json([
                'success' => true,
                'comments' => $comments,
                'status_code' => Response::HTTP_OK
            ], Response::HTTP_OK);
        }else{
            return response()->json([
                'success' => true,
                'message' => "Post Not Found",
                'status_code' => Response::HTTP_NO_CONTENT
            ], Response::HTTP_NO_CONTENT);
        }


    }
}
