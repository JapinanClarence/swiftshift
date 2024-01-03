<?php
include(__DIR__ . "/partials/head.php");
?>
<div class="wrapper">
  <?php
  include(__DIR__ . "/partials/header.php");
  include(__DIR__ . "/partials/sidebar.php");
  ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

        <div class="d-flex justify-content-end mb-3">
          Date:<span id="active_date" class="ml-2">01/03/2024</span>
        </div>
        <div class="row">
          <div class="col-lg-3 col-md-3">
            <div class="d-flex flex-column">
              <div class="">
                <!-- small card -->
                <div class="small-box bg-info">
                  <div class="inner">
                    <h3 id="attendanceCount"></h3>

                    <p>Attendance Count</p>
                  </div>
                  <div class="icon">

                    <i class="fas fa-solid fa-chart-pie"></i>
                  </div>
                  <div class="small-box-footer" style="height: 20px;">
                    <!-- More info <i class="fas fa-arrow-circle-right"></i> -->
                  </div>
                </div>
              </div>
              <div class="">
                <!-- small card -->
                <div class="small-box bg-success">
                  <div class="inner">
                    <h3 id="employeesCount"></h3>

                    <p>Total Employees</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-solid fa-user-tie"></i>
                  </div>
                  <div class="small-box-footer" style="height: 20px;">
                    <!-- More info <i class="fas fa-arrow-circle-right"></i> -->
                  </div>
                </div>
              </div>
            </div>

          </div>
          <!-- /.col-md-6 -->
          <div class="col-lg-9 col-md-9">
            <div class="table-responsive-sm rounded" style="height: 500px; overflow-y: auto;">
              <table class="table table-hover table-light rounded">
                <thead class="thead-dark">
                  <tr>
                    <th scope="col">Fullname</th>
                    <th scope="col">Email</th>
                    <th scope="col">Contact No.</th>
                    <th scope="col">Time In</th>
                    <th scope="col">Time Out</th>
                    <th scope="col">Status</th>
                  </tr>
                </thead>
                <tbody id="table-body">

                </tbody>
              </table>

            </div>
          </div>
        </div>
        <!-- /.col-md-6 -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(function() {
    // Initial load of table data
    refreshTable();

    // Function to refresh the table
    function refreshTable() {
      const url = "./php/controller/admin/dashboardController.php";

      $.ajax({
        type: "GET",
        url: url,
        success: function(res) {
          const data = JSON.parse(res);

          // Clear existing table rows
          $("#table-body").empty();

          if (data.success) {

            let currentDate = data.current_date == null ? "Unset" : formatDate(data.current_date);

            if (!data.current_date) {
              Swal.fire({
                text: "No active sessions, all attendance record will be shown.",
                icon: "info",
              })
            }

            $("#active_date").text(currentDate);
            $("#attendanceCount").text(data.attendees_count);
            $("#employeesCount").text(data.employees_count);
            if (data.isNull) {
              $("#table-body").append(`
                        <tr>
                            <td colspan="6" class="text-center">
                                <p class="card-text">No employees attended</p>
                            </td>
                        </tr>
                    `);
            } else {
              localStorage.setItem("attendance", JSON.stringify(data.data));
              data.data.map((data) => {
                $("#table-body").append(generateRowMarkup(data));
              });
            }
          } else {
            showToast("error", "An error occured");
          }
        },
        error: handleError
      });
    }

    const generateRowMarkup = (data) => {

      const registeredDate = formatDateTime(data.created_at);
      const timeIn = formatDateTime(data.time_in);
      const timeOut = data.time_out == null ? "" : formatDateTime(data.time_out);

      const attendanceStatus = data.time_out == null ? "Incomplete" : "Complete";
      const statusColor = data.time_out == null ? "text-danger" : "text-success";

      return `<tr>
                    <td class="align-middle">${data.employee_name}</td>
                    <td class="align-middle">${data.email}</td>
                    <td class="align-middle">${data.contact_number}</td>
                    <td class="align-middle">${timeIn}</td>
                    <td class="align-middle">${timeOut}</td>
                    <td class="align-middle font-weight-bold ${statusColor}">${attendanceStatus}</td>
                </tr>`;
    };

    // Success handler
    function handleSuccess(res) {
      const data = JSON.parse(res);

      if (data.success == true) {
        showToast("success", data.message);
      } else if (data.success == false) {
        showToast("error", data.message);
      }
    }

    // Your existing error handler
    function handleError(err) {
      console.log(err);
    }

    function showToast(icon, title) {
      Toast.fire({
        icon: icon,
        title: title,
      });
    }

    //format date time
    function formatDateTime(inputDateTime) {
      // Parse the input datetime string
      const parsedDateTime = new Date(inputDateTime);

      // Extract components of the date and time
      const year = parsedDateTime.getFullYear();
      const month = (parsedDateTime.getMonth() + 1).toString().padStart(2, "0"); // Month is zero-indexed, so add 1
      const day = parsedDateTime.getDate().toString().padStart(2, "0");
      const hours = parsedDateTime.getHours().toString().padStart(2, "0");
      const minutes = parsedDateTime.getMinutes().toString().padStart(2, "0");
      const ampm = parsedDateTime.getHours() >= 12 ? "pm" : "am";

      // Adjust hours for 12-hour format
      const formattedHours = parsedDateTime.getHours() % 12 || 12;

      // Construct the formatted datetime string
      const formattedDateTime = `${month}/${day}/${year} ${formattedHours}:${minutes}${ampm}`;

      return formattedDateTime;
    }

    function formatDate(inputDate) {
      const date = new Date(inputDate);
      const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Month is zero-based
      const day = date.getDate().toString().padStart(2, '0');
      const year = date.getFullYear();

      return `${month}/${day}/${year}`;
    }
  });
</script>


<?php
include(__DIR__ . "/partials/foot.php");
?>