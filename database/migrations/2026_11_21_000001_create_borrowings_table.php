<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained();
            $table->date('borrowing_date');
            $table->date('return_date');
            $table->boolean('is_returned')->default(false);
            $table->timestamps();

            $table->index(['book_id', 'is_returned']);
            $table->index(['customer_id', 'is_returned']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
