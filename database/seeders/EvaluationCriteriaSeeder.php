<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EvaluationTemplate;
use App\Models\EvaluationCriterion;

class EvaluationCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. تقييم العملاء (Clients)
        $clientTemplate = EvaluationTemplate::create([
            'name' => 'تقييم العملاء',
            'evaluable_type' => 'App\\Models\\Client',
            'description' => 'قالب تقييم شامل للعملاء يشمل جودة التواصل والالتزام بالدفع',
            'is_active' => true,
        ]);

        $clientCriteria = [
            ['key' => 'communication_quality', 'name' => 'جودة التواصل', 'description' => 'جودة التواصل مع العميل', 'weight' => 3],
            ['key' => 'commitment_to_payments', 'name' => 'الالتزام بالدفع', 'description' => 'التزام العميل بالدفع', 'weight' => 5],
            ['key' => 'responsiveness', 'name' => 'سرعة الاستجابة', 'description' => 'سرعة تجاوبه مع الفريق', 'weight' => 3],
            ['key' => 'cooperation_level', 'name' => 'مستوى التعاون', 'description' => 'مدى تعاونه في الحملات', 'weight' => 4],
            ['key' => 'satisfaction_level', 'name' => 'مستوى الرضا', 'description' => 'رضاه العام عن الخدمات', 'weight' => 4],
        ];

        foreach ($clientCriteria as $index => $criterion) {
            EvaluationCriterion::create([
                'evaluation_template_id' => $clientTemplate->id,
                'key' => $criterion['key'],
                'name' => $criterion['name'],
                'description' => $criterion['description'],
                'weight' => $criterion['weight'],
                'min_value' => 1,
                'max_value' => 5,
                'order' => $index + 1,
            ]);
        }

        // 2. تقييم الوكلاء (Agents)
        $agentTemplate = EvaluationTemplate::create([
            'name' => 'تقييم الوكلاء',
            'evaluable_type' => 'App\\Models\\Agent',
            'description' => 'قالب تقييم شامل للوكلاء يشمل التواجد والكفاءة البيعية',
            'is_active' => true,
        ]);

        $agentCriteria = [
            ['key' => 'availability', 'name' => 'التواجد', 'description' => 'مدى تواجده واستجابته للمكالمات والزيارات', 'weight' => 4],
            ['key' => 'brand_representation', 'name' => 'تمثيل العلامة', 'description' => 'التزامه بالهوية البصرية للشركة', 'weight' => 3],
            ['key' => 'sales_efficiency', 'name' => 'كفاءة المبيعات', 'description' => 'كفاءة تحقيق المبيعات', 'weight' => 5],
            ['key' => 'reporting_accuracy', 'name' => 'دقة التقارير', 'description' => 'دقة إرسال التقارير', 'weight' => 4],
            ['key' => 'relationship_with_clients', 'name' => 'العلاقة مع العملاء', 'description' => 'تعامله مع العملاء النهائيين', 'weight' => 4],
        ];

        foreach ($agentCriteria as $index => $criterion) {
            EvaluationCriterion::create([
                'evaluation_template_id' => $agentTemplate->id,
                'key' => $criterion['key'],
                'name' => $criterion['name'],
                'description' => $criterion['description'],
                'weight' => $criterion['weight'],
                'min_value' => 1,
                'max_value' => 5,
                'order' => $index + 1,
            ]);
        }

        // 3. تقييم الفروع (Branches)
        $branchTemplate = EvaluationTemplate::create([
            'name' => 'تقييم الفروع',
            'evaluable_type' => 'App\\Models\\Branch',
            'description' => 'قالب تقييم شامل للفروع يشمل جودة الخدمة وأداء الفريق',
            'is_active' => true,
        ]);

        $branchCriteria = [
            ['key' => 'service_quality', 'name' => 'جودة الخدمة', 'description' => 'جودة الخدمة للعملاء', 'weight' => 5],
            ['key' => 'cleanliness_and_order', 'name' => 'النظافة والترتيب', 'description' => 'نظافة وترتيب الفرع', 'weight' => 3],
            ['key' => 'team_performance', 'name' => 'أداء الفريق', 'description' => 'أداء الموظفين', 'weight' => 4],
            ['key' => 'stock_management', 'name' => 'إدارة المخزون', 'description' => 'إدارة المخزون المحلي', 'weight' => 4],
            ['key' => 'customer_feedback', 'name' => 'تقييمات العملاء', 'description' => 'تقييمات العملاء للفرع', 'weight' => 5],
        ];

        foreach ($branchCriteria as $index => $criterion) {
            EvaluationCriterion::create([
                'evaluation_template_id' => $branchTemplate->id,
                'key' => $criterion['key'],
                'name' => $criterion['name'],
                'description' => $criterion['description'],
                'weight' => $criterion['weight'],
                'min_value' => 1,
                'max_value' => 5,
                'order' => $index + 1,
            ]);
        }

        // 4. تقييم الحملات (Campaigns)
        $campaignTemplate = EvaluationTemplate::create([
            'name' => 'تقييم الحملات',
            'evaluable_type' => 'App\\Models\\Campaign',
            'description' => 'قالب تقييم شامل للحملات يشمل التخطيط والتنفيذ',
            'is_active' => true,
        ]);

        $campaignCriteria = [
            ['key' => 'planning_quality', 'name' => 'جودة التخطيط', 'description' => 'جودة التخطيط المسبق للحملة', 'weight' => 4],
            ['key' => 'execution_efficiency', 'name' => 'كفاءة التنفيذ', 'description' => 'كفاءة التنفيذ الميداني', 'weight' => 5],
            ['key' => 'budget_efficiency', 'name' => 'كفاءة الميزانية', 'description' => 'حسن إدارة الميزانية', 'weight' => 5],
            ['key' => 'visual_impact', 'name' => 'التأثير البصري', 'description' => 'التأثير البصري والإعلاني', 'weight' => 4],
            ['key' => 'overall_success', 'name' => 'النجاح العام', 'description' => 'النجاح العام للحملة', 'weight' => 5],
        ];

        foreach ($campaignCriteria as $index => $criterion) {
            EvaluationCriterion::create([
                'evaluation_template_id' => $campaignTemplate->id,
                'key' => $criterion['key'],
                'name' => $criterion['name'],
                'description' => $criterion['description'],
                'weight' => $criterion['weight'],
                'min_value' => 1,
                'max_value' => 5,
                'order' => $index + 1,
            ]);
        }

        // 5. تقييم المستخدمين (Users)
        $userTemplate = EvaluationTemplate::create([
            'name' => 'تقييم الموظفين',
            'evaluable_type' => 'App\\Models\\User',
            'description' => 'قالب تقييم شامل للموظفين يشمل الحضور وإنجاز المهام',
            'is_active' => true,
        ]);

        $userCriteria = [
            ['key' => 'attendance', 'name' => 'الحضور', 'description' => 'الالتزام بالحضور والمواعيد', 'weight' => 5],
            ['key' => 'task_completion', 'name' => 'إنجاز المهام', 'description' => 'إنجاز المهام في الوقت المحدد', 'weight' => 5],
            ['key' => 'communication', 'name' => 'التواصل', 'description' => 'التواصل مع الفريق', 'weight' => 4],
            ['key' => 'report_quality', 'name' => 'جودة التقارير', 'description' => 'جودة التقارير الميدانية', 'weight' => 4],
            ['key' => 'initiative', 'name' => 'المبادرة', 'description' => 'المبادرة والإبداع في العمل', 'weight' => 3],
        ];

        foreach ($userCriteria as $index => $criterion) {
            EvaluationCriterion::create([
                'evaluation_template_id' => $userTemplate->id,
                'key' => $criterion['key'],
                'name' => $criterion['name'],
                'description' => $criterion['description'],
                'weight' => $criterion['weight'],
                'min_value' => 1,
                'max_value' => 5,
                'order' => $index + 1,
            ]);
        }

        // 6. تقييم المهام (Tasks)
        $taskTemplate = EvaluationTemplate::create([
            'name' => 'تقييم المهام',
            'evaluable_type' => 'App\\Models\\Task',
            'description' => 'قالب تقييم شامل للمهام يشمل وقت الإنجاز والدقة',
            'is_active' => true,
        ]);

        $taskCriteria = [
            ['key' => 'completion_time', 'name' => 'وقت الإنجاز', 'description' => 'الوقت المستغرق لإنجاز المهمة', 'weight' => 4],
            ['key' => 'accuracy', 'name' => 'الدقة', 'description' => 'دقة التنفيذ ومطابقة المطلوب', 'weight' => 5],
            ['key' => 'resource_usage', 'name' => 'استخدام الموارد', 'description' => 'كفاءة استخدام الموارد', 'weight' => 3],
            ['key' => 'report_completeness', 'name' => 'اكتمال التقرير', 'description' => 'مدى اكتمال تقرير المهمة', 'weight' => 4],
            ['key' => 'collaboration', 'name' => 'التعاون', 'description' => 'التعاون مع الفريق أثناء التنفيذ', 'weight' => 3],
        ];

        foreach ($taskCriteria as $index => $criterion) {
            EvaluationCriterion::create([
                'evaluation_template_id' => $taskTemplate->id,
                'key' => $criterion['key'],
                'name' => $criterion['name'],
                'description' => $criterion['description'],
                'weight' => $criterion['weight'],
                'min_value' => 1,
                'max_value' => 5,
                'order' => $index + 1,
            ]);
        }

        // 7. تقييم الأصول الدعائية (Ad Assets)
        $adAssetTemplate = EvaluationTemplate::create([
            'name' => 'تقييم الأصول الدعائية',
            'evaluable_type' => 'App\\Models\\AdAsset',
            'description' => 'قالب تقييم شامل للأصول الدعائية يشمل الجودة البصرية والمتانة',
            'is_active' => true,
        ]);

        $adAssetCriteria = [
            ['key' => 'visual_quality', 'name' => 'الجودة البصرية', 'description' => 'الجودة البصرية للأصل الدعائي', 'weight' => 5],
            ['key' => 'durability', 'name' => 'المتانة', 'description' => 'مدى تحمّل الأصل للعوامل الخارجية', 'weight' => 4],
            ['key' => 'brand_compliance', 'name' => 'التزام الهوية', 'description' => 'التزام الهوية البصرية للشركة', 'weight' => 5],
            ['key' => 'placement_effectiveness', 'name' => 'فعالية الموقع', 'description' => 'فعالية موقع التركيب', 'weight' => 4],
            ['key' => 'maintenance_need', 'name' => 'الحاجة للصيانة', 'description' => 'الحاجة للصيانة', 'weight' => 3],
        ];

        foreach ($adAssetCriteria as $index => $criterion) {
            EvaluationCriterion::create([
                'evaluation_template_id' => $adAssetTemplate->id,
                'key' => $criterion['key'],
                'name' => $criterion['name'],
                'description' => $criterion['description'],
                'weight' => $criterion['weight'],
                'min_value' => 1,
                'max_value' => 5,
                'order' => $index + 1,
            ]);
        }

        $this->command->info('Evaluation criteria created successfully!');
        $this->command->info('Created 7 evaluation templates with 35 criteria total.');
    }
}
