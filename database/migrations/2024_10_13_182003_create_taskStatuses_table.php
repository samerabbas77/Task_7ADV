<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('taskStatuses', function (Blueprint $table) {
            $table->id();
            $table->enum('old_status',['Open','In_Progress', 'Completed', 'Blocked','N/A'])->nullable();
            $table->enum('new_status',['Open','In_Progress', 'Completed', 'Blocked'])->nullable();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->cascadeOnDelete();
            $table->softDeletes(); // Adds deleted_at column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_statuses');
    }
};
