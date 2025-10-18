<!-- AI Business Intelligence Dashboard Component -->
<!-- Add this to your owner dashboard blade file -->

<div class="row mb-4">
    <!-- AI Health Score Card -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Business Health Score
                        </div>
                        <div class="h2 mb-0 font-weight-bold text-gray-800" id="ai-health-score">
                            --
                        </div>
                        <div class="text-xs text-gray-600" id="ai-health-grade">
                            Loading...
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-brain fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Quick Insights -->
    <div class="col-lg-9 col-md-6 mb-3">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-lightbulb mr-2"></i>AI Insights
                </h6>
                <button class="btn btn-sm btn-primary" onclick="refreshAIInsights()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div id="ai-insights-container">
                    <div class="text-center text-muted">
                        <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                        <p>Analyzing your business data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Recommendations Row -->
<div class="row mb-4">
    <!-- Recommendations Card -->
    <div class="col-lg-8 mb-3">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-tasks mr-2"></i>AI Recommendations
                </h6>
            </div>
            <div class="card-body">
                <div id="ai-recommendations-container">
                    <!-- Recommendations will load here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Card -->
    <div class="col-lg-4 mb-3">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Alerts
                </h6>
            </div>
            <div class="card-body">
                <div id="ai-alerts-container">
                    <!-- Alerts will load here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ask AI Modal -->
<div class="modal fade" id="askAIModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-robot mr-2"></i>Ask AI Assistant
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="aiQuestion">Ask a question about your business:</label>
                    <input type="text" class="form-control" id="aiQuestion" 
                           placeholder="e.g., Why did sales drop last week?">
                </div>
                <div class="mb-3">
                    <small class="text-muted">Suggestions:</small><br>
                    <button class="btn btn-sm btn-outline-secondary m-1" onclick="askSuggestion('Why did sales drop?')">
                        Why did sales drop?
                    </button>
                    <button class="btn btn-sm btn-outline-secondary m-1" onclick="askSuggestion('What products should I stock?')">
                        What products should I stock?
                    </button>
                    <button class="btn btn-sm btn-outline-secondary m-1" onclick="askSuggestion('How are expenses trending?')">
                        How are expenses trending?
                    </button>
                </div>
                <div id="aiAnswerContainer" style="display: none;">
                    <div class="alert alert-info">
                        <h6><strong>Answer:</strong></h6>
                        <div id="aiAnswer"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitAIQuestion()">
                    <i class="fas fa-paper-plane mr-2"></i>Ask AI
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Floating AI Assistant Button -->
<button class="btn btn-primary btn-circle btn-lg" 
        style="position: fixed; bottom: 30px; right: 30px; z-index: 1000;"
        onclick="$('#askAIModal').modal('show')"
        title="Ask AI Assistant">
    <i class="fas fa-robot"></i>
</button>

<style>
    .ai-recommendation {
        border-left: 4px solid #28a745;
        padding: 10px;
        margin-bottom: 10px;
        background: #f8f9fa;
        border-radius: 4px;
    }

    .ai-recommendation.priority-critical {
        border-left-color: #dc3545;
    }

    .ai-recommendation.priority-high {
        border-left-color: #ffc107;
    }

    .ai-alert {
        padding: 8px 12px;
        margin-bottom: 8px;
        border-radius: 4px;
        font-size: 0.875rem;
    }

    .ai-alert.severity-critical {
        background: #f8d7da;
        border-left: 3px solid #dc3545;
    }

    .ai-alert.severity-warning {
        background: #fff3cd;
        border-left: 3px solid #ffc107;
    }

    .btn-circle {
        width: 60px;
        height: 60px;
        padding: 0;
        border-radius: 50%;
        font-size: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
</style>

<script>
    // AI Business Intelligence JavaScript

    // Load AI insights on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadAIInsights();
        loadHealthScore();
    });

    /**
     * Load comprehensive AI insights
     */
    async function loadAIInsights() {
        try {
            const response = await fetch('/api/ai/insights', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                displayInsights(result.data);
            } else {
                showError('Failed to load AI insights');
            }
        } catch (error) {
            console.error('AI Insights Error:', error);
            showError('Error loading AI insights');
        }
    }

    /**
     * Display AI insights
     */
    function displayInsights(data) {
        // Display summary
        const summaryHtml = `
            <div class="mb-3">
                <h6 class="text-primary"><i class="fas fa-chart-line mr-2"></i>Summary</h6>
                <p class="mb-0">${data.summary.replace(/\n/g, '<br>')}</p>
            </div>
        `;
        $('#ai-insights-container').html(summaryHtml);

        // Display recommendations
        if (data.recommendations && data.recommendations.length > 0) {
            let recsHtml = '';
            data.recommendations.forEach(rec => {
                recsHtml += `
                    <div class="ai-recommendation priority-${rec.priority}">
                        <h6 class="mb-1"><strong>${rec.title}</strong></h6>
                        <p class="mb-1 text-sm">${rec.description}</p>
                        <small class="text-muted">
                            <i class="fas fa-tag mr-1"></i>${rec.type} | 
                            <i class="fas fa-exclamation-circle mr-1"></i>${rec.priority}
                        </small>
                    </div>
                `;
            });
            $('#ai-recommendations-container').html(recsHtml);
        } else {
            $('#ai-recommendations-container').html('<p class="text-muted text-center">No recommendations at this time</p>');
        }

        // Display alerts
        if (data.alerts && data.alerts.length > 0) {
            let alertsHtml = '';
            data.alerts.forEach(alert => {
                alertsHtml += `
                    <div class="ai-alert severity-${alert.severity}">
                        <i class="fas fa-${alert.severity === 'critical' ? 'exclamation-triangle' : 'info-circle'} mr-2"></i>
                        ${alert.message}
                    </div>
                `;
            });
            $('#ai-alerts-container').html(alertsHtml);
        } else {
            $('#ai-alerts-container').html('<p class="text-muted text-center">No alerts</p>');
        }
    }

    /**
     * Load health score
     */
    async function loadHealthScore() {
        try {
            const response = await fetch('/api/ai/health-score', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                $('#ai-health-score').text(result.data.total + '/100');
                $('#ai-health-grade').text('Grade: ' + result.data.grade);
                
                // Color code based on score
                const scoreElement = $('#ai-health-score');
                if (result.data.total >= 80) {
                    scoreElement.removeClass('text-gray-800').addClass('text-success');
                } else if (result.data.total >= 60) {
                    scoreElement.removeClass('text-gray-800').addClass('text-warning');
                } else {
                    scoreElement.removeClass('text-gray-800').addClass('text-danger');
                }
            }
        } catch (error) {
            console.error('Health Score Error:', error);
            $('#ai-health-score').text('--');
            $('#ai-health-grade').text('Error loading');
        }
    }

    /**
     * Submit AI question
     */
    async function submitAIQuestion() {
        const question = $('#aiQuestion').val().trim();
        
        if (!question) {
            alert('Please enter a question');
            return;
        }

        // Show loading
        $('#aiAnswerContainer').show();
        $('#aiAnswer').html('<i class="fas fa-spinner fa-spin"></i> Thinking...');

        try {
            const response = await fetch('/api/ai/ask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ question: question })
            });

            const result = await response.json();

            if (result.success) {
                displayAnswer(result.answer);
            } else {
                $('#aiAnswer').html('<span class="text-danger">Failed to get answer</span>');
            }
        } catch (error) {
            console.error('AI Question Error:', error);
            $('#aiAnswer').html('<span class="text-danger">Error: ' + error.message + '</span>');
        }
    }

    /**
     * Display AI answer
     */
    function displayAnswer(answer) {
        let html = '';
        
        if (typeof answer === 'string') {
            html = `<p>${answer}</p>`;
        } else if (answer.answer) {
            html = `<p><strong>${answer.answer}</strong></p>`;
            
            // Sales analysis data
            if (answer.current_sales) {
                html += `<p>💰 Current Sales: ₱${answer.current_sales}</p>`;
            }
            if (answer.change) {
                html += `<p>📈 Change: ${answer.change}</p>`;
            }
            
            // Inventory data
            if (answer.total_products !== undefined) {
                html += `<div class="row mt-3">
                    <div class="col-3"><strong>📦 Total:</strong> ${answer.total_products}</div>
                    <div class="col-3"><strong>✅ In Stock:</strong> ${answer.in_stock || 0}</div>
                    <div class="col-3"><strong>⚠️ Low Stock:</strong> ${answer.low_stock || 0}</div>
                    <div class="col-3"><strong>❌ Out of Stock:</strong> ${answer.out_of_stock || 0}</div>
                </div>`;
                
                if (answer.health_score) {
                    html += `<p>📊 Inventory Health: ${answer.health_score}</p>`;
                }
            }
            
            // Critical items
            if (answer.critical_items && answer.critical_items.length > 0) {
                html += '<p><strong>🚨 Critical Items:</strong></p><ul>';
                answer.critical_items.forEach(item => {
                    html += `<li>${item}</li>`;
                });
                html += '</ul>';
            }
            
            // Expense data
            if (answer.this_month) {
                html += `<p>💸 This Month: ₱${answer.this_month}</p>`;
                if (answer.last_month) {
                    html += `<p>📅 Last Month: ₱${answer.last_month}</p>`;
                }
            }
            
            // Top categories for expenses
            if (answer.top_categories && answer.top_categories.length > 0) {
                html += '<p><strong>📋 Top Expense Categories:</strong></p><ul>';
                answer.top_categories.forEach(cat => {
                    html += `<li><strong>${cat.category}:</strong> ₱${cat.amount}</li>`;
                });
                html += '</ul>';
            }
            
            // Top products
            if (answer.products && answer.products.length > 0) {
                html += '<p><strong>🏆 Top Products:</strong></p><ul>';
                answer.products.forEach(product => {
                    html += `<li><strong>${product.name}:</strong> ${product.units_sold} units sold (₱${product.revenue})</li>`;
                });
                html += '</ul>';
                
                if (answer.best_branch) {
                    html += `<p>🏪 Best Branch: ${answer.best_branch}</p>`;
                }
            }
            
            // Possible reasons
            if (answer.possible_reasons && answer.possible_reasons.length > 0) {
                html += '<p><strong>🤔 Possible Reasons:</strong></p><ul>';
                answer.possible_reasons.forEach(reason => {
                    html += `<li>${reason}</li>`;
                });
                html += '</ul>';
            }
            
            // Recommendations
            if (answer.recommendations && answer.recommendations.length > 0) {
                html += '<p><strong>💡 Recommendations:</strong></p><ul>';
                answer.recommendations.forEach(rec => {
                    html += `<li>${rec}</li>`;
                });
                html += '</ul>';
            }
            
            // Suggestions
            if (answer.suggestions && answer.suggestions.length > 0) {
                html += '<p><strong>💭 Try asking:</strong></p><ul>';
                answer.suggestions.forEach(suggestion => {
                    html += `<li><a href="#" onclick="askSuggestion('${suggestion}')">${suggestion}</a></li>`;
                });
                html += '</ul>';
            }
        } else {
            html = `<pre>${JSON.stringify(answer, null, 2)}</pre>`;
        }
        
        $('#aiAnswer').html(html);
    }

    /**
     * Ask suggested question
     */
    function askSuggestion(question) {
        $('#aiQuestion').val(question);
        submitAIQuestion();
    }

    /**
     * Refresh AI insights
     */
    async function refreshAIInsights() {
        $('#ai-insights-container').html(`
            <div class="text-center text-muted">
                <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                <p>Clearing cache and refreshing insights...</p>
            </div>
        `);
        
        try {
            // Clear server-side cache first
            await fetch('/api/ai/clear-cache', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        } catch (error) {
            console.log('Cache clearing failed, continuing with refresh...');
        }
        
        // Load fresh insights
        loadAIInsights();
        loadHealthScore();
    }

    /**
     * Show error message
     */
    function showError(message) {
        $('#ai-insights-container').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle mr-2"></i>${message}
            </div>
        `);
    }
</script>
