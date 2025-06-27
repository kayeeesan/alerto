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
        if(Auth::attempt($credentials)) {
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

                $mainConnection = 'sqlite';
                $tenantConnection = 'tenant';

                // Ensure the main DB connection uses absolute path
                config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
                Log::info("Main DB connection set to: " . config('database.connections.sqlite.database'));

                // Configure tenant connection
                config(['database.connections.tenant' => [
                    'driver' => 'sqlite',
                    'database' => $dbPath,
                    'prefix' => '',
                ]]);

                // Switch to tenant
                DB::setDefaultConnection($tenantConnection);
                Log::info("Switched to tenant database: $dbName");

                try {
                    // Get all tables from main DB except system ones
                    $tables = DB::connection($mainConnection)->select("
                        SELECT name FROM sqlite_master 
                        WHERE type='table' AND name NOT LIKE 'sqlite_%'
                    ");

                    Log::info("Found tables in main DB: ", array_map(fn($t) => $t->name, $tables));

                    foreach ($tables as $tableObj) {
                    $table = $tableObj->name;

                    $tenantDb = DB::connection($tenantConnection);
                    $mainDb = DB::connection($mainConnection);

                    // Check if table exists in tenant DB
                    $exists = $tenantDb->select("
                        SELECT name FROM sqlite_master 
                        WHERE type='table' AND name = ?
                    ", [$table]);

                    if (empty($exists)) {
                        // Table doesn't exist → create it
                        $createStmt = $mainDb->select("
                            SELECT sql FROM sqlite_master 
                            WHERE type='table' AND name = ?
                        ", [$table]);

                        if (!empty($createStmt) && isset($createStmt[0]->sql)) {
                            $createSql = $createStmt[0]->sql;
                            $tenantDb->statement($createSql);
                            Log::info("Created table '$table' in tenant DB.");
                        } else {
                            Log::warning("No CREATE TABLE statement found for '$table'. Skipping.");
                            continue;
                        }
                    }

                    // Sync new data
                    try {
                        // Assuming 'id' is the primary key — adjust if necessary
                        $existingIds = $tenantDb->table($table)->pluck('id')->toArray();

                        $newRows = $mainDb->table($table)
                            ->when(!empty($existingIds), function ($query) use ($existingIds) {
                                return $query->whereNotIn('id', $existingIds);
                            })
                            ->get();

                        foreach ($newRows as $row) {
                            $tenantDb->table($table)->insert((array) $row);
                        }

                        Log::info("Synced " . count($newRows) . " new rows to table '$table'.");
                    } catch (\Exception $e) {
                        Log::error("Failed to sync table '$table': " . $e->getMessage());
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
            
            return response([
                'user' => new ResourcesUser($user),
                'access_token' => $user->createToken('MyApp')->plainTextToken,
                'tenant_db_path' => $municipality ? $dbPath : null,
            ], Response::HTTP_OK);
        }else{
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
