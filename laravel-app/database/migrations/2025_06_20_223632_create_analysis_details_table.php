<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('analysis_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_id')->constrained('analyses')->onDelete('cascade');

            $table->integer('segment_index'); // رقم الفريم أو المقطع
            $table->enum('prediction', ['REAL', 'FAKE']);
            $table->float('confidence')->default(0);

            $table->string('original_image_path')->nullable();     // للفيديو
            $table->string('cropped_face_path')->nullable();       // للفيديو والصورة
            $table->json('extra_json')->nullable();                // تفاصيل الصوت أو إضافات

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_details');
    }
};
