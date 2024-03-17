<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class SetLimitSoExport implements FromView, WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('pdf.set-limit-so', $this->data);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 9,
            'B' => 20,
            'C' => 7,
            'D' => 27,
            'E' => 30,
            'F' => 31,
            'G' => 5,
            'H' => 8,
            'I' => 10,
        ];
    }
    
}
