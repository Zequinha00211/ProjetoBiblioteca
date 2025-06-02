<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

use Illuminate\Http\Middleware\TrustProxies as Middleware;

class KeycloakMiddleware extends Middleware
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
        try {
            $header = $request->headers->get('Authorization');
            $accessToken = trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $header));
            if ($accessToken === '' || empty($accessToken)) {
                return response()->json(['success' => false, 'status' => 'Token is Required'], 401);
            }
            $keycloak = new  \App\Helpers\Keycloak();
            $keycloak->verifyJwt($accessToken);
        } catch (Exception $e) {
            $output = new \Symfony\Component\Console\Output\ConsoleOutput();
            $message = 'Message: '. $e->getMessage(). ' Linha: '. $e->getLine(). ' File: '. $e->getFile();
            $output->writeln("<info>KeycloakMiddleware: $message</info>");
            return response()->json(['success' => false, 'status' => $e->getMessage()], $e->getCode());
        }
        return $next($request);
    }
}
