<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campaign;
use App\Models\User;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get manager user
        $manager = User::where('username', 'manager')->first();

        $campaigns = [
            [
                'title' => 'حملة الترويج الصيفية',
                'campaign_type' => 'promotional',
                'objective' => 'زيادة المبيعات خلال فترة الصيف بنسبة 30%',
                'status' => 'active',
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(50),
                'budget' => 50000.00,
                'owner_id' => $manager->id,
                'result_summary' => [
                    'visits_completed' => 45,
                    'new_clients' => 12,
                    'sales_increase' => '15%',
                ],
            ],
            [
                'title' => 'حملة العودة للمدارس',
                'campaign_type' => 'seasonal',
                'objective' => 'استهداف المدارس والمؤسسات التعليمية',
                'status' => 'active',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
                'budget' => 30000.00,
                'owner_id' => $manager->id,
                'result_summary' => [
                    'visits_completed' => 20,
                    'new_clients' => 8,
                ],
            ],
            [
                'title' => 'حملة التوسع في عدن',
                'campaign_type' => 'expansion',
                'objective' => 'فتح أسواق جديدة في محافظة عدن',
                'status' => 'active',
                'start_date' => now()->subDays(15),
                'end_date' => now()->addDays(45),
                'budget' => 75000.00,
                'owner_id' => $manager->id,
                'result_summary' => [
                    'visits_completed' => 30,
                    'new_clients' => 15,
                    'new_agents' => 2,
                ],
            ],
            [
                'title' => 'حملة الولاء للعملاء',
                'campaign_type' => 'retention',
                'objective' => 'تعزيز العلاقة مع العملاء الحاليين',
                'status' => 'paused',
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(30),
                'budget' => 20000.00,
                'owner_id' => $manager->id,
                'result_summary' => [
                    'visits_completed' => 50,
                    'satisfaction_rate' => '85%',
                ],
            ],
            [
                'title' => 'حملة إطلاق منتج جديد',
                'campaign_type' => 'product_launch',
                'objective' => 'تعريف العملاء بالمنتجات الجديدة',
                'status' => 'draft',
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(70),
                'budget' => 40000.00,
                'owner_id' => $manager->id,
            ],
        ];

        foreach ($campaigns as $campaignData) {
            Campaign::create($campaignData);
        }
    }
}
