<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthorizationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // The roles table.
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name'); // The name field is mandatory.
            $table->string('label')->nullable();
            $table->timestamps();
        });

        // The permissions table.
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name'); // The name field is mandatory.
            $table->string('label')->nullable();
            $table->text('closure')->nullable();
            $table->timestamps();
        });

        // The role permissions pivot table.
        Schema::create('permission_role', function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });

        // The specific user permissions table.
        Schema::create('permission_user', function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->primary(['permission_id', 'user_id']);
        });

        // The role user pivot table.
        Schema::create('role_user', function (Blueprint $table) {
            $table->integer('role_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->primary(['role_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('role_user');
        Schema::drop('permission_role');
        Schema::drop('permission_user');
        Schema::drop('permissions');
        Schema::drop('roles');
    }
}
