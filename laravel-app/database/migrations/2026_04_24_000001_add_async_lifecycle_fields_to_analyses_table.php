<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('file_type');
            $table->string('file_path')->nullable()->after('file_name');
            $table->timestamp('queued_at')->nullable()->after('result_json');
            $table->timestamp('started_at')->nullable()->after('queued_at');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->text('error_message')->nullable()->after('completed_at');
        });

        Schema::table('analyses', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->enum('prediction', ['REAL', 'FAKE'])->nullable()->change();
            $table->float('confidence')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'file_path',
                'queued_at',
                'started_at',
                'completed_at',
                'error_message',
            ]);
        });

        Schema::table('analyses', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->enum('prediction', ['REAL', 'FAKE'])->nullable(false)->change();
            $table->float('confidence')->nullable(false)->default(0)->change();
        });
    }
};
