<?php namespace Pensoft\Mails\Updates;

use Schema;
use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdatePensoftMailsGroups3 extends Migration
{
    public function up(): void
    {
        Schema::table('pensoft_mails_groups', function (Blueprint $table) {
            $table->string('replace_from')->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->string('replace_to')->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->string('add_reply_to')->nullable()->unsigned(false)->default(null)->comment(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('pensoft_mails_groups', function (Blueprint $table) {
            $table->text('replace_from')->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->text('replace_to')->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->text('add_reply_to')->nullable()->unsigned(false)->default(null)->comment(null)->change();
        });
    }
}
