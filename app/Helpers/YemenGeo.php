<?php

namespace App\Helpers;

class YemenGeo
{
    public static function getProvinces(): array
    {
        return [
            'Sana\'a' => 'صنعاء',
            'Aden' => 'عدن',
            'Taiz' => 'تعز',
            'Hodeidah' => 'الحديدة',
            'Ibb' => 'إب',
            'Dhamar' => 'ذمار',
            'Hajjah' => 'حجة',
            'Amran' => 'عمران',
            'Al Bayda' => 'البيضاء',
            'Saada' => 'صعدة',
            'Al Mahwit' => 'المحويت',
            'Raymah' => 'ريمة',
            'Marib' => 'مأرب',
            'Al Jawf' => 'الجوف',
            'Shabwah' => 'شبوة',
            'Hadramaut' => 'حضرموت',
            'Al Mahrah' => 'المهرة',
            'Abyan' => 'أبين',
            'Lahj' => 'لحج',
            'Al Dhale' => 'الضالع',
            'Socotra' => 'سقطرى',
        ];
    }

    public static function getDistricts(string $province): array
    {
        $districts = [
            'Sana\'a' => [
                'Old City' => 'صنعاء القديمة',
                'Al Wahdah' => 'الوحدة',
                'As Sabain' => 'السبعين',
                'Assafi\'yah' => 'الصافية',
                'At Tahrir' => 'التحرير',
                'Ath\'thaorah' => 'الثورة',
                'Az\'zal' => 'آزال',
                'Bani Al Harith' => 'بني الحارث',
                'Ma\'ain' => 'معين',
                'Shu\'aub' => 'شعوب',
            ],
            'Aden' => [
                'Al Buraiqeh' => 'البريقة',
                'Al Mansura' => 'المنصورة',
                'Al Mualla' => 'المعلا',
                'Ash Sheikh Outhman' => 'الشيخ عثمان',
                'Attawahi' => 'التواهي',
                'Crater' => 'كريتر',
                'Dar Sad' => 'دار سعد',
                'Khor Maksar' => 'خور مكسر',
            ],
            // Add more districts as needed or keep it simple for now
        ];

        return $districts[$province] ?? [];
    }
}
