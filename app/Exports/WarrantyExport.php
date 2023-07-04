<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Warranty;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class WarrantyExport implements FromQuery, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithEvents
{
    /**
     * EmployeeExport constructor
     */
    public function __construct(string $title = null)
    {
        $this->title = $title;
    }

    public function query()
    {
        if ($this->title)
            return Warranty::query()->with(['request', 'request.user'])->where('title', '=', $this->title);
        else
            return Warranty::query()->with(['request', 'request.user']);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'شناسه','عنوان','نوع ضمانتنامه','درخواست دهنده','اتمام شده'
        ];

    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($warranty): array
    {
        return [
            $warranty->request->shenaseh,
            $warranty->title,
            $warranty->type_w,
            $warranty->request->user->name . ' ' . $warranty->request->user->family,
            $warranty->request->is_finished,
        ];
    }

    public function columnFormats(): array
    {
        return [
//            'D' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }

    public function registerEvents(): array
    {
        $styleArray = [
            'font' => [
                'bold' => true
            ],
        ];

        return [
            AfterSheet::class =>

                function (AfterSheet $event) use ($styleArray) {
                    $event->sheet
                        ->getStyle('A1:E1')
                        ->applyFromArray($styleArray);
                },
        ];

    }
}
