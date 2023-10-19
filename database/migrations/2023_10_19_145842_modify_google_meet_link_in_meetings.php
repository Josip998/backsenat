<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyGoogleMeetLinkInMeetings extends Migration
{
    public function up()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('google_meet_link')->default('Not Online')->change();
        });
    }

    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('google_meet_link')->nullable(false)->change();
        });
    }
}

