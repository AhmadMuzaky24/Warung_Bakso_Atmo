<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catering_orders', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 32)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('event_date');
            $table->text('event_address');
            $table->string('customer_phone', 30);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('dp_amount');
            $table->string('status', 20)->default('submitted');
            $table->string('payment_status', 20)->default('pending');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('status');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catering_orders');
    }
};
