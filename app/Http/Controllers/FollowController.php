<?php

namespace App\Http\Controllers;

use App\Models\FollowUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FollowController extends Controller
{
    public function follow(Request $request){
        $follow = FollowUser::where('follow_id',$request->user_id)->where('followedBy_id',Auth::user()->id)->get();
        if(count($follow) > 0)
        {
            $follow[0]->delete();
            return response()->json([
                'success' => true,
                'message' => "unfollowed",
                'status_code' => Response::HTTP_OK,
            ], Response::HTTP_OK);
        }

        $follow = new FollowUser;
        $follow->follow_id = $request->user_id;
        $follow->followedBy_id = Auth::user()->id;
        $follow->save();

        return response()->json([
            'success' => true,
            'message' => "followed",
            'status_code' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
}
