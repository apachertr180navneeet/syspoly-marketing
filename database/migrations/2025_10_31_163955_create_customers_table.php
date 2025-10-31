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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('firm_name');
            $table->string('person_name');
            $table->string('contact_number')->unique();
            $table->string('email')->unique();
            $table->enum('status', ['active', 'inactive'])->default('active'); // ✅ Status ENUM field
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
