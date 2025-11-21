<?php

namespace App\Exports;

use App\Models\Patient;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PatientsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        $clinicId = Session::get('clinic_id');

        // Eager load the address relationship
        $patients = Patient::with('address') // assumes Patient hasOne Address
            ->where('clinic_id', $clinicId)
            ->get();

        return $patients->map(function ($p) {
            $addr = $p->address;

            // Only load the names, safely handling nulls
            $fullAddress = $addr
                ? trim(
                    ($addr->street ?? '') . ', ' .
                    ($addr->barangay_name ?? '') . ', ' .
                    ($addr->city_name ?? '') . ', ' .
                    ($addr->province_name ?? '')
                )
                : '';

            return [
                'patient_id'      => $p->patient_id,
                'first_name'      => $p->first_name,
                'middle_name'     => $p->middle_name,
                'last_name'       => $p->last_name,
                'email'           => $p->email,
                'mobile_no'       => $p->mobile_no,
                'sex'             => $p->sex,
                'civil_status'    => $p->civil_status,
                'date_of_birth'   => $p->date_of_birth,
                'weight'          => $p->weight,
                'height'          => $p->height,
                'profile_picture' => $p->profile_picture,
                'referral'        => $p->referral,
                'occupation'      => $p->occupation,
                'company'         => $p->company,
                'school'          => $p->school,
                'full_name'       => trim($p->first_name . ' ' . $p->middle_name . ' ' . $p->last_name),
                'address'         => $fullAddress,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Patient ID',
            'First Name',
            'Middle Name',
            'Last Name',
            'Email',
            'Mobile No',
            'Sex',
            'Civil Status',
            'Date of Birth',
            'Weight',
            'Height',
            'Profile Picture',
            'Referral',
            'Occupation',
            'Company',
            'School',
            'Full Name',
            'Address',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:R1')->getFont()->setBold(true);
        $sheet->getStyle('A1:R1')->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFDCE6F1');
        $sheet->getStyle('A1:R1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }
}
