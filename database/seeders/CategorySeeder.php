<?php

namespace Database\Seeders;

use App\Category;
use App\Filter;
use App\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get("database/seeders/Category.json");
        $categories = json_decode($json);
        foreach ($categories as $category => $subCategories) {
            $detail = explode('|', $category);
                    $image = null;
                    if(count($detail) >1){
                        $image =$detail['1'];
                    }
                    $name = $detail['0'];
            $cat = Category::create([
                'name' => $name,
                'cat_image' => $image
            ]);
            foreach ($subCategories as $subCategory => $filters) {
                    $detail = explode('|', $subCategory);
                    $image = null;
                    if(count($detail) >1){
                        $image =$detail['1'];
                    }
                    $name = $detail['0'];
                    $subCat = $cat->subCategory()->create([
                        'name' => $name,
                        'cat_image' => $image
                    ]);
                    foreach ($filters as $filter) {
                        $subCat->filters()->create([
                            'name' => $filter->name,
                            'cat_id' => $cat->id
                        ]);
                    }
                }
            }
        }
    }
