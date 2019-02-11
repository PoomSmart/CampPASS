<?php

namespace App\Imports;

use App\Province;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProvincesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Province([
            'name_en' => $row['name_en'],
            'name_th' => $row['name_th'],
            'zipcode_prefix' => $row['zipcode_prefix'],
        ]);
    }
}
