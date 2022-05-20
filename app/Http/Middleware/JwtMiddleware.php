<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $message = '';
        try {
            JWTAuth::parseToken()->authenticate();
            return $next($request);
        } catch (TokenInvalidException $e) {
            $message = "Invalid Token";
        } catch (TokenExpiredException $e) {
            $message = "Token Expired";
        } catch (JWTException $e) {
            $message = "Provide Token";
        }

        return response()->json([
            'sucess' => false,
            'message' => $message
        ]);

//        catch (Exception $e) {
//            if ($e instanceof TokenInvalidException){
//                $message = "Invalid Token";
//            }else if ($e instanceof TokenExpiredException){
//                $message = "Token Expired";
//            }else{
//                $message = "Provide Toke";
//            }
//        }



    }
}
