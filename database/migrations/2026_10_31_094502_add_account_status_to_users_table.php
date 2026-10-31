<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // active | suspended. Kept as a short string (not an enum column) so
            // new states can be added without an ALTER on a large table.
            $table->string('status', 20)->default('active')->after('password');
            $table->timestamp('suspended_at')->nullable()->after('status');
            $table->string('suspension_reason', 500)->nullable()->after('suspended_at');
            $table->timestamp('last_login_at')->nullable()->after('suspension_reason');

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'suspended_at', 'suspension_reason', 'last_login_at']);
        });
    }
};
