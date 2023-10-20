<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('component')->insert([
            'name' => 'fronty - Łóżko Domek',
            'material' => 'MDF',
            'description' => 'Komplet 2 frontów na przód oraz tył Łóżka Domek. Wykonane z płyty MDF.',
            'independent' => false,
            'height' => 160,
            'length' => 90,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component')->insert([
            'name' => 'barierka długa - łóżko',
            'material' => 'MDF',
            'description' => 'Łączy przednią nogę łóżka z tylnią nogą. Wykonana z płyty MDF.',
            'independent' => false,
            'length' => 180,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component')->insert([
            'name' => 'barierka gitara- łóżko',
            'material' => 'MDF',
            'description' => 'barierka gitara. Zawiera wycięcie będące wejściem do łóżka. Łączy przednią nogę z tylnią nogą. Wykonana z płyty MDF.',
            'independent' => false,
            'length' => 180,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component')->insert([
            'name' => 'poprzeczka górna - łóżko',
            'material' => 'MDF',
            'description' => 'Poprzeczka górna. Łączy kontrukcję nóg u góry. Wykonana z płyty MDF.',
            'independent' => false,
            'length' => 180,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component')->insert([
            'name' => 'poprzeczki boczne - łóżko',
            'material' => 'MDF',
            'description' => 'Poprzeczki boczne. Łączą kontrukcję nóg nad obiema długimi barierkami. Wykonane z płyty MDF.',
            'independent' => false,
            'length' => 180,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component')->insert([
            'name' => 'nogi - Łóżko Domek',
            'material' => 'drewno',
            'description' => 'Łóżko Domek - nogi. Komplet 4 sztuk, przednich i tylnych. Wykonane z drewna.',
            'independent' => false,
            'height' => 160,
            'length' => 90,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component')->insert([
            'name' => 'barierka długa - łóżko',
            'material' => 'drewno',
            'description' => 'Łączy przednią nogę łóżka z tylnią nogą. Wykonana z drewna.',
            'independent' => false,
            'length' => 180,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component')->insert([
            'name' => 'barierka gitara- łóżko',
            'material' => 'drewno',
            'description' => 'barierka gitara. Zawiera wycięcie będące wejściem do łóżka. Łączy przednią nogę z tylnią nogą. Wykonana z drewna.',
            'independent' => false,
            'length' => 180,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component')->insert([
            'name' => 'poprzeczka górna - łóżko',
            'material' => 'drewno',
            'description' => 'Poprzeczka górna. Łączy kontrukcję nóg u góry. Wykonana z drewna.',
            'independent' => false,
            'length' => 180,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component')->insert([
            'name' => 'poprzeczki boczne - łóżko',
            'material' => 'drewno',
            'description' => 'Poprzeczki boczne. Łączą kontrukcję nóg nad obiema długimi barierkami. Wykonane z drewna.',
            'independent' => false,
            'length' => 180,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component')->insert([
            'name' => 'stelaż',
            'material' => 'drewno',
            'description' => 'Komplet 14 elementów potrzebnych do stworzenia stelaża dla łóżka. Wymiary podane są dla pojedynczego elementu kompletu',
            'independent' => true,
            'length' => 90,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component')->insert([
            'name' => 'szczebelek',
            'material' => 'drewno',
            'description' => 'Używany jako element do wytwarzania drewnianych barierek',
            'independent' => true,
            'length' => 20,
            'width' => 6,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }
}