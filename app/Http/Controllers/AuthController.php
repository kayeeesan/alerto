<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Resources\User as ResourcesUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->status === 'pending') {
                Auth::logout();
                return response([
                    'message' => 'Account is pending'
                ], Response::HTTP_UNAUTHORIZED);
            }

            if ($user->status === 'disabled') {
                Auth::logout();
                return response([
                    'message' => 'Account is disabled'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $token = $user->createToken('MyApp')->plainTextToken;

            $municipality = $user->staff->municipality ?? null;

            if ($municipality) {
                $dbName = strtolower(str_replace(' ', '_', $municipality->name)) . '_db';

                try {
                    $mainDb = config('database.connections.mysql.database');
                    $mysqlConnection = DB::connection('mysql');

                    $dbExists = $mysqlConnection->select(
                        "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", 
                        [$dbName]
                    );

                    if (empty($dbExists)) {
                        $mysqlConnection->statement("CREATE DATABASE $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        Log::info("Created tenant database: $dbName");
                    } else {
                        Log::info("Tenant database already exists: $dbName");
                    }

                    // Configure tenant connection
                    config(['database.connections.tenant' => [
                        'driver' => 'mysql',
                        'host' => env('DB_HOST', '127.0.0.1'),
                        'port' => env('DB_PORT', '3306'),
                        'database' => $dbName,
                        'username' => env('DB_USERNAME', 'root'),
                        'password' => env('DB_PASSWORD', ''),
                        'charset' => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                        'prefix' => '',
                        'strict' => true,
                        'engine' => null,
                    ]]);

                    DB::purge('tenant');
                    DB::reconnect('tenant');
                    DB::setDefaultConnection('tenant');

                    // Sync all tables from main DB (even if already exists)
                    $tables = DB::connection('mysql')->select("SHOW TABLES");

                    foreach ($tables as $tableObj) {
                        $tableName = array_values((array) $tableObj)[0];

                        // Skip migrations table
                        if ($tableName === 'migrations') {
                            continue;
                        }

                        try {
                            // Drop if exists
                            DB::statement("DROP TABLE IF EXISTS `$dbName`.`$tableName`");

                            // Recreate structure
                            DB::statement("CREATE TABLE `$dbName`.`$tableName` LIKE `$mainDb`.`$tableName`");

                            // Copy data
                            DB::statement("INSERT INTO `$dbName`.`$tableName` SELECT * FROM `$mainDb`.`$tableName`");

                            Log::info("Synced table: $tableName");
                        } catch (\Exception $e) {
                            Log::error("Error syncing table $tableName: " . $e->getMessage());
                        }
                    }

                } catch (\Exception $e) {
                    Log::error("Tenant DB setup failed: " . $e->getMessage());
                    return response([
                        'message' => 'Tenant DB setup failed.',
                        'error' => $e->getMessage()
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

            } else {
                DB::setDefaultConnection(config('database.default'));
                Log::info("No municipality found. Using main database.");
            }

            return response([
                'user' => new ResourcesUser($user),
                'access_token' => $token,
                'tenant_database' => $municipality ? $dbName : null,
            ], Response::HTTP_OK);
        } else {
            return response([
                'message' => 'Incorrect credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }



    public function logout()
    {
        Auth::guard('web')->logout();

        return response([
            'message' => 'Successfully logout.'
        ], Response::HTTP_OK);
    }

    public function setPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = Auth::user();
            $user->password = Hash::make($request->password);
            $user->password_reset = false;
            $user->update();

            return response()->json(['message' => 'Password successfully saved.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}