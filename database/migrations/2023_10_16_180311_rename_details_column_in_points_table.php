<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameDetailsColumnInPointsTable extends Migration
{
    public function up()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->renameColumn('description', 'details');
        });
    }

    public function down()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->renameColumn('details', 'description');
        });
    }
}

