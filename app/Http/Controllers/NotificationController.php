<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationHistory;
use App\Models\Mitra;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotificationsForMitra()
    {
        $mitra = Mitra::where('user_id', Auth::id())->first();
        
        if (!$mitra) {
            return response()->json(['count' => 0, 'notifications' => []]);
        }
        
        $notifications = Notification::where('type', 'perusahaan_to_mitra')
            ->where('to_id', $mitra->MitraID)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $unreadCount = $notifications->where('dibaca', false)->count();
        
        return response()->json([
            'count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }
    
    public function getNotificationsForPerusahaan()
    {
        $perusahaan = Perusahaan::where('user_id', Auth::id())->first();
        
        if (!$perusahaan) {
            return response()->json(['count' => 0, 'notifications' => []]);
        }
        
        $notifications = Notification::where('type', 'mitra_to_perusahaan')
            ->where('to_id', $perusahaan->PerusahaanID)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $unreadCount = $notifications->where('dibaca', false)->count();
        
        return response()->json([
            'count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }
    
    public function markAsRead(Request $request)
    {
        $notificationId = $request->input('id');
        $notification = Notification::find($notificationId);
        
        if ($notification) {
            // Simpan notifikasi ke dalam tabel riwayat
            NotificationHistory::create([
                'notification_id' => $notification->id,
                'from_id' => $notification->from_id,
                'to_id' => $notification->to_id,
                'proyek_id' => $notification->proyek_id,
                'rekomendasi_id' => $notification->rekomendasi_id,
                'type' => $notification->type,
                'judul' => $notification->judul,
                'pesan' => $notification->pesan,
                'read_at' => now(), // Tandai waktu dibaca
                'created_at' => $notification->created_at, // Gunakan waktu dibuat asli
            ]);

            // Hapus notifikasi daripada hanya menandainya dibaca
            $notification->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function markAllAsRead(Request $request)
    {
        $type = $request->type; // 'mitra' atau 'perusahaan'
        $remainingCount = 0;
        
        if ($type == 'mitra') {
            $mitra = Mitra::where('user_id', Auth::id())->first();
            
            if ($mitra) {
                $notifications = Notification::where('type', 'perusahaan_to_mitra')
                    ->where('to_id', $mitra->MitraID)
                    ->where('dibaca', false)
                    ->get();
                
                foreach ($notifications as $notification) {
                    $this->moveToHistory($notification);
                    // Hapus notifikasi daripada hanya menandainya dibaca
                    $notification->delete();
                }
            }
        } else if ($type == 'perusahaan') {
            $perusahaan = Perusahaan::where('user_id', Auth::id())->first();
            
            if ($perusahaan) {
                $notifications = Notification::where('type', 'mitra_to_perusahaan')
                    ->where('to_id', $perusahaan->PerusahaanID)
                    ->where('dibaca', false)
                    ->get();
                
                foreach ($notifications as $notification) {
                    $this->moveToHistory($notification);
                    // Hapus notifikasi daripada hanya menandainya dibaca
                    $notification->delete();
                }
            }
        }

        return response()->json(['success' => true, 'remainingCount' => 0]);
    }

    private function moveToHistory($notification)
    {
        // Simpan notifikasi ke riwayat
        NotificationHistory::create([
            'notification_id' => $notification->id,
            'from_id' => $notification->from_id,
            'to_id' => $notification->to_id,
            'proyek_id' => $notification->proyek_id,
            'rekomendasi_id' => $notification->rekomendasi_id ?? null, // Tambahkan rekomendasi_id jika ada
            'type' => $notification->type,
            'judul' => $notification->judul,
            'pesan' => $notification->pesan,
            'read_at' => now(),
            'created_at' => $notification->created_at // Gunakan waktu dibuat asli dari notifikasi
        ]);
    }

    public function getNotificationHistory(Request $request)
    {
        // Ambil tipe user dari request
        $type = $request->input('type', null);
        
        // Jika tipe tidak disediakan, coba deteksi berdasarkan user yang login
        if (!$type) {
            // Cek apakah user adalah mitra
            $mitra = Mitra::where('user_id', Auth::id())->first();
            if ($mitra) {
                $type = 'mitra';
            } else {
                // Jika bukan mitra, anggap sebagai perusahaan
                $type = 'perusahaan';
            }
        }
        
        if ($type == 'mitra') {
            $mitra = Mitra::where('user_id', Auth::id())->first();
            
            if (!$mitra) {
                return response()->json(['notifications' => []]);
            }
            
            $history = NotificationHistory::where('type', 'perusahaan_to_mitra')
                ->where('to_id', $mitra->MitraID)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $perusahaan = Perusahaan::where('user_id', Auth::id())->first();
            
            if (!$perusahaan) {
                return response()->json(['notifications' => []]);
            }
            
            $history = NotificationHistory::where('type', 'mitra_to_perusahaan')
                ->where('to_id', $perusahaan->PerusahaanID)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json(['notifications' => $history]);
    }
    
    // Tambahkan fungsi untuk menghapus riwayat notifikasi
    public function deleteHistory(Request $request)
    {
        $historyId = $request->input('id');
        $history = NotificationHistory::find($historyId);
        
        if ($history) {
            $history->delete();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }
    
    // Tambahkan fungsi untuk menghapus semua riwayat notifikasi
    public function deleteAllHistory(Request $request)
    {
        $type = $request->input('type', null);
        
        // Jika tipe tidak disediakan, coba deteksi berdasarkan user yang login
        if (!$type) {
            // Cek apakah user adalah mitra
            $mitra = Mitra::where('user_id', Auth::id())->first();
            if ($mitra) {
                $type = 'mitra';
            } else {
                // Jika bukan mitra, anggap sebagai perusahaan
                $type = 'perusahaan';
            }
        }
        
        if ($type == 'mitra') {
            $mitra = Mitra::where('user_id', Auth::id())->first();
            
            if ($mitra) {
                NotificationHistory::where('type', 'perusahaan_to_mitra')
                    ->where('to_id', $mitra->MitraID)
                    ->delete();
            }
        } else {
            $perusahaan = Perusahaan::where('user_id', Auth::id())->first();
            
            if ($perusahaan) {
                NotificationHistory::where('type', 'mitra_to_perusahaan')
                    ->where('to_id', $perusahaan->PerusahaanID)
                    ->delete();
            }
        }
        
        return response()->json(['success' => true]);
    }
}