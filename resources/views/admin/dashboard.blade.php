@extends('admin.layout')

@section('content')
    <script>
        window.dashboardData = {
            expenseData: {
                monthly: @json($expenseData['monthly']),
                yearly: @json($expenseData['yearly'])
            },
            studentData: {
                monthly: @json($studentData['monthly']),
                yearly: @json($studentData['yearly'])
            },
            incomeData: {
                monthly: @json($incomeData['monthly']),
                yearly: @json($incomeData['yearly'])
            }
        };
    </script>
    <div x-data="{
        incomePeriod: 'month',
        expensePeriod: 'month', 
        studentPeriod: 'month',
        incomeChange: window.dashboardData.incomeData.monthly.change,
        expenseChange: window.dashboardData.expenseData.monthly.change,
        studentChange: window.dashboardData.studentData.monthly.change,
        
        updateIncomeChart() {
            const data = this.incomePeriod === 'month' ? 
                window.dashboardData.incomeData.monthly.data : 
                window.dashboardData.incomeData.yearly.data;
            const labels = this.incomePeriod === 'month' ? 
                window.dashboardData.incomeData.monthly.labels : 
                window.dashboardData.incomeData.yearly.labels;
            
            incomeChart.data.labels = labels;
            incomeChart.data.datasets[0].data = data;
            incomeChart.update();
            
            const last = data[data.length - 1];
            const previous = data[data.length - 2];
            this.incomeChange = previous > 0 ? ((last - previous) / previous * 100).toFixed(1) : 0;
        },
        
        updateExpenseChart() {
            const data = this.expensePeriod === 'month' ? 
                window.dashboardData.expenseData.monthly.data : 
                window.dashboardData.expenseData.yearly.data;
            const labels = this.expensePeriod === 'month' ? 
                window.dashboardData.expenseData.monthly.labels : 
                window.dashboardData.expenseData.yearly.labels;
            
            expenseChart.data.labels = labels;
            expenseChart.data.datasets[0].data = data;
            expenseChart.update();
            
            const last = data[data.length - 1];
            const previous = data[data.length - 2];
            this.expenseChange = ((last - previous) / previous * 100).toFixed(1);
        },
        
        updateStudentChart() {
            const data = this.studentPeriod === 'month' ? 
                window.dashboardData.studentData.monthly.data : 
                window.dashboardData.studentData.yearly.data;
            const labels = this.studentPeriod === 'month' ? 
                window.dashboardData.studentData.monthly.labels : 
                window.dashboardData.studentData.yearly.labels;
            
            studentChart.data.labels = labels;
            studentChart.data.datasets[0].data = data;
            studentChart.update();
            
            const last = data[data.length - 1];
            const previous = data[data.length - 2];
            this.studentChange = ((last - previous) / previous * 100).toFixed(1);
        },
        
        updateInitialChanges() {
            // Set initial change values based on default period (month)
            this.expenseChange = window.dashboardData.expenseData.monthly.change;
            this.studentChange = window.dashboardData.studentData.monthly.change;
            this.incomeChange = window.dashboardData.incomeData.monthly.change;
        },
        
        init() {
            this.updateInitialChanges();
        }
    }" x-init="init">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Students</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $activeStudentsCount }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Courses</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $coursesCount }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Income (This Month)</p>
                    <p class="text-2xl font-bold text-gray-800">${{ number_format($thisMonthIncome, $thisMonthIncome == floor($thisMonthIncome) ? 0 : 2) }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Expense (This Month)</p>
                    <p class="text-2xl font-bold text-gray-800">${{ number_format($thisMonthExpense, $thisMonthExpense == floor($thisMonthExpense) ? 0 : 2) }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphs Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Income Graph -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Income</h3>
                    <div class="flex gap-2">
                        <select x-model="incomePeriod" @change="updateIncomeChart()" class="text-sm border border-gray-300 rounded px-2 py-1">
                            <option value="month">Month</option>
                            <option value="year">Year</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span x-text="incomeChange > 0 ? '+' + incomeChange + '%' : incomeChange + '%'" 
                          :class="incomeChange > 0 ? 'text-green-600' : 'text-red-600'" 
                          class="text-sm font-semibold"></span>
                    <span x-text="incomeChange > 0 ? '↑ increase' : '↓ decrease'" 
                          :class="incomeChange > 0 ? 'text-green-600' : 'text-red-600'" 
                          class="text-xs"></span>
                </div>
            </div>
            <div class="p-6">
                <canvas id="incomeChart" height="200"></canvas>
            </div>
        </div>

        <!-- Expense Graph -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Expense</h3>
                    <div class="flex gap-2">
                        <select x-model="expensePeriod" @change="updateExpenseChart()" class="text-sm border border-gray-300 rounded px-2 py-1">
                            <option value="month">Month</option>
                            <option value="year">Year</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span x-text="expenseChange > 0 ? '+' + expenseChange + '%' : expenseChange + '%'" 
                          :class="expenseChange > 0 ? 'text-red-600' : 'text-green-600'" 
                          class="text-sm font-semibold"></span>
                    <span x-text="expenseChange > 0 ? '↑ increase' : '↓ decrease'" 
                          :class="expenseChange > 0 ? 'text-red-600' : 'text-green-600'" 
                          class="text-xs"></span>
                </div>
            </div>
            <div class="p-6">
                <canvas id="expenseChart" height="200"></canvas>
            </div>
        </div>

        <!-- Students Graph -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Students</h3>
                    <div class="flex gap-2">
                        <select x-model="studentPeriod" @change="updateStudentChart()" class="text-sm border border-gray-300 rounded px-2 py-1">
                            <option value="month">Month</option>
                            <option value="year">Year</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span x-text="studentChange > 0 ? '+' + studentChange + '%' : studentChange + '%'" 
                          :class="studentChange > 0 ? 'text-green-600' : 'text-red-600'" 
                          class="text-sm font-semibold"></span>
                    <span x-text="studentChange > 0 ? '↑ increase' : '↓ decrease'" 
                          :class="studentChange > 0 ? 'text-green-600' : 'text-red-600'" 
                          class="text-xs"></span>
                </div>
            </div>
            <div class="p-6">
                <canvas id="studentChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly data
            const monthlyIncomeData = window.dashboardData.incomeData.monthly.data;
            const monthlyExpenseData = window.dashboardData.expenseData.monthly.data;
            const monthlyStudentData = window.dashboardData.studentData.monthly.data;
            const monthlyLabels = window.dashboardData.expenseData.monthly.labels;

            // Income Chart
            const incomeCtx = document.getElementById('incomeChart').getContext('2d');
            window.incomeChart = new Chart(incomeCtx, {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Income',
                        data: monthlyIncomeData,
                        borderColor: '#EAB308',
                        backgroundColor: 'rgba(234, 179, 8, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Expense Chart
            const expenseCtx = document.getElementById('expenseChart').getContext('2d');
            window.expenseChart = new Chart(expenseCtx, {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Expense',
                        data: monthlyExpenseData,
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Student Chart
            const studentCtx = document.getElementById('studentChart').getContext('2d');
            window.studentChart = new Chart(studentCtx, {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Students',
                        data: monthlyStudentData,
                        backgroundColor: '#3B82F6',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
@endsection