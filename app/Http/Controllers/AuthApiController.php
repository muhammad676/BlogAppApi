<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthApiController extends Controller
{
    public function register(Request $request)
    {
        //Validate data
        $data = $request->only( 'email', 'password');
        $validator = Validator::make($data, [
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new user
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //User created, return success response
        return $this->login($request);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return $credentials;
            return response()->json([
                'success' => false,
                'message' => 'Could not create token.',
            ], 500);
        }
        $user = Auth::user();

        $user->followings;
        $user->followers;
        $user['followingCount']= count($user->followings);
        $user['followerCount']= count($user->followers);

        //Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
            'data' => $user
        ],Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        //valid credential
//        $validator = Validator::make($request->only('token'), [
//            'token' => 'required'
//        ]);
//
//        //Send failed response if request is not valid
//        if ($validator->fails()) {
//            return response()->json(['error' => $validator->messages()], 200);
//        }

        //Request is validated, do logout
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function saveUserInfo(Request $request)
    {
        $credentials = $request->only('firstname', 'lastname','photo');
        $validator = Validator::make($credentials,  [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $user = User::find(Auth::user()->id);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $photoName = '';
        //check if user provided photo
        if ($request->photo != '' OR $request->photo != null)
        {
            $photoName = time().'.'.$request->photo->extension();
            $request->photo->storeAs('public/profiles', $photoName);
//            //user time for photo name to prevent name duplication
//            $photo = time().'.jpg';
//            //decode photo string and save to stroage/profiles
//            file_put_contents('storage/profiles/'.$photo,base64_decode($request->photo));
            $user->photo = $photoName;
        }
        $user->update();

        $user->followings;
        $user->followers;
        $user['followingCount']= count($user->followings);
        $user['followerCount']= count($user->followers);

        return response()->json([
            'success' => true,
            'message' => 'User Info has been saved',
            'data' => $user
        ]);
    }

    public function updateUserInfo(Request $request)
    {
        $credentials = $request->only('firstname', 'lastname','photo');
        $validator = Validator::make($credentials,  [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        $user = User::find(Auth::user()->id);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $photoName = '';
        //check if user provided photo
        if ($request->photo != '' OR $request->photo != null)
        {
            $photoName = time().'.'.$request->photo->extension();
            $request->photo->storeAs('public/profiles', $photoName);
//            //user time for photo name to prevent name duplication
//            $photo = time().'.jpg';
//            //decode photo string and save to stroage/profiles
//            file_put_contents('storage/profiles/'.$photo,base64_decode($request->photo));
            $user->photo = $photoName;
        }
        $user->update();

        $user->followings;
        $user->followers;
        $user['followingCount']= count($user->followings);
        $user['followerCount']= count($user->followers);
        return response()->json([
            'success' => true,
            'message' => 'User Info has been updated',
            'data' => $user
        ]);
    }

    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }

    public function pic(Request $request)
    {
        $photoName = time().'.'.$request->photo->extension();
        $request->photo->storeAs('public/profiles', $photoName);


        return response()->json([
            'success' => true,
            'message' => 'Picture Has Been Saved',
            'data' => $photoName
        ]);
    }
}
