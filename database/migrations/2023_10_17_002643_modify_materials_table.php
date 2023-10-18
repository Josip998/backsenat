<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
        });
    }
    
    public function down()
    {
        // Define how to revert the changes if necessary
    }
    
};
