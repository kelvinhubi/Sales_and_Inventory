<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PastOrder;
use App\Models\PastOrderItem;
use App\Models\Expense;
use App\Models\Brand;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Exception;

/**
 * AI Business Intelligence Service
 * 
 * Provides AI-powered insights, predictions, and recommendations
 * for business decision-making.
 */
class AIBusinessIntelligenceService
{
    private $cacheEnabled = true;
    private $cacheTTL = 3600; // 1 hour

    /**
     * Get comprehensive business insights
     */
    public function getBusinessInsights(): array
    {
        $cacheKey = 'ai_business_insights_' . Carbon::now()->format('Y-m-d-H');
        
        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $data = $this->gatherBusinessData();
        $insights = $this->analyzeBusinessData($data);
        
        if ($this->cacheEnabled) {
            Cache::put($cacheKey, $insights, $this->cacheTTL);
        }

        return $insights;
    }

    /**
     * Gather all business data for analysis
     */
    private function gatherBusinessData(): array
    {
        $today = Carbon::now();
        $lastMonth = $today->copy()->subMonth();
        $lastWeek = $today->copy()->subWeek();

        return [
            'sales' => $this->getSalesMetrics($today, $lastMonth),
            'inventory' => $this->getInventoryMetrics(),
            'expenses' => $this->getExpenseMetrics($today, $lastMonth),
            'products' => $this->getProductPerformance(),
            'branches' => $this->getBranchPerformance(),
            'trends' => $this->getTrendAnalysis(),
        ];
    }

    /**
     * Get sales metrics
     */
    private function getSalesMetrics($today, $lastMonth): array
    {
        $thisMonthSales = PastOrder::whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->sum('total_amount');

        $lastMonthSales = PastOrder::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->sum('total_amount');

        $thisWeekSales = PastOrder::whereBetween('created_at', [
            $today->copy()->startOfWeek(),
            $today->copy()->endOfWeek()
        ])->sum('total_amount');

        $lastWeekSales = PastOrder::whereBetween('created_at', [
            $today->copy()->subWeek()->startOfWeek(),
            $today->copy()->subWeek()->endOfWeek()
        ])->sum('total_amount');

        return [
            'this_month' => $thisMonthSales,
            'last_month' => $lastMonthSales,
            'this_week' => $thisWeekSales,
            'last_week' => $lastWeekSales,
            'month_growth' => $lastMonthSales > 0 
                ? (($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100 
                : 0,
            'week_growth' => $lastWeekSales > 0 
                ? (($thisWeekSales - $lastWeekSales) / $lastWeekSales) * 100 
                : 0,
        ];
    }

    /**
     * Get inventory metrics
     */
    private function getInventoryMetrics(): array
    {
        $total = Product::count();
        $inStock = Product::where('quantity', '>=', 10)->count();
        $lowStock = Product::whereBetween('quantity', [1, 9])->count();
        $outOfStock = Product::where('quantity', '<=', 0)->count();

        $criticalProducts = Product::where('quantity', '<=', 5)
            ->where('quantity', '>', 0)
            ->get(['id', 'name', 'quantity']);

        return [
            'total_products' => $total,
            'in_stock' => $inStock,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'critical_products' => $criticalProducts,
            'stock_health' => $total > 0 ? ($inStock / $total) * 100 : 0,
        ];
    }

    /**
     * Get expense metrics
     */
    private function getExpenseMetrics($today, $lastMonth): array
    {
        $thisMonthExpenses = Expense::whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->sum('amount');

        $lastMonthExpenses = Expense::whereMonth('date', $lastMonth->month)
            ->whereYear('date', $lastMonth->year)
            ->sum('amount');

        $expensesByCategory = Expense::whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return [
            'this_month' => $thisMonthExpenses,
            'last_month' => $lastMonthExpenses,
            'growth' => $lastMonthExpenses > 0 
                ? (($thisMonthExpenses - $lastMonthExpenses) / $lastMonthExpenses) * 100 
                : 0,
            'by_category' => $expensesByCategory,
        ];
    }

    /**
     * Get product performance
     */
    private function getProductPerformance(): array
    {
        $top10 = PastOrderItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(quantity * price) as total_revenue')
            )
            ->with('product:id,name,quantity')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(10)
            ->get();

        $bottom10 = PastOrderItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(quantity * price) as total_revenue')
            )
            ->with('product:id,name,quantity')
            ->groupBy('product_id')
            ->orderBy('total_sold')
            ->take(10)
            ->get();

        return [
            'top_sellers' => $top10,
            'slow_movers' => $bottom10,
        ];
    }

    /**
     * Get branch performance
     */
    private function getBranchPerformance(): array
    {
        $branches = Branch::all()->map(function ($branch) {
            $sales = PastOrder::where('branch_id', $branch->id)
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('total_amount');

            $orders = PastOrder::where('branch_id', $branch->id)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();

            return [
                'id' => $branch->id,
                'name' => $branch->name,
                'sales' => $sales,
                'orders' => $orders,
                'avg_order_value' => $orders > 0 ? $sales / $orders : 0,
            ];
        })->sortByDesc('sales')->values();

        return [
            'all_branches' => $branches,
            'best_performer' => $branches->first(),
            'worst_performer' => $branches->last(),
        ];
    }

    /**
     * Get trend analysis
     */
    private function getTrendAnalysis(): array
    {
        $last6Months = collect(range(0, 5))->map(function ($monthsAgo) {
            $date = Carbon::now()->subMonths($monthsAgo);
            $sales = PastOrder::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_amount');

            return [
                'month' => $date->format('M Y'),
                'sales' => $sales,
            ];
        })->reverse()->values();

        return [
            'monthly_sales' => $last6Months,
            'trend' => $this->calculateTrend($last6Months),
        ];
    }

    /**
     * Calculate trend direction
     */
    private function calculateTrend($data): string
    {
        $values = $data->pluck('sales')->toArray();
        if (count($values) < 2) return 'stable';

        $firstHalf = array_slice($values, 0, ceil(count($values) / 2));
        $secondHalf = array_slice($values, ceil(count($values) / 2));

        $avgFirst = array_sum($firstHalf) / count($firstHalf);
        $avgSecond = array_sum($secondHalf) / count($secondHalf);

        if ($avgSecond > $avgFirst * 1.1) return 'growing';
        if ($avgSecond < $avgFirst * 0.9) return 'declining';
        return 'stable';
    }

    /**
     * Analyze business data and generate insights
     */
    private function analyzeBusinessData(array $data): array
    {
        $insights = [
            'summary' => $this->generateSummary($data),
            'recommendations' => $this->generateRecommendations($data),
            'alerts' => $this->generateAlerts($data),
            'opportunities' => $this->identifyOpportunities($data),
            'risks' => $this->identifyRisks($data),
            'forecast' => $this->generateForecast($data),
            'score' => $this->calculateBusinessHealthScore($data),
        ];

        return $insights;
    }

    /**
     * Generate business summary
     */
    private function generateSummary(array $data): string
    {
        $sales = $data['sales'];
        $inventory = $data['inventory'];
        $trends = $data['trends'];

        $summary = "Business Performance Summary:\n\n";

        // Sales performance
        if ($sales['month_growth'] > 10) {
            $summary .= "📈 Excellent growth this month! Sales increased by " . number_format($sales['month_growth'], 1) . "%. ";
        } elseif ($sales['month_growth'] > 0) {
            $summary .= "📊 Modest growth this month. Sales increased by " . number_format($sales['month_growth'], 1) . "%. ";
        } else {
            $summary .= "📉 Sales declined by " . number_format(abs($sales['month_growth']), 1) . "% this month. ";
        }

        // Inventory health
        if ($inventory['stock_health'] >= 70) {
            $summary .= "Your inventory is healthy with " . number_format($inventory['stock_health'], 0) . "% of products well-stocked. ";
        } else {
            $summary .= "⚠️ Inventory needs attention - only " . number_format($inventory['stock_health'], 0) . "% of products are adequately stocked. ";
        }

        // Trend
        $summary .= "\n\nOverall trend: " . ucfirst($trends['trend']) . ".";

        return $summary;
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations(array $data): array
    {
        $recommendations = [];

        // Inventory recommendations
        if ($data['inventory']['low_stock'] > 0) {
            $recommendations[] = [
                'type' => 'inventory',
                'priority' => 'high',
                'title' => 'Restock Low Inventory Items',
                'description' => "You have {$data['inventory']['low_stock']} products with low stock. Consider restocking soon.",
                'action' => 'View low stock items',
            ];
        }

        if ($data['inventory']['out_of_stock'] > 0) {
            $recommendations[] = [
                'type' => 'inventory',
                'priority' => 'critical',
                'title' => 'Out of Stock Alert',
                'description' => "{$data['inventory']['out_of_stock']} products are completely out of stock. This may lead to lost sales.",
                'action' => 'Restock immediately',
            ];
        }

        // Sales recommendations
        if ($data['sales']['week_growth'] < -10) {
            $recommendations[] = [
                'type' => 'sales',
                'priority' => 'medium',
                'title' => 'Sales Decline Investigation',
                'description' => "Sales dropped " . number_format(abs($data['sales']['week_growth']), 1) . "% this week. Review pricing and promotions.",
                'action' => 'Analyze sales data',
            ];
        }

        // Slow-moving products
        $slowMovers = $data['products']['slow_movers']->filter(function ($item) {
            return $item->total_sold < 10;
        });

        if ($slowMovers->count() > 0) {
            $recommendations[] = [
                'type' => 'product',
                'priority' => 'medium',
                'title' => 'Consider Discontinuing Slow Products',
                'description' => "{$slowMovers->count()} products have very low sales. Consider promotions or discontinuation.",
                'action' => 'Review product lineup',
            ];
        }

        // Expense control
        if ($data['expenses']['growth'] > 20) {
            $recommendations[] = [
                'type' => 'expenses',
                'priority' => 'high',
                'title' => 'Expense Spike Detected',
                'description' => "Expenses increased " . number_format($data['expenses']['growth'], 1) . "% this month. Review spending.",
                'action' => 'Analyze expenses',
            ];
        }

        return $recommendations;
    }

    /**
     * Generate alerts
     */
    private function generateAlerts(array $data): array
    {
        $alerts = [];

        // Critical stock alerts
        foreach ($data['inventory']['critical_products'] as $product) {
            $alerts[] = [
                'severity' => 'warning',
                'message' => "Low stock: {$product->name} has only {$product->quantity} units left",
                'timestamp' => Carbon::now()->toISOString(),
            ];
        }

        // Sales trend alerts
        if ($data['sales']['week_growth'] < -20) {
            $alerts[] = [
                'severity' => 'critical',
                'message' => "Sharp sales decline: " . number_format(abs($data['sales']['week_growth']), 1) . "% drop this week",
                'timestamp' => Carbon::now()->toISOString(),
            ];
        }

        return $alerts;
    }

    /**
     * Identify growth opportunities
     */
    private function identifyOpportunities(array $data): array
    {
        $opportunities = [];

        // Top sellers opportunity
        $topSellers = $data['products']['top_sellers']->take(3);
        if ($topSellers->count() > 0) {
            $products = $topSellers->pluck('product.name')->implode(', ');
            $opportunities[] = [
                'title' => 'Capitalize on Best Sellers',
                'description' => "Your top products ({$products}) are performing well. Consider increasing inventory and promotion.",
                'potential_impact' => 'high',
            ];
        }

        // Branch expansion
        if (isset($data['branches']['best_performer'])) {
            $best = $data['branches']['best_performer'];
            $opportunities[] = [
                'title' => 'Replicate Success',
                'description' => "Branch '{$best['name']}' is your top performer. Analyze their strategies for other branches.",
                'potential_impact' => 'medium',
            ];
        }

        return $opportunities;
    }

    /**
     * Identify business risks
     */
    private function identifyRisks(array $data): array
    {
        $risks = [];

        // Inventory risks
        if ($data['inventory']['stock_health'] < 50) {
            $risks[] = [
                'type' => 'inventory',
                'severity' => 'high',
                'description' => 'Poor inventory health may lead to stockouts and lost sales',
                'mitigation' => 'Implement automated reordering system',
            ];
        }

        // Sales decline risk
        if ($data['trends']['trend'] === 'declining') {
            $risks[] = [
                'type' => 'sales',
                'severity' => 'medium',
                'description' => 'Declining sales trend over past months',
                'mitigation' => 'Review pricing strategy and market conditions',
            ];
        }

        return $risks;
    }

    /**
     * Generate sales forecast
     */
    private function generateForecast(array $data): array
    {
        $monthlySales = $data['trends']['monthly_sales'];
        $values = $monthlySales->pluck('sales')->toArray();

        if (count($values) < 3) {
            return [
                'next_month' => 'Insufficient data',
                'confidence' => 'low',
            ];
        }

        // Simple linear regression forecast
        $avg = array_sum($values) / count($values);
        $trend = $data['trends']['trend'];
        
        $nextMonthEstimate = $avg;
        if ($trend === 'growing') {
            $nextMonthEstimate = $avg * 1.1;
        } elseif ($trend === 'declining') {
            $nextMonthEstimate = $avg * 0.9;
        }

        return [
            'next_month' => number_format($nextMonthEstimate, 2),
            'range' => [
                'low' => number_format($nextMonthEstimate * 0.9, 2),
                'high' => number_format($nextMonthEstimate * 1.1, 2),
            ],
            'confidence' => count($values) >= 6 ? 'high' : 'medium',
            'trend' => $trend,
        ];
    }

    /**
     * Calculate business health score (0-100)
     */
    private function calculateBusinessHealthScore(array $data): array
    {
        $score = 0;
        $breakdown = [];

        // Sales performance (30 points)
        $salesScore = 0;
        if ($data['sales']['month_growth'] > 10) $salesScore = 30;
        elseif ($data['sales']['month_growth'] > 0) $salesScore = 20;
        elseif ($data['sales']['month_growth'] > -10) $salesScore = 10;
        $score += $salesScore;
        $breakdown['sales_performance'] = $salesScore;

        // Inventory health (30 points)
        $inventoryScore = ($data['inventory']['stock_health'] / 100) * 30;
        $score += $inventoryScore;
        $breakdown['inventory_health'] = round($inventoryScore);

        // Trend (20 points)
        $trendScore = 0;
        if ($data['trends']['trend'] === 'growing') $trendScore = 20;
        elseif ($data['trends']['trend'] === 'stable') $trendScore = 15;
        else $trendScore = 5;
        $score += $trendScore;
        $breakdown['trend'] = $trendScore;

        // Expense control (20 points)
        $expenseScore = 20;
        if ($data['expenses']['growth'] > 20) $expenseScore = 5;
        elseif ($data['expenses']['growth'] > 10) $expenseScore = 10;
        elseif ($data['expenses']['growth'] > 0) $expenseScore = 15;
        $score += $expenseScore;
        $breakdown['expense_control'] = $expenseScore;

        return [
            'total' => round($score),
            'grade' => $this->getGrade($score),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Get letter grade from score
     */
    private function getGrade(float $score): string
    {
        if ($score >= 90) return 'A+';
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        return 'F';
    }

    /**
     * Get inventory recommendations
     */
    public function getInventoryRecommendations(): array
    {
        $products = Product::all();
        $recommendations = [];

        foreach ($products as $product) {
            // Calculate sales velocity (last 30 days)
            $salesLast30Days = PastOrderItem::where('product_id', $product->id)
                ->whereBetween('created_at', [
                    Carbon::now()->subDays(30),
                    Carbon::now()
                ])
                ->sum('quantity');

            $dailyVelocity = $salesLast30Days / 30;
            $daysUntilStockout = $dailyVelocity > 0 
                ? $product->quantity / $dailyVelocity 
                : 999;

            // Generate recommendation
            if ($daysUntilStockout < 7 && $product->quantity > 0) {
                $recommendedQuantity = ceil($dailyVelocity * 30); // 30 days supply

                $recommendations[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'current_stock' => $product->quantity,
                    'daily_sales' => round($dailyVelocity, 2),
                    'days_until_stockout' => round($daysUntilStockout, 1),
                    'recommended_quantity' => $recommendedQuantity,
                    'priority' => $daysUntilStockout < 3 ? 'critical' : 'high',
                    'action' => 'restock',
                    'reason' => "Will run out in " . round($daysUntilStockout, 1) . " days",
                ];
            }
        }

        return collect($recommendations)
            ->sortBy('days_until_stockout')
            ->values()
            ->toArray();
    }

    /**
     * Answer natural language question
     */
    public function answerQuestion(string $question): array
    {
        $data = $this->gatherBusinessData();
        $question = strtolower($question);

        // Pattern matching for common questions
        if (str_contains($question, 'sales') && (str_contains($question, 'drop') || str_contains($question, 'decline'))) {
            return $this->analyzeSalesDecline($data);
        }

        if (str_contains($question, 'best') || str_contains($question, 'top')) {
            return $this->getTopPerformers($data);
        }

        if (str_contains($question, 'stock') || str_contains($question, 'inventory')) {
            return $this->getInventoryStatus($data);
        }

        if (str_contains($question, 'expense') || str_contains($question, 'cost')) {
            return $this->getExpenseAnalysis($data);
        }

        // Default response
        return [
            'answer' => "I can help you with questions about sales, inventory, expenses, and business performance. Try asking: 'Why did sales drop?' or 'What are my top products?'",
            'suggestions' => [
                'Why did my sales drop?',
                'What are my best-selling products?',
                'Which items need restocking?',
                'How are my expenses trending?',
            ],
        ];
    }

    /**
     * Analyze sales decline
     */
    private function analyzeSalesDecline(array $data): array
    {
        $reasons = [];
        
        if ($data['inventory']['out_of_stock'] > 0) {
            $reasons[] = "{$data['inventory']['out_of_stock']} products are out of stock, potentially causing lost sales";
        }

        if ($data['sales']['week_growth'] < 0) {
            $reasons[] = "Weekly sales declined by " . number_format(abs($data['sales']['week_growth']), 1) . "%";
        }

        return [
            'answer' => "Sales analysis for " . Carbon::now()->format('M Y'),
            'current_sales' => number_format($data['sales']['this_month'], 2),
            'change' => number_format($data['sales']['month_growth'], 1) . '%',
            'possible_reasons' => $reasons,
            'recommendations' => [
                'Check inventory levels for popular items',
                'Review pricing and promotions',
                'Analyze competitor activity',
            ],
        ];
    }

    /**
     * Get top performers
     */
    private function getTopPerformers(array $data): array
    {
        $topProducts = $data['products']['top_sellers']->take(5)->map(function ($item) {
            return [
                'name' => $item->product->name,
                'units_sold' => $item->total_sold,
                'revenue' => number_format($item->total_revenue, 2),
            ];
        });

        return [
            'answer' => "Top 5 performing products this period",
            'products' => $topProducts,
            'best_branch' => $data['branches']['best_performer']['name'] ?? 'N/A',
        ];
    }

    /**
     * Get inventory status
     */
    private function getInventoryStatus(array $data): array
    {
        return [
            'answer' => "Current inventory status",
            'total_products' => $data['inventory']['total_products'],
            'in_stock' => $data['inventory']['in_stock'],
            'low_stock' => $data['inventory']['low_stock'],
            'out_of_stock' => $data['inventory']['out_of_stock'],
            'health_score' => number_format($data['inventory']['stock_health'], 1) . '%',
            'critical_items' => $data['inventory']['critical_products']->pluck('name'),
        ];
    }

    /**
     * Get expense analysis
     */
    private function getExpenseAnalysis(array $data): array
    {
        return [
            'answer' => "Expense analysis for " . Carbon::now()->format('M Y'),
            'this_month' => number_format($data['expenses']['this_month'], 2),
            'last_month' => number_format($data['expenses']['last_month'], 2),
            'change' => number_format($data['expenses']['growth'], 1) . '%',
            'top_categories' => $data['expenses']['by_category']->take(3)->map(function ($item) {
                return [
                    'category' => $item->category,
                    'amount' => number_format($item->total, 2),
                ];
            }),
        ];
    }
}
