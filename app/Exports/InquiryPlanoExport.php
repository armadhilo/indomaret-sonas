<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class InquiryPlanoExport implements FromView, WithColumnWidths
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
        return view('pdf.inquiry-plano-sonas', [
            'data' => $this->data, // Pass data to the view
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 15,
        ];
    }

}
