<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use ArielMejiaDev\LarapexCharts\Facades\LarapexChart;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function ViewHome()
    {
        // Ambil produk dari database dan kelompokkan berdasarkan tanggal
        $produkPerHari = Produk::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Memisahkan data untuk grafik
        $dates = [];
        $totals = [];

        foreach ($produkPerHari as $item) {
            $dates[] = Carbon::parse($item->date)->format('Y-m-d'); // Format tanggal
            $totals[] = $item->total;

        }

                // Membuat grafik menggunakan data yang diambil
                $chart = LarapexChart::barChart()
                ->setTitle('Produk Ditambahkan Per Hari')
                ->setSubtitle('Data Penambahan Produk Harian')
                ->addData('Jumlah Produk', $totals)
                ->setXAxis($dates);

            // Data tambahan untuk view
            $data = [
                'totalProducts' => Produk::count(), // Total produk
                'salesToday' => 130, //  data lainnya
                'totalRevenue' => 'Rp 75,000,000',
                'registeredUsers' => 350,
                'chart' => $chart // Pass chart ke view
            ];

            return view('home', $data);
        }
    }
