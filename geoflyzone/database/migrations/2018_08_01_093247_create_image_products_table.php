<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImageProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->text('original_image');
            $table->text('product_image')->nullable();
            $table->text('product_image_thumb')->nullable();
            $table->text('product_image_small')->nullable();
            $table->integer('mark_as_main')->default(0);
            $table->integer('display_order')->nullable();
            $table->integer('display_status')->nullable();
            $table->integer('attribute_id');
            $table->integer('attribute_value_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('image_products');
    }
}
