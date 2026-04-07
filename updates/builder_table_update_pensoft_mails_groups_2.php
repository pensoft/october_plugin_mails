<?php namespace Pensoft\Mails\Updates;

use Schema;
use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdatePensoftMailsGroups2 extends Migration
{
    public function up(): void
    {
        Schema::table('pensoft_mails_groups', function (Blueprint $table) {
            $table->renameColumn('add_replay_to', 'add_reply_to');
        });
    }

    public function down(): void
    {
        Schema::table('pensoft_mails_groups', function (Blueprint $table) {
            $table->renameColumn('add_reply_to', 'add_replay_to');
        });
    }
}
