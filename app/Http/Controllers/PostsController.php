<?php

namespace App\Http\Controllers;

use App\Models\FollowUser;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PostsController extends Controller
{
    public function create(Request $request)
    {
        $post = new Post;
        $post->user_id = Auth::user()->id;
        $post->desc = $request->desc;

        //check if post has photo
        if ($request->photo != '') {
            $photoName = time().'.'.$request->photo->extension();
            $request->photo->storeAs('public/posts', $photoName);
//            //choose a unique name for photo
//            $photo = time() . '.jpg';
//            file_put_contents('storage/posts/'.$photo,base64_decode($request->photo));
            $post->photo = $photoName;
        }

        $post->save();
        $post->user;
        $post->user->followings;
        $post->user->followers;
        $post->user['followingCount']= count($post->user->followings);
        $post->user['followerCount']= count($post->user->followers);
        return response()->json([
            'success' => true,
            'message' => "Posted Successfully",
            'post' => $post,
            'status_code' => Response::HTTP_OK
        ], Response::HTTP_OK);

    }

    public function update(Request $request)
    {
        $post = Post::find($request->id);

        //check if user is editing his post
        if (Auth::user()->id != $post->user_id) {
            return response()->json([
                'success' => false,
                'message' => "Unauthorized Access",
                'status_code' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        } else {
            $photoName = '';
            //check if user provided photo
            if ($request->photo != '' OR $request->photo != null)
            {
                $photoName = time().'.'.$request->photo->extension();
                $request->photo->storeAs('public/posts', $photoName);
                $post->photo = $photoName;
            }
            $post->desc = $request->desc;
            $post->update();
            $post->user;
            $post->user->followings;
            $post->user->followers;
            $post->user['followingCount']= count($post->user->followings);
            $post->user['followerCount']= count($post->user->followers);
            return response()->json([
                'success' => true,
                'message' => "Post Updated Successfully",
                'post' => $post,
                'status_code' => Response::HTTP_OK
            ], Response::HTTP_OK);
        }
    }

    public function delete(Request $request)
    {
        $post = Post::find($request->id);

        //check if user is editing his post
        if (Auth::user()->id != $post->user_id) {
            return response()->json([
                'success' => false,
                'message' => "Unauthorized Access",
                'status_code' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        } else {
            //check if post has photo to delete
            if ($post->photo != '')
            {
                Storage::delete('public/posts/'.$post->photo);
            }
            $post->delete();
            return response()->json([
                'success' => true,
                'message' => "Post Deleted Successfully",
                'status_code' => Response::HTTP_OK
            ], Response::HTTP_OK);
        }
    }

    public function posts(Request $request)
    {
        $posts = Post::orderBy('id','desc')->get();
        foreach ($posts as $post)
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
            'posts' => $posts,
            'status_code' => Response::HTTP_OK
        ], Response::HTTP_OK);

    }

    public function my_posts(Request $request)
    {

        if ($request->user_id == null){
            $posts = Post::where('user_id',Auth::user()->id)->orderBy('id','desc')->get();
        }else{
            $posts = Post::where('user_id',$request->user_id)->orderBy('id','desc')->get();
        }

        foreach ($posts as $post)
        {
            //get user of post
            $post->user;
            $post->user->followings;
            $post->user->followers;
            $post->user['followingCount']= count($post->user->followings);
            $post->user['followerCount']= count($post->user->followers);
            $follow = FollowUser::where('follow_id',$request->user_id)->where('followedBy_id',Auth::user()->id)->get();
            if(count($follow) > 0)
            {
                $post->user['isFollow'] = true;
            }else{
                $post->user['isFollow'] = false;
            }

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
            'posts' => $posts,
            'status_code' => Response::HTTP_OK
        ], Response::HTTP_OK);

    }

}
