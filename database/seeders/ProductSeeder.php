<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product')->insert([
            'name' => 'Łóżko Domek',
            'material' => 'drewno',
            'color' => 'biały',
            'description' => 'Łóżko Domek wykonane z drewna w kolorze białym.',
            'price' => 799,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'Łóżko Domek',
            'material' => 'płyta MDF',
            'color' => 'szary',
            'description' => 'Łóżko Domek wykonane z płyty MDF w kolorze szarym.',
            'price' => 799,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'Łóżko Domek - fronty',
            'material' => 'MDF',
            'description' => 'Łóżko Domek - fronty. Komplet 2 frontów na przód oraz tył łóżka. Wykonane z płyty MDF.',
            'parent_id' => 2,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'Łóżko Domek - barierka długa',
            'material' => 'MDF',
            'description' => 'Łóżko Domek - barierka długa. Łączy przednią nogę z tylnią nogą. Wykonana z płyty MDF.',
            'parent_id' => 2,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'Łóżko Domek - barierka gitara',
            'material' => 'MDF',
            'description' => 'Łóżko Domek - barierka gitara. Zawiera wycięcie będące wejściem do łóżka. Łączy przednią nogę z tylnią nogą. Wykonana z płyty MDF.',
            'parent_id' => 2,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'Łóżko Domek - poprzeczka górna',
            'material' => 'MDF',
            'description' => 'Łóżko Domek - poprzeczka górna. Łączy kontrukcję nóg u góry. Wykonana z płyty MDF.',
            'parent_id' => 2,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'Łóżko Domek - poprzeczki boczne',
            'material' => 'MDF',
            'description' => 'Łóżko Domek - poprzeczki boczne. Łączą kontrukcję nóg nad obiema długimi barierkami. Wykonane z płyty MDF.',
            'parent_id' => 2,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'Łóżko Domek - nogi',
            'material' => 'drewno',
            'description' => 'Łóżko Domek - nogi. Komplet 4 sztuk, przednich i tylnych. Wykonane z drewna.',
            'parent_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'Łóżko Domek - barierka krótka',
            'material' => 'drewno',
            'description' => 'Łóżko Domek - barierka krótka. Łączy 1 przednie lub 1 tylnie nogi. Wykonana z drewna.',
            'parent_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'Łóżko Domek - barierka długa',
            'material' => 'drewno',
            'description' => 'Łóżko Domek - barierka długa. Łączy przednią nogę z tylnią nogą. Wykonana z drewna.',
            'parent_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'Łóżko Domek - barierka gitara',
            'material' => 'drewno',
            'description' => 'Łóżko Domek - barierka gitara. Zawiera wycięcie będące wejściem do łóżka. Łączy przednią nogę z tylnią nogą. Wykonana z drewna.',
            'parent_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'Łóżko Domek - poprzeczka górna',
            'material' => 'drewno',
            'description' => 'Łóżko Domek - poprzeczka górna. Łączy kontrukcję nóg u góry. Wykonana z drewna.',
            'parent_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'Łóżko Domek - poprzeczki boczne',
            'material' => 'drewno',
            'description' => 'Łóżko Domek - poprzeczki boczne. 2 sztuki - łączą kontrukcję nóg nad obiema długimi barierkami. Wykonane z drewna.',
            'parent_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'stelaż',
            'material' => 'drewno',
            'description' => 'Komplet 14 elementów potrzebnych do stworzenia stelaża dla łóżka.',
            'parent_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'szczebelek',
            'material' => 'drewno',
            'description' => 'Używany jako element do wytwarzania drewnianych barierek',
            'parent_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }

}
