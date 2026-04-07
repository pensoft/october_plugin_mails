<?php namespace Pensoft\Mails\Updates;

use Schema;
use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdatePensoftMailsGroups extends Migration
{
    public function up(): void
    {
        Schema::table('pensoft_mails_groups', function (Blueprint $table) {
            $table->text('replace_from')->nullable();
            $table->text('replace_to')->nullable();
            $table->text('add_replay_to')->nullable();
            $table->string('name_append')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pensoft_mails_groups', function (Blueprint $table) {
            $table->dropColumn('replace_from');
            $table->dropColumn('replace_to');
            $table->dropColumn('add_replay_to');
            $table->dropColumn('name_append');
        });
    }
}
