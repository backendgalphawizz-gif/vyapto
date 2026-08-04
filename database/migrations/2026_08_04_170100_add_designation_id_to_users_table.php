<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'designation_id')) {
            Schema::table('users', function (Blueprint $table) {
                $after = Schema::hasColumn('users', 'job_type') ? 'job_type' : 'role_id';
                $table->unsignedBigInteger('designation_id')->nullable()->after($after);
                $table->foreign('designation_id')->references('id')->on('designations')->nullOnDelete();
            });
        }

        if (Schema::hasColumn('users', 'designation') && Schema::hasTable('designations')) {
            $users = DB::table('users')
                ->whereNotNull('designation')
                ->where('designation', '!=', '')
                ->get(['id', 'designation']);

            foreach ($users as $user) {
                $name = trim((string) $user->designation);
                if ($name === '') {
                    continue;
                }

                $designationId = DB::table('designations')->where('name', $name)->value('id');
                if (! $designationId) {
                    $designationId = DB::table('designations')->insertGetId([
                        'name' => $name,
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('users')->where('id', $user->id)->update(['designation_id' => $designationId]);
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('designation');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'designation')) {
            Schema::table('users', function (Blueprint $table) {
                $after = Schema::hasColumn('users', 'job_type') ? 'job_type' : 'role_id';
                $table->string('designation', 255)->nullable()->after($after);
            });

            if (Schema::hasColumn('users', 'designation_id') && Schema::hasTable('designations')) {
                $users = DB::table('users')
                    ->whereNotNull('designation_id')
                    ->get(['id', 'designation_id']);

                foreach ($users as $user) {
                    $name = DB::table('designations')->where('id', $user->designation_id)->value('name');
                    DB::table('users')->where('id', $user->id)->update(['designation' => $name]);
                }
            }
        }

        if (Schema::hasColumn('users', 'designation_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['designation_id']);
                $table->dropColumn('designation_id');
            });
        }
    }
};
