<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwoFactorTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('authentication_users_yubikeys', function (Blueprint $table) {
            $table->string("ccid");
            $table->string("yubikey", 12);
            $table->timestamps();
        });

        Schema::create('authentication_users_protected', function (Blueprint $table) {
            $table->string("ccid");
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
		//
	}

}
