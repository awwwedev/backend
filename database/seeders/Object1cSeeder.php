<?php

namespace Database\Seeders;

use App\Models\Object1c;
use Illuminate\Database\Seeder;

class Object1cSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $object1=new Object1c();
        $object2=new Object1c();
        $object3=new Object1c();
        $object1->name='офис №17';
        $object2->name='офис №43';
        $object3->name='офис №11';
        $object1->id_1c='000000351';
        $object2->id_1c='000000124';
        $object3->id_1c='000000057';
        $object1->user_id=2;
        $object2->user_id=2;
        $object3->user_id=3;
        $object1->save();
        $object2->save();
        $object3->save();
    }
}
