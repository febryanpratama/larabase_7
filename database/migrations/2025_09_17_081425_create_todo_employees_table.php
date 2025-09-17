<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTodoEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todo_employees', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('title_todo');
            $table->text('description_todo');
            $table->enum('label', ['low', 'medium', 'high'])->default('low');
            $table->enum('status', ['done', 'pending', 'onprogress'])->default('pending');
            $table->text('attachment_path')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('todo_employees');
    }
}
