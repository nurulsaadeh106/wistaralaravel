<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SalesAnalyticsController extends Controller
{
    protected bool $hasPaymentStatusColumn;

    public function __construct()
    {
        $this->hasPaymentStatusColumn = Schema::hasColumn('orders', 'status_pembayaran');
    }

    /**
     * Return monthly sales summary plus drill-down data per product.
     */
    public function monthlySales(Request $request)
    {
        if (!session()->has('admin_logged_in')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $months = (int) $request->input('months', 12);
        $months = $months < 1 ? 12 : min($months, 24);

        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();

        $products = Produk::orderBy('nama_produk')
            ->get(['id_produk', 'nama_produk']);

        $monthPeriod = collect(range($months - 1, 0))->map(function ($monthsAgo) {
            $date = Carbon::now()->copy()->subMonths($monthsAgo)->startOfMonth();
            return [
                'key' => $date->format('Y-m'),
                'label' => $date->format('M Y'),
            ];
        });

        $summaryQuery = Order::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') AS `year_month`")
            ->selectRaw("SUM(total) AS total_sales")
            ->selectRaw("COUNT(*) AS order_count")
            ->where('created_at', '>=', $startDate)
            ->whereRaw('status <> ?', ['batal'])
            ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(created_at, '%Y-%m')");

        if ($this->hasPaymentStatusColumn) {
            $summaryQuery->where('status_pembayaran', 'lunas');
        }

        $summaryRows = $summaryQuery->get();

        $detailQuery = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('produk', 'order_items.id_produk', '=', 'produk.id_produk')
            ->selectRaw("DATE_FORMAT(orders.created_at, '%Y-%m') AS `year_month`")
            ->selectRaw("order_items.id_produk AS produk_id")
            ->selectRaw("COALESCE(produk.nama_produk, 'Produk Tanpa Nama') AS produk_name")
            ->selectRaw("SUM(order_items.qty) AS total_qty")
            ->selectRaw("SUM(order_items.subtotal) AS total_sales")
            ->where('orders.created_at', '>=', $startDate)
            ->whereRaw('orders.status <> ?', ['batal'])
            ->groupByRaw("DATE_FORMAT(orders.created_at, '%Y-%m')")
            ->groupBy('order_items.id_produk')
            ->groupBy('produk.nama_produk')
            ->orderByRaw("DATE_FORMAT(orders.created_at, '%Y-%m')");

        if ($this->hasPaymentStatusColumn) {
            $detailQuery->where('orders.status_pembayaran', 'lunas');
        }

        $detailRows = $detailQuery->get();

        $summary = $this->buildSummary($monthPeriod, $summaryRows);
        $details = $this->mapDetailRows($detailRows, $monthPeriod, $products);

        return response()->json([
            'summary' => $summary,
            'details' => $details,
            'products' => $products->map(function ($product) {
                return [
                    'id' => $product->id_produk,
                    'name' => $product->nama_produk,
                ];
            })->values(),
        ]);
    }

    protected function buildSummary(Collection $monthPeriod, Collection $summaryRows): Collection
    {
        $summary = $monthPeriod->mapWithKeys(function ($info) {
            return [
                $info['key'] => [
                    'key' => $info['key'],
                    'label' => $info['label'],
                    'total' => 0.0,
                    'orders' => 0,
                ],
            ];
        });

        $summaryRows->each(function ($row) use (&$summary) {
            if ($summary->has($row->year_month)) {
                $existing = $summary->get($row->year_month);
                $existing['total'] = (float) $row->total_sales;
                $existing['orders'] = (int) $row->order_count;
                $summary->put($row->year_month, $existing);
            }
        });

        return $summary->values();
    }

    /**
     * Group raw detail rows by month.
     */
    protected function mapDetailRows(Collection $rows, Collection $monthPeriod, Collection $products): array
    {
        $baseDetail = $products->mapWithKeys(function ($product) {
            return [
                $product->id_produk => [
                    'id' => $product->id_produk,
                    'name' => $product->nama_produk,
                    'total' => 0.0,
                    'qty' => 0,
                ],
            ];
        });

        $monthlyDetails = [];
        foreach ($monthPeriod as $info) {
            $monthlyDetails[$info['key']] = $baseDetail->map(function ($item) {
                return $item;
            })->toArray();
        }

        $rows->each(function ($row) use (&$monthlyDetails) {
            $monthKey = $row->year_month;
            if (!isset($monthlyDetails[$monthKey])) {
                return;
            }

            $productId = $row->produk_id;
            if ($productId !== null && isset($monthlyDetails[$monthKey][$productId])) {
                $monthlyDetails[$monthKey][$productId]['total'] = (float) $row->total_sales;
                $monthlyDetails[$monthKey][$productId]['qty'] = (int) $row->total_qty;
            } else {
                $monthlyDetails[$monthKey]['unknown_' . uniqid()] = [
                    'id' => $productId,
                    'name' => $row->produk_name ?? 'Produk Tanpa Nama',
                    'total' => (float) $row->total_sales,
                    'qty' => (int) $row->total_qty,
                ];
            }
        });

        return collect($monthlyDetails)
            ->map(function ($items) {
                return collect($items)->values()->toArray();
            })
            ->toArray();
    }
}
