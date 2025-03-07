<?php

namespace Database\Seeders;

use App\Models\Series;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use DB;

class SeriesSeeder extends Seeder
{
    public function run()
    {
        //DB::table('series')->delete();
        Series::create(array(
            'name' => 'Document',
            'slug' => config('enums.series.document'),
            'prefix' => 'DOC',
            'max_digit' => 6,
            'starting_value' => 1
        ));
    }
}
