<?php

namespace App\Http\Middleware;

use DB;
use Closure;
use App\Affiliate;
use App\AffiliateLog;

class Track
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
        if ($request->has('track')) {
            // CPC的用户
            $affiliate = Affiliate::where(['track' => $request->track, 'status' => 1])->first();
            if ($affiliate instanceof Affiliate) {
                $visited = AffiliateLog::where(['ip' => $request->ip(), 'track' => $request->track])->whereDate('created_at', DB::raw('CURDATE()'))->count();
                if (!$visited) {
                    AffiliateLog::create(['ip' => $request->ip(), 'track' => $request->track]);
                    $affiliate->click++;
                    $affiliate->save();
                }
            }
        }

        return $next($request);
    }
}
