<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Student;
use App\Models\StudentStatusLog;
use App\Models\Subject;
use App\Models\Voucher;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $activeStudentsCount = Student::where('status', true)->count();
        $coursesCount = Subject::count();
        $thisMonthExpense = Expense::whereMonth('expense_date', now()->month)
                                     ->whereYear('expense_date', now()->year)
                                     ->sum('amount');
        
        // Calculate income from vouchers (sale + student_fee)
        $thisMonthIncome = Voucher::whereMonth('voucher_date', now()->month)
                                    ->whereYear('voucher_date', now()->year)
                                    ->sum('total_amount');
        
        // Get expense data for charts
        $expenseData = $this->getExpenseData();
        
        // Get student data from status logs
        $studentData = $this->getStudentData();
        
        // Get income data for charts
        $incomeData = $this->getIncomeData();
        
        return view('admin.dashboard', [
            'title' => 'Dashboard',
            'activeStudentsCount' => $activeStudentsCount,
            'coursesCount' => $coursesCount,
            'thisMonthExpense' => $thisMonthExpense,
            'thisMonthIncome' => $thisMonthIncome,
            'expenseData' => $expenseData,
            'studentData' => $studentData,
            'incomeData' => $incomeData
        ]);
    }
    
    private function getExpenseData()
    {
        // Get last 6 months of data
        $monthlyData = [];
        $monthlyLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M');
            $monthlyLabels[] = $monthName;
            
            $amount = Expense::whereMonth('expense_date', $date->month)
                            ->whereYear('expense_date', $date->year)
                            ->sum('amount');
            $monthlyData[] = $amount;
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
    
    private function getStudentData()
    {
        // Get student count changes for last 6 months
        $monthlyData = [];
        $monthlyLabels = [];
        
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
    
    private function getIncomeData()
    {
        // Get last 6 months of data
        $monthlyData = [];
        $monthlyLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M');
            $monthlyLabels[] = $monthName;
            
            $amount = Voucher::whereMonth('voucher_date', $date->month)
                            ->whereYear('voucher_date', $date->year)
                            ->sum('total_amount');
            $monthlyData[] = $amount;
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
}
