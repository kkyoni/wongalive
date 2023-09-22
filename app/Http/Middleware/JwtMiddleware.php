<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $headers = apache_request_headers();
        // $request->headers->set('Authorization', $headers['Authorization']);
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json([
                    'status' => 'token_error',
                    'message' => 'Token is Invalid',
                ],404);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json([
                    'status' => 'token_error',
                    'message' => 'Token is Invalid',
                ]);
            }else{
                return response()->json([
                    'status'  => 'token_error',
                    'message' => 'Authorization Token not found'
                ]);
            }
        }
        return $next($request);
    }
}
