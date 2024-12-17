<?php

namespace App\Http\Middleware;

use App\Traits\APIResponse;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class CheckTokenVersion
{
    use APIResponse;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $tokenVersion = $payload->get('token_version');
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->errorResponse('User not found', 404, ['error' => 'User not found']);
            }

            if ($user->token_version !== $tokenVersion) {
                return $this->errorResponse('Invalid User.', 401, ['error' => 'Token has been revoked']);
            }

        } catch (TokenExpiredException $e) {
            return $this->errorResponse('Token has expired.', 410, ['error' => 'Token expired']);

        } catch (TokenInvalidException $e) {
            return $this->errorResponse('Invalid token.', 401, ['error' => 'Token is invalid']);

        } catch (JWTException $e) {
            return $this->errorResponse('Token error.', 401, ['error' => 'Could not parse token: ' . $e->getMessage()]);
        }

        return $next($request);
    }
}
