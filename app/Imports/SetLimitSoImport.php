<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SetLimitSoImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public $data;
    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row) {
            $data[] = [
                'plu' => $row[0],
                'desc' => $row[1],
                'lokasi' => $row[2],
                'div' => $row[3],
                'dept' => $row[4],
                'kat' => $row[5],
                'toko' => $row[6],
                'gudang' => $row[7],
                'total_plano' => $row[8],
            ];
        }

        $this->data = $data;
    }
}
