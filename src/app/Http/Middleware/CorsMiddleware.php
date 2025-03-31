<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // .env dominios origens
        $allowedOrigins = explode(',', env('LISTA_DOMAINS_PERMITIDOS', ''));

        // Se nenhuma origem permitida estiver configurada, permita todas as origens (use com cautela)
        if (empty($allowedOrigins) || (count($allowedOrigins) === 1 && $allowedOrigins[0] === '')) {
            $origin = '*'; // Permite todas as origens
        } else {
            // Se origens permitidas estiverem configuradas, use a primeira da lista como origem padrão
            $origin = $allowedOrigins[0];
        }

        // Requisição OPTIONS
        if ($request->isMethod('OPTIONS')) {
            return response()->json('OK', 200, [
                'Access-Control-Allow-Origin' => $origin,
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN',
            ]);
        }

        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN');

        return $response;

        // # .env dominios origens
        // $allowedOrigins = explode(',', env('LISTA_DOMAINS_PERMITIDOS', ''));


        // $origin = $request->headers->get('Origin');

        // # Se a origem não estiver na lista, bloqueia o acesso
        // if (!in_array($origin, $allowedOrigins)) {
        //     Log::info('CORS Origin: ' .$origin);
        //     return response()->json(['error' => 'Acesso bloqueado para origem'], 403);
        // }

        // # requisição OPTIONS
        // if ($request->isMethod('OPTIONS')) {
        //     return response()->json('OK', 200, [
        //         'Access-Control-Allow-Origin' => $origin,
        //         'Access-Control-Allow-Credentials' => 'true',
        //         'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
        //         'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN'
        //     ]);
        // }

        // $response = $next($request);

        // $response->headers->set('Access-Control-Allow-Origin', $origin);
        // $response->headers->set('Access-Control-Allow-Credentials', 'true');
        // $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        // $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN');

        // return $response;
    }
}
