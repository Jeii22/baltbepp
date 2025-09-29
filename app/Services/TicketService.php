<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class TicketService
{
    /**
     * Generate a PDF ticket for the booking
     */
    public function generateTicket(Booking $booking): string
    {
        // Load the booking with trip relationship
        $booking->load('trip');
        
        // Generate QR code data (booking reference)
        $qrData = $booking->payment_reference ?? 'BLT-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT);
        
        // Prepare ticket data
        $ticketData = [
            'booking' => $booking,
            'qrData' => $qrData,
            'generatedAt' => now()->format('F j, Y - g:i A'),
        ];
        
        // Generate PDF
        $pdf = Pdf::loadView('tickets.ferry-ticket', $ticketData);
        $pdf->setPaper('A4', 'portrait');
        
        // Create filename
        $filename = 'ticket-' . $booking->id . '-' . time() . '.pdf';
        $filepath = 'tickets/' . $filename;
        
        // Ensure tickets directory exists
        if (!Storage::disk('public')->exists('tickets')) {
            Storage::disk('public')->makeDirectory('tickets');
        }
        
        // Save PDF to storage
        Storage::disk('public')->put($filepath, $pdf->output());
        
        // Return full path
        return storage_path('app/public/' . $filepath);
    }
    
    /**
     * Generate ticket data for email attachment
     */
    public function generateTicketForEmail(Booking $booking): ?string
    {
        try {
            return $this->generateTicket($booking);
        } catch (\Exception $e) {
            \Log::error('Failed to generate ticket for booking ' . $booking->id . ': ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Clean up old ticket files (optional - for maintenance)
     */
    public function cleanupOldTickets(int $daysOld = 30): void
    {
        $cutoffDate = now()->subDays($daysOld);
        $files = Storage::disk('public')->files('tickets');
        
        foreach ($files as $file) {
            $lastModified = Storage::disk('public')->lastModified($file);
            if ($lastModified < $cutoffDate->timestamp) {
                Storage::disk('public')->delete($file);
            }
        }
    }
}