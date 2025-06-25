<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Resources\User as ResourcesUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $credentials['username'] = strtolower($credentials['username']);

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

            $municipality = $user->staff->municipality ?? null;

            $dbPath = null;
            $tenantConnection = 'tenant';
            $mainConnection = 'sqlite';

            if ($municipality) {
                $dbName = strtolower(str_replace(' ', '_', $municipality->name)) . '_db';
                $dbPath = database_path("municipalities/{$dbName}.sqlite");

                // Ensure the directory exists
                if (!File::exists(database_path('municipalities'))) {
                    File::makeDirectory(database_path('municipalities'), 0755, true);
                    Log::info("Created directory for municipality databases at: " . database_path('municipalities'));
                }

                // Create the database file if it doesn't exist
                if (!File::exists($dbPath)) {
                    File::put($dbPath, '');
                    Log::info("Created new SQLite database: $dbPath");
                } else {
                    Log::info("SQLite database already exists: $dbPath");
                }

                // Set main DB path explicitly
                config(['database.connections.sqlite.database' => database_path('database.sqlite')]);

                // Configure tenant DB
                config(['database.connections.tenant' => [
                    'driver' => 'sqlite',
                    'database' => $dbPath,
                    'prefix' => '',
                ]]);

                DB::setDefaultConnection($tenantConnection);
                Log::info("Switched to tenant database: $dbName");

                try {
                    $tables = DB::connection($mainConnection)->select("
                        SELECT name FROM sqlite_master
                        WHERE type='table' AND name NOT LIKE 'sqlite_%'
                    ");

                    foreach ($tables as $tableObj) {
                        $table = $tableObj->name;

                        $exists = DB::connection($tenantConnection)->select("
                            SELECT name FROM sqlite_master
                            WHERE type='table' AND name = ?
                        ", [$table]);

                        if (!empty($exists)) {
                            Log::info("Table '$table' already exists in tenant DB. Skipping.");
                            continue;
                        }

                        $createStmt = DB::connection($mainConnection)->select("
                            SELECT sql FROM sqlite_master
                            WHERE type='table' AND name = ?
                        ", [$table]);

                        if (!empty($createStmt) && isset($createStmt[0]->sql)) {
                            $createSql = $createStmt[0]->sql;
                            DB::connection($tenantConnection)->statement($createSql);
                            Log::info("Created table '$table' in tenant DB.");

                            $rows = DB::connection($mainConnection)->table($table)->get();
                            foreach ($rows as $row) {
                                DB::connection($tenantConnection)->table($table)->insert((array) $row);
                            }

                            Log::info("Copied data for table '$table'.");
                        } else {
                            Log::warning("No CREATE TABLE statement found for '$table'. Skipping.");
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Error during tenant DB setup: " . $e->getMessage());
                    return response([
                        'message' => 'Failed to initialize tenant database.',
                        'error' => $e->getMessage()
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                DB::setDefaultConnection(config('database.default'));
                Log::info("No municipality assigned. Using default database.");
            }

            // Generate token in main DB
            DB::setDefaultConnection($mainConnection);
            $token = $user->createToken('MyApp');
            $plainToken = $token->plainTextToken;

            // Sync token to tenant DB if needed
            if ($municipality) {
                try {
                    $mainTokenData = DB::connection($mainConnection)->table('personal_access_tokens')
                        ->where('id', $token->accessToken->id)
                        ->first();

                    if ($mainTokenData) {
                        DB::connection($tenantConnection)->table('personal_access_tokens')->insert([
                            'id' => $mainTokenData->id,
                            'tokenable_type' => $mainTokenData->tokenable_type,
                            'tokenable_id' => $mainTokenData->tokenable_id,
                            'name' => $mainTokenData->name,
                            'token' => $mainTokenData->token,
                            'abilities' => $mainTokenData->abilities,
                            'last_used_at' => $mainTokenData->last_used_at,
                            'created_at' => $mainTokenData->created_at,
                            'updated_at' => $mainTokenData->updated_at,
                        ]);
                        Log::info("Token copied to tenant DB for user ID {$user->id}");
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to copy token to tenant DB: " . $e->getMessage());
                }
            }

            return response([
                'user' => new ResourcesUser($user),
                'access_token' => $plainToken,
                'tenant_db_path' => $dbPath,
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
