<?php

// app/Http/Middleware/CompletePastSessions.php
namespace App\Http\Middleware;

use Closure;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CompletePastSessions
{
    public function handle(Request $request, Closure $next)
    {
        Session::where('status','scheduled')
            ->where('scheduled_at','<', now())
            ->update(['status'=>'completed']);

        return $next($request);
    }
}