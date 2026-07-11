<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_no')->unique();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->date('delivery_date');
            $table->time('delivery_time')->nullable();
            $table->text('address');
            $table->string('contact_number')->nullable();
            $table->integer('quantity')->default(1);
            $table->enum('delivery_type', ['regular', 'rush', 'scheduled', 'pickup'])->default('regular');
            $table->enum('status', ['scheduled', 'assigned', 'out_for_delivery', 'delivered', 'failed', 'cancelled'])->default('scheduled');
            $table->enum('route', ['morning', 'afternoon', 'evening'])->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['delivery_date', 'status']);
            $table->index('driver_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
