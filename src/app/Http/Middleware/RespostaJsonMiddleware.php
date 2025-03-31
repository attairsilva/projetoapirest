<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RespostaJsonMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Garante que a resposta seja JSON
        if ($request->expectsJson() && !$response->headers->has('Content-Type')) {
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}
