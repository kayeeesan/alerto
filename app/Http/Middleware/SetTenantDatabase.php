<?php


namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;


class SetTenantDatabase
{
    public function handle(Request $request, Closure $next): Response
    {
        $dbPath = $request->header('X-Tenant-DB');


        if ($dbPath && file_exists($dbPath)) {
            Config::set('database.connections.tenant', [
                'driver' => 'sqlite',
                'database' => $dbPath,
                'prefix' => '',
            ]);
            DB::setDefaultConnection('tenant');
        } else {
            DB::setDefaultConnection(config('database.default'));
        }


        return $next($request);
    }
}


