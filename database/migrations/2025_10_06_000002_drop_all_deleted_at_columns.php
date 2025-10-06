<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $defaultConnection = config('database.default');
        $connectionConfig = config("database.connections.$defaultConnection");
        $databaseName = $connectionConfig['database'] ?? null;

        if (!$databaseName) {
            return;
        }

        $tablesWithDeletedAt = DB::select(
            'SELECT TABLE_NAME as table_name FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND COLUMN_NAME = "deleted_at"',
            [$databaseName]
        );

        foreach ($tablesWithDeletedAt as $row) {
            $table = $row->table_name;
            if (!Schema::hasColumn($table, 'deleted_at')) {
                continue;
            }
            Schema::table($table, function ($tableBlueprint) {
                $tableBlueprint->dropColumn('deleted_at');
            });
        }
    }

    public function down(): void
    {
        // Tidak perlu restore kolom deleted_at
    }
};


