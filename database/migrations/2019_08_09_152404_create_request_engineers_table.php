<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestEngineersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_engineers', function (Blueprint $table) {
            $table->string('id');
            $table->primary('id');
            $table->text('description');
            $table->string('work_order_id')->nullable();
            $table->string('assigned_to')->nullable();
            $table->smallInteger('status')->default(2);
            $table->date('assigned_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('work_order_id')->references('id')->on('work_orders')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_engineers');
    }
}
