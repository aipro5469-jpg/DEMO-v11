<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\User;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get campaigns
        $campaigns = Campaign::all();
        $clients = Client::all();
        $fieldAgents = User::role('field_agent')->get();

        if ($campaigns->isEmpty() || $clients->isEmpty() || $fieldAgents->isEmpty()) {
            $this->command->warn('Please run CampaignSeeder, ClientSeeder, and UserSeeder first');
            return;
        }

        // Tasks for first campaign (الترويج الصيفية)
        $campaign1 = $campaigns->where('title', 'حملة الترويج الصيفية')->first();

        $tasks1 = [
            [
                'title' => 'زيارة محل الجبلي للدراجات',
                'description' => 'عرض المنتجات الجديدة والعروض الصيفية',
                'status' => 'completed',
                'priority' => 'high',
                'start_at' => now()->subDays(8),
                'due_at' => now()->subDays(7),
                'assignee_id' => $fieldAgents->random()->id,
                'campaign_id' => $campaign1->id,
                'client_id' => $clients->random()->id,
                'location' => 'شارع الستين، حدة، صنعاء',
                'progress_percentage' => 100,
                'created_by' => User::where('username', 'manager')->first()->id,
            ],
            [
                'title' => 'متابعة طلبية معرض الأمل',
                'description' => 'التأكد من استلام الطلبية ومتابعة الدفع',
                'status' => 'in_progress',
                'priority' => 'high',
                'start_at' => now()->subDays(2),
                'due_at' => now()->addDays(2),
                'assignee_id' => $fieldAgents->random()->id,
                'campaign_id' => $campaign1->id,
                'client_id' => $clients->random()->id,
                'location' => 'شارع المعلا، عدن',
                'progress_percentage' => 60,
                'created_by' => User::where('username', 'manager')->first()->id,
            ],
            [
                'title' => 'تقييم ورشة النجاح للصيانة',
                'description' => 'تقييم جودة الخدمة ومستوى الرضا',
                'status' => 'pending',
                'priority' => 'medium',
                'start_at' => now()->addDays(1),
                'due_at' => now()->addDays(5),
                'assignee_id' => $fieldAgents->random()->id,
                'campaign_id' => $campaign1->id,
                'client_id' => $clients->random()->id,
                'location' => 'شارع الزبيري، صنعاء',
                'progress_percentage' => 0,
                'created_by' => User::where('username', 'manager')->first()->id,
            ],
        ];

        foreach ($tasks1 as $taskData) {
            Task::create($taskData);
        }

        // Tasks for second campaign (العودة للمدارس)
        $campaign2 = $campaigns->where('title', 'حملة العودة للمدارس')->first();

        $tasks2 = [
            [
                'title' => 'زيارة مركز السرعة للخدمات',
                'description' => 'عرض منتجات خاصة بموسم المدارس',
                'status' => 'completed',
                'priority' => 'high',
                'start_at' => now()->subDays(4),
                'due_at' => now()->subDays(3),
                'assignee_id' => $fieldAgents->random()->id,
                'campaign_id' => $campaign2->id,
                'client_id' => $clients->random()->id,
                'location' => 'كريتر، عدن',
                'progress_percentage' => 100,
                'created_by' => User::where('username', 'manager')->first()->id,
            ],
            [
                'title' => 'توصيل عينات لمحل قطع الغيار المتحدة',
                'description' => 'توصيل عينات من المنتجات الجديدة',
                'status' => 'in_progress',
                'priority' => 'medium',
                'start_at' => now()->subDays(1),
                'due_at' => now()->addDays(3),
                'assignee_id' => $fieldAgents->random()->id,
                'campaign_id' => $campaign2->id,
                'client_id' => $clients->random()->id,
                'location' => 'سوق الطلح، صنعاء',
                'progress_percentage' => 40,
                'created_by' => User::where('username', 'manager')->first()->id,
            ],
        ];

        foreach ($tasks2 as $taskData) {
            Task::create($taskData);
        }

        // Tasks for third campaign (التوسع في عدن)
        $campaign3 = $campaigns->where('title', 'حملة التوسع في عدن')->first();

        $tasks3 = [
            [
                'title' => 'استكشاف أسواق جديدة في عدن',
                'description' => 'البحث عن عملاء محتملين في مناطق جديدة',
                'status' => 'in_progress',
                'priority' => 'high',
                'start_at' => now()->subDays(10),
                'due_at' => now()->addDays(10),
                'assignee_id' => $fieldAgents->random()->id,
                'campaign_id' => $campaign3->id,
                'client_id' => null,
                'location' => 'عدن',
                'progress_percentage' => 70,
                'created_by' => User::where('username', 'manager')->first()->id,
            ],
            [
                'title' => 'التواصل مع وكلاء محتملين',
                'description' => 'عقد اجتماعات مع وكلاء محتملين',
                'status' => 'pending',
                'priority' => 'high',
                'start_at' => now()->addDays(2),
                'due_at' => now()->addDays(15),
                'assignee_id' => $fieldAgents->random()->id,
                'campaign_id' => $campaign3->id,
                'client_id' => null,
                'location' => 'عدن',
                'progress_percentage' => 0,
                'created_by' => User::where('username', 'manager')->first()->id,
            ],
        ];

        foreach ($tasks3 as $taskData) {
            Task::create($taskData);
        }

        // Additional standalone tasks
        $standaloneTasks = [
            [
                'title' => 'جرد المخزون الشهري',
                'description' => 'إجراء جرد شامل للمخزون',
                'status' => 'pending',
                'priority' => 'medium',
                'start_at' => now()->addDays(5),
                'due_at' => now()->addDays(10),
                'assignee_id' => $fieldAgents->random()->id,
                'campaign_id' => null,
                'client_id' => null,
                'location' => 'المستودع الرئيسي',
                'progress_percentage' => 0,
                'created_by' => User::where('username', 'manager')->first()->id,
            ],
            [
                'title' => 'تدريب فريق المبيعات',
                'description' => 'تدريب على المنتجات الجديدة',
                'status' => 'pending',
                'priority' => 'low',
                'start_at' => now()->addDays(7),
                'due_at' => now()->addDays(14),
                'assignee_id' => $fieldAgents->random()->id,
                'campaign_id' => null,
                'client_id' => null,
                'location' => 'المكتب الرئيسي',
                'progress_percentage' => 0,
                'created_by' => User::where('username', 'manager')->first()->id,
            ],
        ];

        foreach ($standaloneTasks as $taskData) {
            Task::create($taskData);
        }
    }
}
