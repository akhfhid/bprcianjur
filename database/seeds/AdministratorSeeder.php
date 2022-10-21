<?php

use Illuminate\Database\Seeder;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $administrator = new \App\User;
        $administrator->username = "administrator";
        $administrator->name="Site Administrator";
        $administrator->email="administrator@bprcianjur.test";
        $administrator->phone="085659200558";
        $administrator->roles=json_encode(["ADMIN"]);
        $administrator->password= \Hash::make("bprcianjur");
        $administrator->avatar="Not_Available";
        $administrator->address="cianjur, jawabarat";

        $administrator->save();
        $this->command->info("user admin berhasil diinput");
    }
}
