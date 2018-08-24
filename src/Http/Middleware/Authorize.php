<?php

namespace Sbine\RouteViewer\Http\Middleware;

use Sbine\RouteViewer\RouteViewer;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        return resolve(RouteViewer::class)->authorize($request) ? $next($request) : abort(403);
    }
}
