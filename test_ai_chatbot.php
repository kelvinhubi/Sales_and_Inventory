<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AIBusinessIntelligenceService;

echo "=== TESTING AI CHATBOT FUNCTIONALITY ===\n";

$service = new AIBusinessIntelligenceService();

// Test questions about products
$productQuestions = [
    "What are my best products?",
    "Which products are out of stock?",
    "Show me inventory status",
    "What items need restocking?"
];

echo "\n=== TESTING PRODUCT QUESTIONS ===\n";
foreach ($productQuestions as $question) {
    echo "\nQ: $question\n";
    try {
        $answer = $service->answerQuestion($question);
        echo "✅ Response received:\n";
        if (is_array($answer)) {
            if (isset($answer['answer'])) {
                echo "   Answer: " . $answer['answer'] . "\n";
            }
            if (isset($answer['total_products'])) {
                echo "   Total products: " . $answer['total_products'] . "\n";
            }
            if (isset($answer['out_of_stock'])) {
                echo "   Out of stock: " . $answer['out_of_stock'] . "\n";
            }
            if (isset($answer['critical_items']) && count($answer['critical_items']) > 0) {
                echo "   Critical items: " . implode(', ', $answer['critical_items']) . "\n";
            }
        } else {
            echo "   " . $answer . "\n";
        }
    } catch (Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
}

// Test questions about expenses
$expenseQuestions = [
    "How are my expenses?",
    "What are my costs this month?",
    "Show me expense analysis",
    "What are my biggest expenses?"
];

echo "\n=== TESTING EXPENSE QUESTIONS ===\n";
foreach ($expenseQuestions as $question) {
    echo "\nQ: $question\n";
    try {
        $answer = $service->answerQuestion($question);
        echo "✅ Response received:\n";
        if (is_array($answer)) {
            if (isset($answer['answer'])) {
                echo "   Answer: " . $answer['answer'] . "\n";
            }
            if (isset($answer['this_month'])) {
                echo "   This month: ₱" . $answer['this_month'] . "\n";
            }
            if (isset($answer['change'])) {
                echo "   Change: " . $answer['change'] . "\n";
            }
            if (isset($answer['top_categories']) && count($answer['top_categories']) > 0) {
                echo "   Top categories:\n";
                foreach ($answer['top_categories'] as $cat) {
                    echo "     - " . $cat['category'] . ": ₱" . $cat['amount'] . "\n";
                }
            }
        } else {
            echo "   " . $answer . "\n";
        }
    } catch (Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n=== TESTING DEFAULT RESPONSE ===\n";
echo "\nQ: What can you help me with?\n";
try {
    $answer = $service->answerQuestion("What can you help me with?");
    echo "✅ Response received:\n";
    if (is_array($answer)) {
        if (isset($answer['answer'])) {
            echo "   Answer: " . $answer['answer'] . "\n";
        }
        if (isset($answer['suggestions']) && count($answer['suggestions']) > 0) {
            echo "   Suggestions:\n";
            foreach ($answer['suggestions'] as $suggestion) {
                echo "     - " . $suggestion . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}