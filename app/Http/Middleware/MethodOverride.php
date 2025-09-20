<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MethodOverride
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('POST') && $request->has('_method')) {
            $method = strtoupper($request->input('_method'));
            
            if (in_array($method, ['PUT', 'PATCH', 'DELETE'])) {
                $request->setMethod($method);
            }
        }
        
        return $next($request);
    }
}