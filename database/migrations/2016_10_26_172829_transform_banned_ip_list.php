<?php

use Illuminate\Database\Migrations\Migration;

class TransformBannedIpList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $firewall = new \FacebookAnonymousPublisher\Firewall\Firewall;

        DB::table('blocks')
            ->where('type', 'ip')
            ->get(['value'])
            ->each(function ($ip) use ($firewall) {
                $firewall->ban($ip->value);
            });

        DB::table('blocks')
            ->where('type', 'ip')
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $data = [];

        DB::table('firewalls')
            ->get(['ip'])
            ->each(function ($ip) use (&$data) {
                $data[] = [
                    'type' => 'ip',
                    'value' => inet_ntop(base64_decode($ip->ip, true)),
                ];
            });

        DB::table('blocks')->insert($data);
    }
}
