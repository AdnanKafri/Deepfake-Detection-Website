<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('file_name');
            $table->enum('file_type', ['image', 'video', 'audio']);
            $table->enum('prediction', ['REAL', 'FAKE']);
            $table->float('confidence')->default(0);

            $table->string('category')->nullable(); // سياسي، خاص، إلخ
            $table->enum('user_feedback', ['CORRECT', 'INCORRECT'])->nullable();
            $table->boolean('report_flag')->default(false);

            $table->longText('result_json')->nullable(); // الرد الكامل من FastAPI

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
