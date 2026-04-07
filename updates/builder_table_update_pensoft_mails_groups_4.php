<?php namespace Pensoft\Mails\Updates;

use Schema;
use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdatePensoftMailsGroups4 extends Migration
{
    public function up(): void
    {
        Schema::table('pensoft_mails_groups', function (Blueprint $table) {
            $table->smallInteger('active')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pensoft_mails_groups', function (Blueprint $table) {
            $table->smallInteger('active')->nullable(false)->change();
        });
    }
}
