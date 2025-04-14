$(document).ready(function() {
    // Deteksi tipe user (mitra atau perusahaan)
    var userType = $('meta[name="user-type"]').attr('content') || 'mitra';
    
    // Variable untuk melacak status tampilan (active/history)
    var currentView = 'active';
    
    // Inisialisasi dengan memuat notifikasi aktif
    loadActiveNotifications();
    
    // Menangani klik pada notifikasi
    $(document).on('click', '.mark-as-read', function(e) {
        e.preventDefault();
        var notificationId = $(this).data('id');
        
        $.ajax({
            url: '/notifications/' + notificationId + '/read',
            type: 'POST',
            data: {
                id: notificationId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Hapus notifikasi dari tampilan
                    $('#notificationList').find('[data-id="' + notificationId + '"]').remove();
                    // Update badge count
                    updateNotificationCount();
                }
            }
        });
    });
    
    // Menangani klik pada "Riwayat"
    $('#showHistory').on('click', function(e) {
        e.preventDefault();
        
        if (currentView === 'history') {
            return; // Sudah di tampilan riwayat
        }
        
        $.ajax({
            url: '/notifications/history',
            type: 'GET',
            data: {
                type: userType
            },
            success: function(response) {
                currentView = 'history';
                
                // Tampilkan riwayat notifikasi
                $('#notificationList').empty(); // Kosongkan daftar notifikasi
                
                if (response.notifications.length === 0) {
                    $('#notificationList').append(`
                        <div class="dropdown-item">
                            <p class="text-center text-muted">Tidak ada riwayat notifikasi</p>
                        </div>
                    `);
                } else {
                    response.notifications.forEach(function(notification) {
                        let notificationHtml = `
                            <div class="dropdown-item preview-item history-item" data-id="${notification.id}">
                                <div class="preview-item-content">
                                    <p class="preview-subject font-weight-medium">${notification.judul}</p>
                                    <p class="text-muted small">${notification.pesan}</p>
                                    <p class="text-muted smaller">${formatDateTime(notification.read_at)}</p>
                        `;
                        
                        if (notification.rekomendasi_id) {
                            notificationHtml += `
                                    <a href="/rekomendasis/${notification.rekomendasi_id}/edit" class="btn btn-sm btn-outline-primary mt-1">Lihat Rekomendasi</a>
                            `;
                        }
                        
                        notificationHtml += `
                                    <button class="btn btn-sm btn-outline-danger mt-1 delete-history" data-id="${notification.id}">Hapus</button>
                                </div>
                            </div>
                        `;
                        
                        $('#notificationList').append(notificationHtml);
                    });
                }
                
                // Ubah header dropdown
                $('.dropdown-header').html(`
                    <p class="mb-0 font-weight-normal">Riwayat Notifikasi</p>
                    <a href="#" class="text-muted" id="showActiveNotifications">Kembali</a>
                `);
                
                // Ubah footer dropdown
                $('.dropdown-divider').next().html(`
                    <a class="dropdown-item text-center" href="#" id="deleteAllHistory">Hapus Semua Riwayat</a>
                `);
                
                // Reset badge count (karena kita sedang melihat riwayat)
                $('#notificationCount').text('0');
            },
            error: function() {
                alert('Gagal memuat riwayat notifikasi. Silakan coba lagi.');
            }
        });
    });
    
    // Menangani klik pada "Hapus" di riwayat notifikasi
    $(document).on('click', '.delete-history', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var historyId = $(this).data('id');
        var historyItem = $(this).closest('.history-item');
        
        $.ajax({
            url: '/notifications/history/delete',
            type: 'POST',
            data: {
                id: historyId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Hapus dari tampilan
                    historyItem.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Cek apakah masih ada riwayat
                        if ($('#notificationList .history-item').length === 0) {
                            $('#notificationList').html(`
                                <div class="dropdown-item">
                                    <p class="text-center text-muted">Tidak ada riwayat notifikasi</p>
                                </div>
                            `);
                        }
                    });
                }
            },
            error: function() {
                alert('Gagal menghapus riwayat. Silakan coba lagi.');
            }
        });
    });
    
    // Menangani klik pada "Hapus Semua Riwayat"
    $(document).on('click', '#deleteAllHistory', function(e) {
        e.preventDefault();
        
        if (confirm('Anda yakin ingin menghapus semua riwayat notifikasi?')) {
            $.ajax({
                url: '/notifications/history/delete-all',
                type: 'POST',
                data: {
                    type: userType,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Kosongkan daftar riwayat
                        $('#notificationList').html(`
                            <div class="dropdown-item">
                                <p class="text-center text-muted">Tidak ada riwayat notifikasi</p>
                            </div>
                        `);
                    }
                },
                error: function() {
                    alert('Gagal menghapus semua riwayat. Silakan coba lagi.');
                }
            });
        }
    });
    
    // Menangani klik pada "Kembali" (ditambahkan secara dinamis)
    $(document).on('click', '#showActiveNotifications', function(e) {
        e.preventDefault();
        
        if (currentView === 'active') {
            return; // Sudah di tampilan aktif
        }
        
        // Kembalikan header dropdown
        $('.dropdown-header').html(`
            <p class="mb-0 font-weight-normal">Notifikasi</p>
            <a href="#" class="text-muted" id="showHistory">Riwayat</a>
        `);
        
        // Kembalikan footer dropdown
        $('.dropdown-divider').next().html(`
            <a class="dropdown-item text-center" href="#" id="markAllAsRead">Tandai semua sudah dibaca</a>
        `);
        
        loadActiveNotifications();
    });
    
    // Menangani klik pada "Tandai semua sudah dibaca"
    $(document).on('click', '#markAllAsRead', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/notifications/mark-all-read',
            type: 'POST',
            data: {
                type: userType,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Kosongkan daftar notifikasi
                    $('#notificationList').html(`
                        <div class="dropdown-item">
                            <p class="text-center text-muted">Tidak ada notifikasi baru</p>
                        </div>
                    `);
                    
                    // Update badge count
                    $('#notificationCount').text('0');
                }
            }
        });
    });
    
    // Function untuk memformat tanggal & waktu
    function formatDateTime(dateTimeStr) {
        const date = new Date(dateTimeStr);
        return date.toLocaleString('id-ID', { 
            day: 'numeric', 
            month: 'short', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    // Function untuk update counter notifikasi
    function updateNotificationCount() {
        var count = $('#notificationList .mark-as-read').length;
        $('#notificationCount').text(count);
        
        // Jika tidak ada notifikasi, tampilkan pesan
        if (count === 0) {
            $('#notificationList').html(`
                <div class="dropdown-item">
                    <p class="text-center text-muted">Tidak ada notifikasi baru</p>
                </div>
            `);
        }
    }
    
    // Function untuk memuat notifikasi aktif
    function loadActiveNotifications() {
        currentView = 'active';
        var endpoint = userType === 'mitra' ? '/notifications/mitra' : '/notifications/perusahaan';
        
        $.ajax({
            url: endpoint,
            type: 'GET',
            success: function(response) {
                $('#notificationList').empty();
                
                if (!response.notifications || response.notifications.length === 0) {
                    $('#notificationList').append(`
                        <div class="dropdown-item">
                            <p class="text-center text-muted">Tidak ada notifikasi baru</p>
                        </div>
                    `);
                } else {
                    response.notifications.forEach(function(notification) {
                        let notificationHtml;
                        
                        if (notification.rekomendasi_id) {
                            notificationHtml = `
                                <a href="/rekomendasis/${notification.rekomendasi_id}/edit" class="dropdown-item preview-item mark-as-read" data-id="${notification.id}">
                                    <div class="preview-item-content">
                                        <p class="preview-subject font-weight-medium">${notification.judul}</p>
                                        <p class="text-muted small">${notification.pesan}</p>
                                    </div>
                                </a>
                            `;
                        } else {
                            notificationHtml = `
                                <div class="dropdown-item preview-item mark-as-read" data-id="${notification.id}">
                                    <div class="preview-item-content">
                                        <p class="preview-subject font-weight-medium">${notification.judul}</p>
                                        <p class="text-muted small">${notification.pesan}</p>
                                        <p class="text-danger small">[Tidak ada rekomendasi terkait]</p>
                                    </div>
                                </div>
                            `;
                        }
                        
                        $('#notificationList').append(notificationHtml);
                    });
                }
                
                // Update badge count
                $('#notificationCount').text(response.count || 0);
            },
            error: function() {
                console.error('Gagal memuat notifikasi aktif');
                $('#notificationList').html(`
                    <div class="dropdown-item">
                        <p class="text-center text-danger">Gagal memuat notifikasi</p>
                    </div>
                `);
            }
        });
    }
});