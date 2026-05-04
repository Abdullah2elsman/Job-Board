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
        Schema::table('jobs', function (Blueprint $table)
        {
            if (!Schema::hasColumn('jobs', 'salary')) {
                $table->decimal('salary', 10, 2)->nullable()->after('requirements');
            }

            $table->index('status');
            $table->index('work_type');
            $table->index('deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table)
        {
            if (Schema::hasColumn('jobs', 'salary')) {
                $table->dropColumn('salary');
            }

            $table->dropIndex(['status']);
            $table->dropIndex(['work_type']);
            $table->dropIndex(['deadline']);
        });
    }
};
