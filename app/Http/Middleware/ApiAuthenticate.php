<?php
namespace App\Http\Middleware;

use App\Response;
use Closure;
use App\Models\Token;
use Illuminate\Support\Facades\Log;

class ApiAuthenticate
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
        $token = $request->header('X-Api-Token');
        if(!$request->header('X-Api-Token')){
            return response()->json(Response::prepareErrorResponse( 'Token Invalid.', 403));
        }

        /** @var Token $tokenModel */
        $tokenModel = Token::query()->with('user')->find($token);
        if(!$tokenModel){
            return response()->json(Response::prepareErrorResponse('Token Invalid.', Response::HTTP_BAD_REQUEST));
        }
        if(!$tokenModel->user) {
            Log::error('User not Found in DB for token: ' . $token);
            return response()->json(Response::prepareErrorResponse('Token Invalid.', Response::HTTP_BAD_REQUEST));
        }

        $request->setUserResolver(function() use ($tokenModel){ return $tokenModel->user;});
        return $next($request);
    }
}
