<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Trip;
use App\Models\User;
use App\Models\ContactMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Get date range from request or default to current month
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Parse dates
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        
        // Get overview statistics
        $stats = $this->getOverviewStats($start, $end);
        
        // Get booking trends
        $bookingTrends = $this->getBookingTrends($start, $end);
        
        // Get revenue trends
        $revenueTrends = $this->getRevenueTrends($start, $end);
        
        // Get popular routes
        $popularRoutes = $this->getPopularRoutes($start, $end);
        
        // Get payment method breakdown
        $paymentMethods = $this->getPaymentMethodBreakdown($start, $end);
        
        // Get recent bookings
        $recentBookings = $this->getRecentBookings($start, $end);
        
        // Get contact messages (feedback)
        $contactMessages = ContactMessage::orderBy('created_at', 'desc')->paginate(10);
        
        return view('reports.index', compact(
            'stats',
            'bookingTrends',
            'revenueTrends',
            'popularRoutes',
            'paymentMethods',
            'recentBookings',
            'contactMessages',
            'startDate',
            'endDate'
        ));
    }
    
    private function getOverviewStats($start, $end)
    {
        $totalBookings = Booking::whereBetween('created_at', [$start, $end])->count();
        $totalRevenue = Booking::whereBetween('created_at', [$start, $end])
            ->where('status', 'confirmed')
            ->sum('total_amount');
        $totalPassengers = Booking::whereBetween('created_at', [$start, $end])
            ->sum(DB::raw('adult + child + infant + pwd + student'));
        $averageBookingValue = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;
        
        // Get previous period for comparison
        $periodLength = $start->diffInDays($end);
        $prevStart = $start->copy()->subDays($periodLength + 1);
        $prevEnd = $start->copy()->subDay();
        
        $prevTotalBookings = Booking::whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $prevTotalRevenue = Booking::whereBetween('created_at', [$prevStart, $prevEnd])
            ->where('status', 'confirmed')
            ->sum('total_amount');
        
        return [
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'total_passengers' => $totalPassengers,
            'average_booking_value' => $averageBookingValue,
            'bookings_growth' => $prevTotalBookings > 0 ? (($totalBookings - $prevTotalBookings) / $prevTotalBookings) * 100 : 0,
            'revenue_growth' => $prevTotalRevenue > 0 ? (($totalRevenue - $prevTotalRevenue) / $prevTotalRevenue) * 100 : 0,
        ];
    }
    
    private function getBookingTrends($start, $end)
    {
        return Booking::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
    
    private function getRevenueTrends($start, $end)
    {
        return Booking::whereBetween('created_at', [$start, $end])
            ->where('status', 'confirmed')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
    
    private function getPopularRoutes($start, $end)
    {
        return Booking::whereBetween('created_at', [$start, $end])
            ->selectRaw('CONCAT(origin, " → ", destination) as route, COUNT(*) as bookings, SUM(total_amount) as revenue')
            ->groupBy('origin', 'destination')
            ->orderBy('bookings', 'desc')
            ->limit(10)
            ->get();
    }
    
    private function getPaymentMethodBreakdown($start, $end)
    {
        return Booking::whereBetween('created_at', [$start, $end])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('payment_method')
            ->get();
    }
    
    private function getRecentBookings($start, $end)
    {
        return Booking::whereBetween('created_at', [$start, $end])
            ->with('trip')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }
    
    public function export(Request $request)
    {
        $type = $request->get('type', 'bookings');
        $format = $request->get('format', 'csv');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        
        switch ($type) {
            case 'bookings':
                return $this->exportBookings($start, $end, $format);
            case 'revenue':
                return $this->exportRevenue($start, $end, $format);
            case 'passengers':
                return $this->exportPassengers($start, $end, $format);
            default:
                return redirect()->back()->with('error', 'Invalid export type');
        }
    }
    
    private function exportBookings($start, $end, $format)
    {
        $bookings = Booking::whereBetween('created_at', [$start, $end])
            ->with('trip')
            ->get();
        
        $filename = 'bookings_' . $start->format('Y-m-d') . '_to_' . $end->format('Y-m-d') . '.' . $format;
        
    if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($bookings) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Date', 'Route', 'Passenger Name', 'Email', 'Phone', 'Adults', 'Children', 'Infants', 'PWD', 'Total Amount', 'Payment Method', 'Status']);
                
                foreach ($bookings as $booking) {
                    fputcsv($file, [
                        $booking->id,
                        $booking->created_at->format('Y-m-d H:i:s'),
                        $booking->origin . ' → ' . $booking->destination,
                        $booking->full_name,
                        $booking->email,
                        $booking->phone,
                        $booking->adult,
                        $booking->child,
                        $booking->infant,
                        $booking->pwd,
                        $booking->student,
                        $booking->total_amount,
                        $booking->payment_method,
                        $booking->status,
                    ]);
                }
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }

        if ($format === 'sql') {
            $filename = 'bookings_' . $start->format('Y-m-d') . '_to_' . $end->format('Y-m-d') . '.sql';
            $headers = [
                'Content-Type' => 'application/sql; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($bookings) {
                $out = fopen('php://output', 'w');
                // Helper to escape values for SQL
                $escape = function($value) {
                    if (is_null($value)) return 'NULL';
                    if ($value === true) return '1';
                    if ($value === false) return '0';
                    // Convert arrays/objects to JSON
                    if (is_array($value) || is_object($value)) {
                        $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                    }
                    // Use single quotes and escape existing single quotes
                    return "'" . str_replace("'", "''", (string)$value) . "'";
                };

                // Determine columns dynamically from DB schema (falls back to common fields)
                try {
                    $columns = Schema::getColumnListing('bookings');
                } catch (\Exception $e) {
                    $columns = ['id','user_id','trip_id','origin','destination','departure_time','adult','child','infant','pwd','student','full_name','email','phone','payment_method','payment_reference','total_amount','status','reference','contact','meta','created_at','updated_at'];
                }

                fwrite($out, "-- SQL export generated by application\n");
                fwrite($out, "SET FOREIGN_KEY_CHECKS=0;\n\n");
                fwrite($out, "TRUNCATE TABLE `bookings`;\n\n");

                foreach ($bookings as $booking) {
                    $values = [];
                    foreach ($columns as $col) {
                        $val = $booking->{$col} ?? null;
                        // If column contains JSONable fields, ensure string
                        if (is_array($val) || is_object($val)) {
                            $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                        }
                        $values[] = $escape($val);
                    }

                    $cols = implode('`, `', $columns);
                    $vals = implode(', ', $values);
                    fwrite($out, "INSERT INTO `bookings` (`$cols`) VALUES ($vals);\n");
                }

                fwrite($out, "\nSET FOREIGN_KEY_CHECKS=1;\n");
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Unsupported format');
    }
    
    private function exportRevenue($start, $end, $format)
    {
        $revenue = Booking::whereBetween('created_at', [$start, $end])
            ->where('status', 'confirmed')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as bookings')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $filename = 'revenue_' . $start->format('Y-m-d') . '_to_' . $end->format('Y-m-d') . '.' . $format;
        
    if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($revenue) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Date', 'Revenue', 'Bookings']);
                
                foreach ($revenue as $row) {
                    fputcsv($file, [
                        $row->date,
                        $row->revenue,
                        $row->bookings,
                    ]);
                }
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }
        if ($format === 'sql') {
            $filename = 'revenue_' . $start->format('Y-m-d') . '_to_' . $end->format('Y-m-d') . '.sql';
            $headers = [
                'Content-Type' => 'application/sql; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($revenue) {
                $out = fopen('php://output', 'w');
                fwrite($out, "-- Revenue report SQL export\n");
                fwrite($out, "CREATE TABLE IF NOT EXISTS `revenue_report` (`date` DATE, `revenue` DECIMAL(12,2), `bookings` INT);\n\n");
                fwrite($out, "TRUNCATE TABLE `revenue_report`;\n\n");

                foreach ($revenue as $row) {
                    $date = $row->date;
                    $rev = number_format((float)$row->revenue, 2, '.', '');
                    $bookings = (int)$row->bookings;
                    fwrite($out, "INSERT INTO `revenue_report` (`date`, `revenue`, `bookings`) VALUES ('$date', $rev, $bookings);\n");
                }

                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Unsupported format');
    }
    
    private function exportPassengers($start, $end, $format)
    {
        $passengers = Booking::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, SUM(adult + child + infant + pwd + student) as total_passengers, SUM(adult) as adults, SUM(child) as children, SUM(infant) as infants, SUM(pwd) as pwd, SUM(student) as students')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $filename = 'passengers_' . $start->format('Y-m-d') . '_to_' . $end->format('Y-m-d') . '.' . $format;
        
    if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($passengers) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Date', 'Total Passengers', 'Adults', 'Children', 'Infants', 'PWD/Senior']);
                
                foreach ($passengers as $row) {
                    fputcsv($file, [
                        $row->date,
                        $row->total_passengers,
                        $row->adults,
                        $row->children,
                        $row->infants,
                        $row->pwd,
                        $row->students,
                    ]);
                }
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }
        if ($format === 'sql') {
            $filename = 'passengers_' . $start->format('Y-m-d') . '_to_' . $end->format('Y-m-d') . '.sql';
            $headers = [
                'Content-Type' => 'application/sql; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($passengers) {
                $out = fopen('php://output', 'w');
                fwrite($out, "-- Passengers report SQL export\n");
                fwrite($out, "CREATE TABLE IF NOT EXISTS `passengers_report` (`date` DATE, `total_passengers` INT, `adults` INT, `children` INT, `infants` INT, `pwd` INT, `students` INT);\n\n");
                fwrite($out, "TRUNCATE TABLE `passengers_report`;\n\n");

                foreach ($passengers as $row) {
                    $date = $row->date;
                    $tp = (int)$row->total_passengers;
                    $ad = (int)$row->adults;
                    $ch = (int)$row->children;
                    $inf = (int)$row->infants;
                    $pwd = (int)$row->pwd;
                    $st = (int)$row->students;
                    fwrite($out, "INSERT INTO `passengers_report` (`date`, `total_passengers`, `adults`, `children`, `infants`, `pwd`, `students`) VALUES ('$date', $tp, $ad, $ch, $inf, $pwd, $st);\n");
                }

                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Unsupported format');
    }
}
