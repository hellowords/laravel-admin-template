<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create('user_profiles', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('User_id')->index();
            $table->string('real_name')->nullable()->comment('姓名')->index();
            $table->date('birthday')->nullable()->comment('生日');
            $table->string('avatar')->default('/images/faces/avatar6.jpg')->comment('头像路径');
            $table->string('id_card')->nullable()->comment('身份证');
            $table->string('phone')->nullable()->comment('手机');
            $table->tinyInteger('sex')->default(0)->comment('性别');
            $table->timestamp('last_active_at')->nullable()->comment('上次活动时间');
            $table->ipAddress('last_active_ip')->nullable()->comment('上次登录IP');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::dropIfExists('user_profiles');
    }
}
