<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
//为啥增加以下 Model类，这玩意不是在 App\Models\ 的吗
// App\Models\ 这是目录， 这里导入了我不懂的类
// 代码中将用到 Model::unguard() 和 Model::reguard() 两个静态方法
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        Model::unguard();
        $this->call(UserTableSeeder::class);
        $this->call(StatusesTableSeeder::class);
        $this->call(FollowersTableSeeder::class);
        Model::reguard();
    }
}
