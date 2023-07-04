<?php

namespace App\Exports;

use App\Models\Facilities;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class FacilityExport implements FromQuery, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithEvents
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
            return Facilities::query()->with(['request', 'request.user', 'introduction'])->where('title', '=', $this->title);
        else
            return Facilities::query()->with(['request', 'request.user', 'introduction']);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'شناسه','عنوان','نوع تسهیلات','درخواست دهنده','دانش بنیان','نوع دانش بنیان','اتمام شده'
        ];

    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($facility): array
    {
        return [
            $facility->request->shenaseh,
            $facility->title,
            $facility->type_f,
            $facility->request->user->name . ' ' . $facility->request->user->family,
            $facility->introduction[0]->is_knowledge,
            $facility->introduction[0]->area,
            $facility->request->is_finished,
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
