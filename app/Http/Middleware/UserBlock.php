<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserBlock
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!resolve(UserRepository::class)->isBlock()) {
            return $next($request);
        }

        return response()->json([
            'message' => 'You are blocked'
        ], Response::HTTP_FORBIDDEN);
    }
}
