<?php

use Illuminate\Database\Seeder;
use App\Unit;
use App\Notice;
use App\Attachment;
use Faker\Factory;

class DatabaseSeeder extends Seeder
{
    function seedUnits()
    {
        $exist=Unit::where('code','118000')->first();
        if($exist) return;
        $unit=Unit::create([
            'name' => '電算中心',
            'code' => '118000',
            
            'is_class' => false,
            'level_ones' => 'iscsd00',
            'level_twos' => '',
            'admin' => 'iscsd00',
            'active' => true,
            'removed' => false,
       ]);

       $exist=Unit::where('code','118010')->first();
        if($exist) return;
        Unit::create([
            'name' => '軟體開發組',
            'code' => '118010',
            'parent' => $unit->id,
            'is_class' => false,
            'level_ones' => '',
            'level_twos' => 'iscsd00',
            'admin' => 'iscsd00',
            'active' => true,
            'removed' => false,
       ]);

    }

    function seedNotices()
    {
        $unit=Unit::where('code','118010')->first();

        $faker = Factory::create();
        foreach(range(1, 50) as $i) {
            Notice::create([
                'unit_id' =>$unit->id,
                'content' => $faker->text,
                'staff' => ( $i %2 == 0 ),
                'teacher' => ( $i %3 == 0 ),
                'student' => ( $i %5 == 0 ),
                
                'units' => '',
                'classes' => '',
                'levels' => '',
                
                'reviewed' => false,
                'reviewed_by' => '',
                'created_by' => 'ss355',
                'updated_by' => 'ss355',
            ]);
            
		}  
    }

    public function run()
    {
        $this->seedUnits();
       
    }
}
