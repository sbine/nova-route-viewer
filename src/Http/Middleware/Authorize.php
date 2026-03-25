<?php

namespace Sbine\RouteViewer\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Sbine\RouteViewer\RouteViewer;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return Response
     */
    public function handle($request, $next)
    {
        $tool = collect(Nova::registeredTools())->first([$this, 'matchesTool']);

        return optional($tool)->authorize($request) ? $next($request) : abort(403);
    }

    /**
     * Determine whether this tool belongs to the package.
     *
     * @param  Tool  $tool
     * @return bool
     */
    public function matchesTool($tool)
    {
        return $tool instanceof RouteViewer;
    }
}
