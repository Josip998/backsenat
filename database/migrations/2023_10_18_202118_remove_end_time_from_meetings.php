<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveEndTimeFromMeetings extends Migration
{
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn('end_time');
        });
    }

    public function down()
    {
        // If needed, you can define a "down" method to rollback the migration.
    }
}

