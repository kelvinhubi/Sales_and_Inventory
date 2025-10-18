<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIBusinessIntelligenceService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AI Insights Controller
 *
 * Provides AI-powered business intelligence endpoints
 */
class AIInsightsController extends Controller
{
    protected $aiService;

    public function __construct(AIBusinessIntelligenceService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Get comprehensive business insights
     *
     * GET /api/ai/insights
     */
    public function getInsights(): JsonResponse
    {
        try {
            $insights = $this->aiService->getBusinessInsights();

            return response()->json([
                'success' => true,
                'data' => $insights,
                'generated_at' => now()->toISOString(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate insights: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get inventory recommendations
     *
     * GET /api/ai/recommendations/inventory
     */
    public function getInventoryRecommendations(): JsonResponse
    {
        try {
            $recommendations = $this->aiService->getInventoryRecommendations();

            return response()->json([
                'success' => true,
                'data' => $recommendations,
                'count' => count($recommendations),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate recommendations: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Answer a natural language question
     *
     * POST /api/ai/ask
     * Body: { "question": "Why did sales drop?" }
     */
    public function askQuestion(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'question' => 'required|string|max:500',
            ]);

            $answer = $this->aiService->answerQuestion($validated['question']);

            return response()->json([
                'success' => true,
                'question' => $validated['question'],
                'answer' => $answer,
                'answered_at' => now()->toISOString(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to answer question: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get business health score
     *
     * GET /api/ai/health-score
     */
    public function getHealthScore(): JsonResponse
    {
        try {
            $insights = $this->aiService->getBusinessInsights();

            return response()->json([
                'success' => true,
                'data' => $insights['score'],
                'summary' => $insights['summary'],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate health score: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get quick daily brief
     *
     * GET /api/ai/daily-brief
     */
    public function getDailyBrief(): JsonResponse
    {
        try {
            $insights = $this->aiService->getBusinessInsights();

            $brief = [
                'date' => now()->format('l, F j, Y'),
                'summary' => $insights['summary'],
                'health_score' => $insights['score']['total'],
                'top_priority_alerts' => collect($insights['alerts'])->take(3),
                'top_recommendations' => collect($insights['recommendations'])->take(3),
                'quick_stats' => [
                    'health_grade' => $insights['score']['grade'],
                    'trend' => $insights['forecast']['trend'],
                    'alerts_count' => count($insights['alerts']),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $brief,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate daily brief: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear AI insights cache
     *
     * POST /api/ai/clear-cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            // Clear all AI insights cache keys for different hours
            $today = now();
            $clearedKeys = 0;

            for ($i = 0; $i < 24; $i++) {
                $cacheKey = 'ai_business_insights_' . $today->copy()->addHours($i)->format('Y-m-d-H');
                if (cache()->forget($cacheKey)) {
                    $clearedKeys++;
                }
            }

            // Also clear current hour cache
            $currentCacheKey = 'ai_business_insights_' . $today->format('Y-m-d-H');
            cache()->forget($currentCacheKey);

            return response()->json([
                'success' => true,
                'message' => 'AI insights cache cleared successfully',
                'keys_cleared' => $clearedKeys,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage(),
            ], 500);
        }
    }
}
