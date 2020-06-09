<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('email')->default('')->unique()->comment('邮箱');
            $table->timestamp('email_verified_at')->nullable()->comment('邮箱确认时间');
            $table->string('password')->comment('密码');
            $table->integer('role_id')->default(0)->comment('角色id');
            $table->boolean('is_super')->default(false)->comment('是否是超级用户');
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
        Schema::dropIfExists('users');
    }
}
