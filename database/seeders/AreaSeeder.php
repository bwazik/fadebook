<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            ['name' => 'القاهرة', 'name_en' => 'Cairo'],
            ['name' => 'الجيزة', 'name_en' => 'Giza'],
            ['name' => 'الإسكندرية', 'name_en' => 'Alexandria'],
            ['name' => 'المنصورة', 'name_en' => 'Mansoura'],
            ['name' => 'طنطا', 'name_en' => 'Tanta'],
            ['name' => 'أسيوط', 'name_en' => 'Assiut'],
            ['name' => 'الأقصر', 'name_en' => 'Luxor'],
            ['name' => 'أسوان', 'name_en' => 'Aswan'],
            ['name' => 'الزقازيق', 'name_en' => 'Zagazig'],
            ['name' => 'دمياط', 'name_en' => 'Damietta'],
            ['name' => 'بورسعيد', 'name_en' => 'Port Said'],
            ['name' => 'السويس', 'name_en' => 'Suez'],
            ['name' => 'الإسماعيلية', 'name_en' => 'Ismailia'],
            ['name' => 'المنيا', 'name_en' => 'Minya'],
            ['name' => 'سوهاج', 'name_en' => 'Sohag'],
            ['name' => 'قنا', 'name_en' => 'Qena'],
            ['name' => 'شرم الشيخ', 'name_en' => 'Sharm El Sheikh'],
            ['name' => 'الغردقة', 'name_en' => 'Hurghada'],
            ['name' => 'العريش', 'name_en' => 'Arish'],
            ['name' => 'الفيوم', 'name_en' => 'Fayyum'],
        ];

        foreach ($areas as $area) {
            Area::updateOrCreate(
                ['slug' => Str::slug($area['name_en'])],
                [
                    'name' => $area['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
