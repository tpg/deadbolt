<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addDeadboltPermissionsColumn extends Migration
{
    protected $table = 'users';

    public function up(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->json('permissions')->after('id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropColumns($this->table, ['permissions']);
    }
}
