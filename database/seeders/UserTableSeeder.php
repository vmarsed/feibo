<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 因为 App/Models/User.php 中有 use HasFactory 的 Trait
        // 所以 User::factory() 这里才可以用
        User::factory()->count(50)->create();

        // 以下代码表示
        // 生成后对一号用户进行更新, 目的是方便后面我们使用此账号进行登录
        // 其实是因为, 填充前需要 migrate:refresh 清空数据库里的数据
        // 所以要设定一个熟悉的账户用于登录
        // 密码可以看 UserFactory 里定义的,就是 Password
        $user=User::find(1);
        $user->name = 'Marson';
        $user->email = 'Marson@google.com';
        $user->save();

        // 定义完自己的 Seeder 要到同目录下的 DatabaseSeeder 中
        // 调用这个自定义的 Seeder
    }
}
