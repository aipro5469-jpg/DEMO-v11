<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Agent;
use App\Models\Branch;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample agents first
        $agent1 = Agent::create([
            'name' => 'وكيل صنعاء',
            'phone' => '777666666',
            'region' => 'صنعاء',
            'is_active' => true,
        ]);

        $agent2 = Agent::create([
            'name' => 'وكيل عدن',
            'phone' => '777777777',
            'region' => 'عدن',
            'is_active' => true,
        ]);

        // Create sample branches
        $branch1 = Branch::create([
            'name' => 'فرع صنعاء الرئيسي',
            'location' => 'شارع الستين، صنعاء',
            'is_active' => true,
        ]);

        $branch2 = Branch::create([
            'name' => 'فرع عدن',
            'location' => 'المعلا، عدن',
            'is_active' => true,
        ]);

        // Create sample clients
        $clients = [
            [
                'name' => 'محل الجبلي للدراجات',
                'phone' => ['777123456', '734123456'],
                'email' => 'jabali.bikes@example.com',
                'category' => 'تاجر دراجات نارية',
                'agent_id' => $agent1->id,
                'branch_id' => $branch1->id,
                'address' => 'شارع الستين، حدة',
                'gps_location' => '15.3694,44.1910',
                'province' => 'صنعاء',
                'district' => 'حدة',
                'importance' => 'A',
                'is_agent' => false,
                'loyalty_level' => 'high',
                'notes' => 'عميل مميز، يطلب بشكل منتظم',
            ],
            [
                'name' => 'ورشة النجاح للصيانة',
                'phone' => ['777234567'],
                'email' => null,
                'category' => 'ورشة / ميكانيكي',
                'agent_id' => $agent1->id,
                'branch_id' => $branch1->id,
                'address' => 'شارع الزبيري',
                'gps_location' => '15.3650,44.1800',
                'province' => 'صنعاء',
                'district' => 'التحرير',
                'importance' => 'B',
                'is_agent' => false,
                'loyalty_level' => 'medium',
                'notes' => 'ورشة متوسطة الحجم',
            ],
            [
                'name' => 'معرض الأمل للدراجات',
                'phone' => ['777345678', '734345678'],
                'email' => 'alamal.showroom@example.com',
                'category' => 'معرض دراجات',
                'agent_id' => $agent2->id,
                'branch_id' => $branch2->id,
                'address' => 'شارع المعلا الرئيسي',
                'gps_location' => '12.7855,45.0187',
                'province' => 'عدن',
                'district' => 'المعلا',
                'importance' => 'A',
                'is_agent' => true,
                'loyalty_level' => 'high',
                'notes' => 'وكيل معتمد، معرض كبير',
            ],
            [
                'name' => 'محل قطع الغيار المتحدة',
                'phone' => ['777456789'],
                'email' => null,
                'category' => 'تاجر قطع غيار',
                'agent_id' => $agent1->id,
                'branch_id' => $branch1->id,
                'address' => 'سوق الطلح',
                'gps_location' => '15.3500,44.1700',
                'province' => 'صنعاء',
                'district' => 'الصافية',
                'importance' => 'B',
                'is_agent' => false,
                'loyalty_level' => 'medium',
                'notes' => 'يتعامل بالجملة',
            ],
            [
                'name' => 'ورشة الخبير للإطارات',
                'phone' => ['777567890'],
                'email' => 'expert.tires@example.com',
                'category' => 'ورشة / ميكانيكي',
                'agent_id' => $agent1->id,
                'branch_id' => $branch1->id,
                'address' => 'شارع الحصبة',
                'gps_location' => '15.3400,44.1600',
                'province' => 'صنعاء',
                'district' => 'الحصبة',
                'importance' => 'C',
                'is_agent' => false,
                'loyalty_level' => 'low',
                'notes' => 'متخصص في الإطارات',
            ],
            [
                'name' => 'مركز السرعة للخدمات',
                'phone' => ['777678901', '734678901'],
                'email' => 'speed.center@example.com',
                'category' => 'مركز خدمة',
                'agent_id' => $agent2->id,
                'branch_id' => $branch2->id,
                'address' => 'كريتر، عدن',
                'gps_location' => '12.7800,45.0300',
                'province' => 'عدن',
                'district' => 'كريتر',
                'importance' => 'A',
                'is_agent' => false,
                'loyalty_level' => 'high',
                'notes' => 'مركز خدمة شامل',
            ],
            [
                'name' => 'تجارة الأخوين للدراجات',
                'phone' => ['777789012'],
                'email' => null,
                'category' => 'تاجر دراجات نارية',
                'agent_id' => $agent1->id,
                'branch_id' => $branch1->id,
                'address' => 'شارع الرينة',
                'gps_location' => '15.3300,44.1500',
                'province' => 'صنعاء',
                'district' => 'شعوب',
                'importance' => 'B',
                'is_agent' => false,
                'loyalty_level' => 'medium',
                'notes' => 'تاجر جملة وتجزئة',
            ],
            [
                'name' => 'ورشة ميكانيكي',
                'phone' => ['777890123'],
                'email' => 'alfaress.accessories@example.com',
                'category' => 'ورشة / ميكانيكي',
                'agent_id' => $agent1->id,
                'branch_id' => $branch1->id,
                'address' => 'سوق باب اليمن',
                'gps_location' => '15.3600,44.1900',
                'province' => 'صنعاء',
                'district' => 'القاع',
                'importance' => 'C',
                'is_agent' => false,
                'loyalty_level' => 'low',
                'notes' => 'ورشة ميكانيكي',
            ],
        ];

        foreach ($clients as $clientData) {
            Client::create($clientData);
        }
    }
}
