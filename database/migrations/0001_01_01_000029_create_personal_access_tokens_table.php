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
        // personal_access_tokens is created by the package migration (2019_12_14_000001). Skip here to avoid duplicate table creation.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // no-op: package migration handles drop to avoid duplicate drops
    }
};
