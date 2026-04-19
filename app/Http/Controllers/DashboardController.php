<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\DailySale;
use App\Models\Expense;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function lowStock()
    {
        $items = Item::whereRaw('stock <= reorder_level')->get();
        return response()->json($items);
    }

    public function todaySales()
    {
        $total = DailySale::whereDate('sale_date', today())->sum('total_amount');
        return response()->json($total);
    }

    public function todayExpenses()
    {
        $total = Expense::whereDate('expense_date', today())->sum('amount');
        return response()->json($total);
    }

    public function salesChartData(Request $request)
    {
        $period = $request->get('period', 'month');
        $data = ['labels' => [], 'values' => []];

        if ($period == 'day') {
            $date = today();
            $total = DailySale::whereDate('sale_date', $date)->sum('total_amount');
            $data['labels'][] = $date->format('Y-m-d');
            $data['values'][] = $total;
        } elseif ($period == 'week') {
            $start = now()->startOfWeek();
            for ($i = 0; $i < 7; $i++) {
                $date = $start->copy()->addDays($i);
                $total = DailySale::whereDate('sale_date', $date)->sum('total_amount');
                $data['labels'][] = $date->format('D');
                $data['values'][] = $total;
            }
        } else { // month
            $daysInMonth = now()->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $date = now()->startOfMonth()->addDays($i - 1);
                $total = DailySale::whereDate('sale_date', $date)->sum('total_amount');
                $data['labels'][] = $i;
                $data['values'][] = $total;
            }
        }
        return response()->json($data);
    }
}