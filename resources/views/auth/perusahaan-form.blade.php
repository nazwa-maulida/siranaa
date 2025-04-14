<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Pemilihan Peran</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="../../vendors/feather/feather.css">
  <link rel="stylesheet" href="../../vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="../../css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="icon" href="{{ asset('assets/img/favicons.ico') }}" >
</head>

<body>
    <!-- partial -->
     <div class="content-wrapper">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
          <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
              <div class="col-lg-4 mx-auto">
                <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                <div class="card-body">
                  <h4 class="card-title">Form Perusahaan</h4>
                  <p class="card-description">
                    Input data perusahaan anda
                  </p>
                  <form action="{{ route('perusahaan.submit', ['id' => $user->id]) }}" method="POST">
                  @csrf
                    <div class="form-group">
                      <label for="NamaPerusahaan">Nama Perusahaan</label>
                      <input type="text" class="form-control" name="NamaPerusahaan" placeholder="Nama Perusahaan" required>
                    </div>
                    <div class="form-group">
                      <label for="PIC">Nama PIC</label>
                      <input type="text" class="form-control" name="PIC" placeholder="Nama PIC">
                    </div>
                    <div class="form-group">
                      <label for="NoTelp">No Telp</label>
                      <input type="text" class="form-control" name="NoTelp" placeholder="No Telp" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                    </div>
                    <div class="form-group">
                      <label for="Alamat">Alamat</label>
                      <input type="text" class="form-control" name="Alamat" placeholder="Alamat">
                    </div>
                    <div class="form-check form-check-flat form-check-primary">
                      <label class="form-check-label">
                        <input type="checkbox" class="form-check-input">
                        Remember me
                      </label>
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          </div>
        </div>

        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        
        <!-- partial -->
      </div>
      <!-- main-panel ends -->

    <!-- page-body-wrapper ends -->
  
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="../../vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="../../js/off-canvas.js"></script>
  <script src="../../js/hoverable-collapse.js"></script>
  <script src="../../js/template.js"></script>
  <script src="../../js/settings.js"></script>
  <script src="../../js/todolist.js"></script>
  <!-- endinject -->
</body>

</html>
