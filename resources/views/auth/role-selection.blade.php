<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Pemilihan Peran</title>
    <link rel="stylesheet" href="{{ asset('vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vertical-layout-light/style.css') }}">
    <link rel="icon" href="{{ asset('assets/img/favicons.ico') }}" >
</head>

<body>
    <div class="content-wrapper">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="col-md-6 mx-auto">
                                <div class="card">
                                    <form id="roleForm" action="{{ route('auth.set-role', ['id' => $user->id]) }}" method="POST">
                                        @csrf
                                        <div class="card-body text-center">
                                            <h4 class="card-title">Pilih Peran</h4>
                                            <p class="card-description">Silakan pilih peran Anda</p>
                                            <div class="d-flex flex-column align-items-center">
                                                <button type="button" class="btn btn-info btn-lg px-4 d-flex align-items-center justify-content-center mb-3"
                                                    style="min-width: 200px;" onclick="setRole('mitra')">
                                                    Pemberi Proyek <i class="ti-user ml-2"></i>
                                                </button>
                                                <button type="button" class="btn btn-primary btn-lg px-4 d-flex align-items-center justify-content-center"
                                                    style="min-width: 200px;" onclick="setRole('perusahaan')">
                                                    <i class="ti-user mr-2"></i> Penerima Proyek
                                                </button>
                                            </div>
                                        </div>
                                    </form> <!-- Menutup form dengan benar -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>

    <script>
        function setRole(role) {
            var form = document.getElementById('roleForm');

            // Tambahkan input hidden untuk role
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'role';
            input.value = role;
            form.appendChild(input);

            // Submit form
            form.submit();
        }
    </script>

    <script src="{{ asset('vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('js/off-canvas.js') }}"></script>
    <script src="{{ asset('js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('js/template.js') }}"></script>
    <script src="{{ asset('js/settings.js') }}"></script>
    <script src="{{ asset('js/todolist.js') }}"></script>
</body>

</html>
