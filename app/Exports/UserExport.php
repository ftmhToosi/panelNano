<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class UserExport implements FromQuery, WithMapping, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithEvents
{

    /**
     * EmployeeExport constructor
     */
    public function __construct(string $type = null, string $phone = null, string $national_code = null)
    {
        $this->type = $type;
        $this->phone = $phone;
        $this->national_code = $national_code;
    }

    public function query()
    {
        if ($this->type)
            return User::query()->with(['profilegenuine', 'profilelagal'])->where('type', '=', $this->type);
        if ($this->phone)
            return User::query()->with(['profilegenuine', 'profilelagal'])->where('phone', '=', $this->phone);
        if ($this->national_code)
            return User::query()->with(['profilegenuine', 'profilelagal'])->where('national_code', '=', $this->national_code);
        else
            return User::query()->with(['profilegenuine', 'profilelagal'])->where('type', '=', 'genuine')
                ->orWhere('type', '=', 'legal');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        if ($this->type == 'genuine')
            return [
                'نام', 'کدملی', 'شماره موبایل', 'ایمیل', 'نام پدر', 'شماره شناسنامه', 'محل صدور', 'سری و سریال شناسنامه', 'ملیت',
                'جنسیت', 'وضعیت تاهل', 'نحصیلات', 'رشته', 'شغل'
            ];
        if ($this->type == 'legal')
            return [
                'نام', 'شناسه ملی', 'نام شرکت', 'شماره موبایل', 'ایمیل', 'نوع شخصیت حقوقی', 'محل ثبت',
//                'تاریخ تاسیس',
                'دارندگان حق امضا', 'سرمایه اولیه', 'سرمایه فعلی', 'موضوع فعالیت', 'نام نماینده شرکت', 'تلفن ثابت', 'سایت'
            ];
        else
            return [
                'نام', 'کدملی', 'شناسه ملی', 'نام شرکت', 'شماره موبایل', 'ایمیل','نوع شخص'
            ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($user): array
    {
        if ($this->type == 'genuine')
            return [
                $user->name . ' ' . $user->family,
                $user->national_code,
                // Date::dateTimeToExcel($export->export->date),
                $user->phone,
                $user->email,
                $user->profilegenuine->father_name ?? '',
                $user->profilegenuine->number_certificate ?? '',
                $user->profilegenuine->place_issue ?? '',
                $user->profilegenuine->series_certificate ?? '',
                $user->profilegenuine->nationality ?? '',
                $user->profilegenuine->gender ?? '',
                $user->profilegenuine->marital ?? '',
                $user->profilegenuine->education ?? '',
                $user->profilegenuine->study ?? '',
                $user->profilegenuine->job ?? '',
            ];
        if ($this->type == 'legal')
            return [
                $user->name . ' ' . $user->family,
                $user->national_company,
                $user->company_name,
                $user->phone,
                $user->email,
                $user->profilelagal->type_legal ?? '',
                $user->profilelagal->place_registration ?? '',
//                $user->profilelagal->establishment ?? ',
                $user->profilelagal->signed_right ?? '',
                $user->profilelagal->initial_investment ?? '',
                $user->profilelagal->fund ?? '',
                $user->profilelagal->subject_activity ?? '',
                $user->profilelagal->name_representative ?? '',
                $user->profilelagal->landline_phone ?? '',
                $user->profilelagal->site ?? '',
            ];
        else
            return [
                $user->name . ' ' . $user->family,
                $user->national_code,
                $user->national_company,
                $user->company_name,
                $user->phone,
                $user->email,
                $user->type
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
        if ($this->type == 'genuine' or $this->type == 'legal')
            return [
                AfterSheet::class =>

                    function (AfterSheet $event) use ($styleArray) {
                        $event->sheet
                            ->getStyle('A1:N1')
                            ->applyFromArray($styleArray);
                    },
            ];
        else
            return [
                AfterSheet::class =>

                    function (AfterSheet $event) use ($styleArray) {
                        $event->sheet
                            ->getStyle('A1:G1')
                            ->applyFromArray($styleArray);
                    },
            ];
    }
}
