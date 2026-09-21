<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Student;
use App\Models\StudentStatusLog;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year');
        $month = $request->get('month');

        // Determine filter period
        if (!$year && !$month) {
            // Default: this month
            $filterYear = now()->year;
            $filterMonth = now()->month;
            $filterType = 'month';
        } elseif ($year && !$month) {
            // Year only: full year
            $filterYear = $year;
            $filterMonth = null;
            $filterType = 'year';
        } elseif (!$year && $month) {
            // Month only: this year + selected month
            $filterYear = now()->year;
            $filterMonth = $month;
            $filterType = 'month';
        } else {
            // Both year and month: selected year + selected month
            $filterYear = $year;
            $filterMonth = $month;
            $filterType = 'month';
        }

        $activeStudentsCount = Student::where('status', true)->count();
        $coursesCount = Subject::count();
        $activeTeachersCount = Teacher::where('status', true)->count();

        // Calculate expense based on filter
        if ($filterType === 'year') {
            $thisMonthExpense = Expense::whereYear('expense_date', $filterYear)->sum('amount');
            $thisMonthIncome = Voucher::whereYear('voucher_date', $filterYear)->sum('total_amount');
            $kpayIncome = Voucher::whereYear('voucher_date', $filterYear)->where('payment_method', 'kpay')->sum('total_amount');
            $cashIncome = Voucher::whereYear('voucher_date', $filterYear)->where('payment_method', 'cash')->sum('total_amount');
        } else {
            $thisMonthExpense = Expense::whereMonth('expense_date', $filterMonth)
                                       ->whereYear('expense_date', $filterYear)
                                       ->sum('amount');
            $thisMonthIncome = Voucher::whereMonth('voucher_date', $filterMonth)
                                       ->whereYear('voucher_date', $filterYear)
                                       ->sum('total_amount');
            $kpayIncome = Voucher::whereMonth('voucher_date', $filterMonth)
                                  ->whereYear('voucher_date', $filterYear)
                                  ->where('payment_method', 'kpay')
                                  ->sum('total_amount');
            $cashIncome = Voucher::whereMonth('voucher_date', $filterMonth)
                                  ->whereYear('voucher_date', $filterYear)
                                  ->where('payment_method', 'cash')
                                  ->sum('total_amount');
        }

        // Get expense data for charts
        $expenseData = $this->getExpenseData($filterYear, $filterMonth, $filterType);

        // Get student data from status logs
        $studentData = $this->getStudentData($filterYear, $filterMonth, $filterType);

        // Get income data for charts
        $incomeData = $this->getIncomeData($filterYear, $filterMonth, $filterType);

        // Get expense data by category for donut chart
        $expenseByCategory = $this->getExpenseByCategory($filterYear, $filterMonth, $filterType);

        return view('admin.dashboard', [
            'title' => 'Dashboard',
            'activeStudentsCount' => $activeStudentsCount,
            'coursesCount' => $coursesCount,
            'activeTeachersCount' => $activeTeachersCount,
            'thisMonthExpense' => $thisMonthExpense,
            'thisMonthIncome' => $thisMonthIncome,
            'kpayIncome' => $kpayIncome,
            'cashIncome' => $cashIncome,
            'expenseData' => $expenseData,
            'studentData' => $studentData,
            'incomeData' => $incomeData,
            'expenseByCategory' => $expenseByCategory,
            'selectedYear' => $year,
            'selectedMonth' => $month
        ]);
    }
    
    private function getExpenseData($filterYear = null, $filterMonth = null, $filterType = 'month')
    {
        // Get last 6 months of data
        $monthlyData = [];
        $monthlyLabels = [];

        if ($filterType === 'year' && $filterYear) {
            // When filtering by year, show all 12 months of that year
            for ($i = 1; $i <= 12; $i++) {
                $date = Carbon::create($filterYear, $i, 1);
                $monthName = $date->format('M');
                $monthlyLabels[] = $monthName;

                $amount = Expense::whereMonth('expense_date', $i)
                                ->whereYear('expense_date', $filterYear)
                                ->sum('amount');
                $monthlyData[] = $amount;
            }
        } else {
            // Default: last 6 months
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthName = $date->format('M');
                $monthlyLabels[] = $monthName;

                $amount = Expense::whereMonth('expense_date', $date->month)
                                ->whereYear('expense_date', $date->year)
                                ->sum('amount');
                $monthlyData[] = $amount;
            }
        }

        // Calculate monthly percentage change
        $lastMonthAmount = $monthlyData[count($monthlyData) - 1];
        $previousMonthAmount = $monthlyData[count($monthlyData) - 2] ?? 0;
        $monthlyPercentageChange = $previousMonthAmount > 0
            ? (($lastMonthAmount - $previousMonthAmount) / $previousMonthAmount) * 100
            : 0;

        // Get last 4 years of data
        $yearlyData = [];
        $yearlyLabels = [];

        for ($i = 3; $i >= 0; $i--) {
            $year = now()->subYears($i)->year;
            $yearlyLabels[] = (string)$year;

            $amount = Expense::whereYear('expense_date', $year)->sum('amount');
            $yearlyData[] = $amount;
        }

        // Calculate yearly percentage change
        $lastYearAmount = $yearlyData[count($yearlyData) - 1];
        $previousYearAmount = $yearlyData[count($yearlyData) - 2] ?? 0;
        $yearlyPercentageChange = $previousYearAmount > 0
            ? (($lastYearAmount - $previousYearAmount) / $previousYearAmount) * 100
            : 0;

        return [
            'monthly' => [
                'data' => $monthlyData,
                'labels' => $monthlyLabels,
                'change' => number_format($monthlyPercentageChange, 1)
            ],
            'yearly' => [
                'data' => $yearlyData,
                'labels' => $yearlyLabels,
                'change' => number_format($yearlyPercentageChange, 1)
            ]
        ];
    }
    
    private function getStudentData($filterYear = null, $filterMonth = null, $filterType = 'month')
    {
        // Get student count changes for last 6 months
        $monthlyData = [];
        $monthlyLabels = [];

        if ($filterType === 'year' && $filterYear) {
            // When filtering by year, show all 12 months of that year
            for ($i = 1; $i <= 12; $i++) {
                $date = Carbon::create($filterYear, $i, 1);
                $monthName = $date->format('M');
                $monthlyLabels[] = $monthName;

                $monthEnd = $date->endOfMonth();
                $totalActiveStudents = Student::where('status', true)
                                             ->where('created_at', '<=', $monthEnd)
                                             ->count();
                $monthlyData[] = $totalActiveStudents;
            }
        } else {
            // Default: last 6 months
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthName = $date->format('M');
                $monthlyLabels[] = $monthName;

                // Count students who were active during that month
                $monthStart = $date->startOfMonth();
                $monthEnd = $date->endOfMonth();

                // Count total active students as of the end of this month
                $totalActiveStudents = Student::where('status', true)
                                             ->where('created_at', '<=', $monthEnd)
                                             ->count();

                $monthlyData[] = $totalActiveStudents;
            }
        }

        // Calculate monthly percentage change
        $lastMonthCount = $monthlyData[count($monthlyData) - 1];
        $previousMonthCount = $monthlyData[count($monthlyData) - 2] ?? 0;
        $monthlyPercentageChange = $previousMonthCount > 0
            ? (($lastMonthCount - $previousMonthCount) / $previousMonthCount) * 100
            : 0;

        // Get last 4 years of data
        $yearlyData = [];
        $yearlyLabels = [];

        for ($i = 3; $i >= 0; $i--) {
            $year = now()->subYears($i)->year;
            $yearlyLabels[] = (string)$year;

            // Count total active students as of the end of this year
            $yearEnd = now()->subYears($i)->endOfYear();
            $totalActiveStudents = Student::where('status', true)
                                         ->where('created_at', '<=', $yearEnd)
                                         ->count();

            $yearlyData[] = $totalActiveStudents;
        }

        // Calculate yearly percentage change
        $lastYearCount = $yearlyData[count($yearlyData) - 1];
        $previousYearCount = $yearlyData[count($yearlyData) - 2] ?? 0;
        $yearlyPercentageChange = $previousYearCount > 0
            ? (($lastYearCount - $previousYearCount) / $previousYearCount) * 100
            : 0;

        return [
            'monthly' => [
                'data' => $monthlyData,
                'labels' => $monthlyLabels,
                'change' => number_format($monthlyPercentageChange, 1)
            ],
            'yearly' => [
                'data' => $yearlyData,
                'labels' => $yearlyLabels,
                'change' => number_format($yearlyPercentageChange, 1)
            ]
        ];
    }
    
    private function getIncomeData($filterYear = null, $filterMonth = null, $filterType = 'month')
    {
        // Get last 6 months of data
        $monthlyData = [];
        $monthlyLabels = [];

        if ($filterType === 'year' && $filterYear) {
            // When filtering by year, show all 12 months of that year
            for ($i = 1; $i <= 12; $i++) {
                $date = Carbon::create($filterYear, $i, 1);
                $monthName = $date->format('M');
                $monthlyLabels[] = $monthName;

                $amount = Voucher::whereMonth('voucher_date', $i)
                                ->whereYear('voucher_date', $filterYear)
                                ->sum('total_amount');
                $monthlyData[] = $amount;
            }
        } else {
            // Default: last 6 months
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthName = $date->format('M');
                $monthlyLabels[] = $monthName;

                $amount = Voucher::whereMonth('voucher_date', $date->month)
                                ->whereYear('voucher_date', $date->year)
                                ->sum('total_amount');
                $monthlyData[] = $amount;
            }
        }

        // Calculate monthly percentage change
        $lastMonthAmount = $monthlyData[count($monthlyData) - 1];
        $previousMonthAmount = $monthlyData[count($monthlyData) - 2] ?? 0;
        $monthlyPercentageChange = $previousMonthAmount > 0
            ? (($lastMonthAmount - $previousMonthAmount) / $previousMonthAmount) * 100
            : 0;

        // Get last 4 years of data
        $yearlyData = [];
        $yearlyLabels = [];

        for ($i = 3; $i >= 0; $i--) {
            $year = now()->subYears($i)->year;
            $yearlyLabels[] = (string)$year;

            $amount = Voucher::whereYear('voucher_date', $year)->sum('total_amount');
            $yearlyData[] = $amount;
        }

        // Calculate yearly percentage change
        $lastYearAmount = $yearlyData[count($yearlyData) - 1];
        $previousYearAmount = $yearlyData[count($yearlyData) - 2] ?? 0;
        $yearlyPercentageChange = $previousYearAmount > 0
            ? (($lastYearAmount - $previousYearAmount) / $previousYearAmount) * 100
            : 0;

        return [
            'monthly' => [
                'data' => $monthlyData,
                'labels' => $monthlyLabels,
                'change' => number_format($monthlyPercentageChange, 1)
            ],
            'yearly' => [
                'data' => $yearlyData,
                'labels' => $yearlyLabels,
                'change' => number_format($yearlyPercentageChange, 1)
            ]
        ];
    }

    private function getExpenseByCategory($filterYear = null, $filterMonth = null, $filterType = 'month')
    {
        $query = Expense::with('category');

        if ($filterType === 'year' && $filterYear) {
            $query->whereYear('expense_date', $filterYear);
        } else {
            $query->whereMonth('expense_date', $filterMonth)
                  ->whereYear('expense_date', $filterYear);
        }

        $expenses = $query->get();

        // Group by category
        $categoryExpenses = $expenses->groupBy('category_id')->map(function ($items) {
            return [
                'category_name' => $items->first()->category->name ?? 'Uncategorized',
                'total_amount' => $items->sum('amount')
            ];
        })->values();

        return $categoryExpenses;
    }
}
