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
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('confirmed_at')->nullable()->after('status');
            $table->unsignedBigInteger('confirmed_by')->nullable()->after('confirmed_at');
            
            // Foreign key constraint
            $table->foreign('confirmed_by')->references('id')->on('users')->onDelete('set null');
            
            // Index cho performance
            $table->index(['status', 'confirmed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['confirmed_by']);
            $table->dropIndex(['status', 'confirmed_at']);
            $table->dropColumn(['confirmed_at', 'confirmed_by']);
        });
    }
};