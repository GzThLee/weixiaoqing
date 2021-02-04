<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Config;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configs')->truncate();
        collect([
            ['key_name' => 'site_title', 'display_name' => '站点名称', 'value' => '微小擎'],
            ['key_name' => 'server_ip', 'display_name' => '服务器IP', 'value' => '127.0.0.1'],
        ])->map(function ($config){
            Config::firstOrCreate($config);
        });
    }
}
