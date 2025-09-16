<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Trip;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run()
    {
        // Get some trips to associate with bookings
        $trips = Trip::all();
        
        if ($trips->isEmpty()) {
            $this->command->info('No trips found. Please seed trips first.');
            return;
        }

        $paymentMethods = ['cod', 'gcash', 'paymaya', 'card'];
        $statuses = ['confirmed', 'pending', 'cancelled'];
        
        // Create bookings for the last 30 days
        for ($i = 0; $i < 50; $i++) {
            $trip = $trips->random();
            $createdAt = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            
            $adultCount = rand(1, 4);
            $childCount = rand(0, 2);
            $infantCount = rand(0, 1);
            $pwdCount = rand(0, 1);
            $studentCount = rand(0, 1);
            
            $totalAmount = ($adultCount * 900) + ($childCount * 450) + ($infantCount * 0) + ($pwdCount * 720) + ($studentCount * 765);
            
            Booking::create([
                'trip_id' => $trip->id,
                'origin' => $trip->origin,
                'destination' => $trip->destination,
                'departure_time' => $trip->departure_time,
                'adult' => $adultCount,
                'child' => $childCount,
                'infant' => $infantCount,
                'pwd' => $pwdCount,
                'student' => $studentCount,
                'full_name' => $this->generateRandomName(),
                'email' => $this->generateRandomEmail(),
                'phone' => $this->generateRandomPhone(),
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
        
        $this->command->info('Created 50 sample bookings for testing reports.');
    }
    
    private function generateRandomName()
    {
        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Carmen', 'Luis', 'Rosa', 'Carlos', 'Elena'];
        $lastNames = ['Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres', 'Flores', 'Rivera'];
        
        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }
    
    private function generateRandomEmail()
    {
        $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
        $username = 'user' . rand(1000, 9999);
        
        return $username . '@' . $domains[array_rand($domains)];
    }
    
    private function generateRandomPhone()
    {
        return '09' . rand(100000000, 999999999);
    }
}