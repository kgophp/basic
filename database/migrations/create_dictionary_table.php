<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDictionaryTable  extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_dictionaries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->default(0);
            $table->string('dict_type',100)->comment('字典类型');
            $table->string('dict_key',100)->comment('字典key值');
            $table->string('dict_value',4000)->comment('字典value值');
            $table->tinyInteger('sort')->default(0)->comment('顺序');
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();

            $table->unique(['dict_type','dict_key']);
            $table->unique(['parent_id','dict_key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_dictionaries');
    }
}
