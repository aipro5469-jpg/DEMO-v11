<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\ItemResource\Widgets\ItemStockChartWidget;
use App\Models\Category;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;
use Carbon\Carbon;

class ItemStockChartWidgetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_calculates_stock_history_correctly_including_adjustments()
    {
        // 1. Setup Data
        // Manually create user to avoid Factory/Schema mismatch
        $user = User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'uuid' => (string) Str::uuid(),
        ]);

        $category = Category::create(['name' => 'Test Cat', 'code' => 'TC', 'parent_id' => null]);
        $item = Item::create([
            'name' => 'Test Item',
            'category_id' => $category->id,
            'total_stock' => 0, // Starts at 0
        ]);

        // Fix time to ensure consistency
        $now = Carbon::now();
        Carbon::setTestNow($now);

        // 2. Create Movements
        // Day 1: In +10
        $m1 = StockMovement::create([
            'item_id' => $item->id,
            'type' => 'in',
            'quantity' => 10,
            'created_by' => $user->id,
        ]);
        // Update timestamp manually as it's not fillable
        $m1->created_at = $now->copy()->subDays(5);
        $m1->save();

        // Day 2: Out -3
        $m2 = StockMovement::create([
            'item_id' => $item->id,
            'type' => 'out',
            'quantity' => 3,
            'created_by' => $user->id,
        ]);
        $m2->created_at = $now->copy()->subDays(4);
        $m2->save();

        // Day 3: Adjustment -2 (Stock Lost)
        $m3 = StockMovement::create([
            'item_id' => $item->id,
            'type' => 'adjustment',
            'quantity' => -2,
            'created_by' => $user->id,
        ]);
        $m3->created_at = $now->copy()->subDays(3);
        $m3->save();

        // Day 4: Adjustment +5 (Stock Found)
        $m4 = StockMovement::create([
            'item_id' => $item->id,
            'type' => 'adjustment',
            'quantity' => 5,
            'created_by' => $user->id,
        ]);
        $m4->created_at = $now->copy()->subDays(2);
        $m4->save();

        // Refresh item to get updated total_stock (should be 10)
        $item->refresh();
        $this->assertEquals(10, $item->total_stock, 'Total stock in DB should be correct');

        // 3. Instantiate Widget
        $widget = new TestableItemStockChartWidget();
        $widget->record = $item;

        // 4. Get Data
        $chartData = $widget->getData();
        $dataset = $chartData['datasets'][0]['data'];
        $labels = $chartData['labels'];

        // 5. Verify Data Points

        // Helper to find data for a specific date
        $findDataForDate = function($date) use ($labels, $dataset) {
            $label = $date->format('d M');
            $index = array_search($label, $labels);
            if ($index === false) {
                 return null;
            }
            return $dataset[$index];
        };

        // Day 1 (5 days ago): End of day stock = 10
        $stockDay1 = $findDataForDate($now->copy()->subDays(5));

        // Day 2 (4 days ago): End of day stock = 10 - 3 = 7
        $stockDay2 = $findDataForDate($now->copy()->subDays(4));

        // Day 3 (3 days ago): End of day stock = 7 - 2 = 5
        $stockDay3 = $findDataForDate($now->copy()->subDays(3));

        // Day 4 (2 days ago): End of day stock = 5 + 5 = 10
        $stockDay4 = $findDataForDate($now->copy()->subDays(2));

        // Today: 10
        $stockToday = $findDataForDate($now);

        // Assertions
        $this->assertEquals(10, $stockDay1, 'Stock after IN should be 10');
        $this->assertEquals(7, $stockDay2, 'Stock after OUT should be 7');
        $this->assertEquals(5, $stockDay3, 'Stock after ADJ(-2) should be 5');
        $this->assertEquals(10, $stockDay4, 'Stock after ADJ(+5) should be 10');
        $this->assertEquals(10, $stockToday, 'Stock today should be 10');
    }
}

// Subclass to expose protected method
class TestableItemStockChartWidget extends ItemStockChartWidget
{
    public function getData(): array
    {
        return parent::getData();
    }
}
