<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Tambahkan meta untuk identifikasi tipe user -->
    <meta name="user-type" content="{{ auth()->user()->isMitra() ? 'mitra' : 'perusahaan' }}">

<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <!-- <a class="navbar-brand brand-logo mr-5" href="index.html"><img src="/images/logo.svg" class="mr-2" alt="logo"/></a>
        <a class="navbar-brand brand-logo-mini" href="index.html"><img src="/images/logo-mini.svg" alt="logo"/></a> -->
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        <ul class="navbar-nav mr-lg-2">
          <li class="nav-item nav-search d-none d-lg-block">
            <div class="input-group">
              <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                <span class="input-group-text" id="search">
                  <i class="icon-search"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now" aria-label="search" aria-describedby="search">
            </div>
          </li>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
   
    
    <li class="nav-item dropdown">
        <a class="nav-link count-indicator dropdown-toggle" href="#" id="notificationDropdown" role="button" data-toggle="dropdown">
            <i class="icon-bell mx-0"></i>
            <span class="badge badge-danger notification-badge" id="notificationCount">
                {{ $allNotifications->where('dibaca', false)->count() }}
            </span>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
            <div class="d-flex justify-content-between align-items-center dropdown-header">
                <p class="mb-0 font-weight-normal">Notifikasi</p>
                <a href="#" class="text-muted" id="showHistory">Riwayat</a>
            </div>
            
            <div id="notificationList">
                @forelse ($allNotifications as $notification)
                    @if ($notification->rekomendasi_id)
                        <a href="{{ route('rekomendasis.edit', $notification->rekomendasi_id) }}" class="dropdown-item preview-item mark-as-read" data-id="{{ $notification->id }}">
                            <div class="preview-item-content">
                                <p class="preview-subject font-weight-medium">{{ $notification->judul }}</p>
                                <p class="text-muted small">{{ $notification->pesan }}</p>
                            </div>
                        </a>
                    @else
                        <div class="dropdown-item preview-item mark-as-read" data-id="{{ $notification->id }}">
                            <div class="preview-item-content">
                                <p class="preview-subject font-weight-medium">{{ $notification->judul }}</p>
                                <p class="text-muted small">{{ $notification->pesan }}</p>
                                <!-- <p class="text-danger small">[Tidak ada rekomendasi terkait]</p> -->
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="dropdown-item">
                        <p class="text-center text-muted">Tidak ada notifikasi baru</p>
                    </div>
                @endforelse
            </div>
            
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-center" href="#" id="markAllAsRead">Tandai semua sudah dibaca</a>
        </div>
    </li>

    @push('scripts')
<script>
    $(document).ready(function() {
        // Mark individual notification as read
        $('.mark-read').on('click', function() {
            const notificationId = $(this).data('id');
            
            $.ajax({
                url: '/notifications/' + notificationId + '/mark-read',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#notification-' + notificationId).fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if no more notifications
                        if ($('.notification-item').length === 0) {
                            $('.notification-list').html('<div class="text-center py-3 text-muted">Tidak ada notifikasi baru</div>');
                            
                            // Optional: Hide the entire notification card after a delay
                            setTimeout(function() {
                                $('.notification-list').closest('.row').fadeOut();
                            }, 2000);
                        }
                    });
                }
            });
        });
        
        // Mark all notifications as read
        $('#markAllRead').on('click', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '/notifications/mark-all-read',
                type: 'POST',
                data: {
                    type: 'mitra_to_perusahaan',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('.notification-item').fadeOut(300, function() {
                        $('.notification-list').html('<div class="text-center py-3 text-muted">Tidak ada notifikasi baru</div>');
                        
                        // Optional: Hide the entire notification card after a delay
                        setTimeout(function() {
                            $('.notification-list').closest('.row').fadeOut();
                        }, 2000);
                    });
                }
            });
        });
    });
</script>
@endpush



          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              <img src="/images/faces/hijabgirl.jpg" alt="profile"/>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item">
                <i class="ti-settings text-primary"></i>
                Settings
              </a>
              <a class="dropdown-item">
                <i class="ti-power-off text-primary"></i>
                Logout
              </a>
            </div>
          </li>
          <li class="nav-item nav-settings d-none d-lg-flex">
            <a class="nav-link" href="#">
              <i class="icon-ellipsis"></i>
            </a>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>