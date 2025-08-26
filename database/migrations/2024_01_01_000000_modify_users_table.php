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
        Schema::table('users', function (Blueprint $table) {
            // Add new columns
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('full_name', 255)->after('phone');
            $table->date('date_of_birth')->nullable()->after('full_name');
            $table->string('user_type', 20)->after('date_of_birth')->comment('farmer, bank, cooperative, verifier, government, buyer');
            $table->decimal('gps_latitude', 10, 8)->nullable()->after('user_type');
            $table->decimal('gps_longitude', 11, 8)->nullable()->after('gps_latitude');
            $table->string('organization_name', 255)->nullable()->after('gps_longitude');
            $table->string('organization_type', 50)->nullable()->after('organization_name');
            $table->text('address')->nullable()->after('organization_type');

            // Drop the default 'name' column and add 'full_name' instead
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert changes
            $table->string('name')->after('id');
            $table->dropColumn([
                'phone',
                'full_name',
                'date_of_birth',
                'user_type',
                'gps_latitude',
                'gps_longitude',
                'organization_name',
                'organization_type',
                'address'
            ]);
        });
    }
};
