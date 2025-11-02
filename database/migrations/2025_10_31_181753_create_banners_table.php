<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('whatsappcontent')->nullable();
            $table->text('emailcontant')->nullable(); // kept your spelling "emailcontant"
            $table->string('image')->nullable();       // path or filename
            $table->string('position')->nullable();       // path or filename
            $table->string('createdimage')->nullable(); // kept as string per request
            $table->enum('status', ['active', 'inactive'])->default('active'); // 👈 status field
            $table->timestamps();
            $table->softDeletes(); // Adds deleted_at column for soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
}
