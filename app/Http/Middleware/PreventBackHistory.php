<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // headers->add() works on every Symfony response, including file downloads
        // (BinaryFileResponse/StreamedResponse), which have no withHeaders() method.
        $response->headers->add([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);

        return $response;
    }
}
