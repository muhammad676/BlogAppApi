<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LikesControllerler extends Controller
{
    public function likes(Request $request)
    {
        $like = Like::where('post_id',$request->id)->where('user_id',Auth::user()->id)->get();
        //checking if it returns 0 then post is not liked and should be liked else unliked
        if(count($like) > 0)
        {
            $like[0]->delete();
            $post = Post::where('id',$request->id)->get();
            foreach ($post as $post)
            {
                //get user of post
                $post->user;
                $post->user->followings;
                $post->user->followers;
                $post->user['followingCount']= count($post->user->followings);
                $post->user['followerCount']= count($post->user->followers);
                //comments count
                $post['commentsCount'] = count($post->comments);
                //likes count
                $post['likesCount'] = count($post->likes);
                //check if user like his own post
                $post['selfLike'] = false;
                foreach ($post->likes as $like)
                {
                    if ($post->user_id == Auth::user()->id)
                    {
                        $post['selfLike'] = true;
                    }
                }
            }
            return response()->json([
                'success' => true,
                'message' => "unliked",
                'status_code' => Response::HTTP_OK,
                'post'=>$post
            ], Response::HTTP_OK);
        }

        $like = new Like;
        $like->user_id = Auth::user()->id;
        $like->post_id = $request->id;
        $like->save();
        $post = $like->post;
        $post->user;
        $post->comments;
        $post->likes;
        $post->user->followings;
        $post->user->followers;
        $post->user['followingCount']= count($post->user->followings);
        $post->user['followerCount']= count($post->user->followers);
        $post['commentsCount'] = count($post->comments);
        $post['likesCount'] = count($post->likes);
        if ($post->user_id == Auth::user()->id)
        {
            $post['selfLike'] = true;
        }else{
            $post['selfLike'] = false;
        }

        return response()->json([
            'success' => true,
            'message' => "liked",
            'status_code' => Response::HTTP_OK,
            'post'=>$post
        ], Response::HTTP_OK);

//        return response()->json([
//            'success' => true,
//            'message' => "liked",
//            'status_code' => Response::HTTP_OK
//        ], Response::HTTP_OK);
    }
}

