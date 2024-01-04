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
						<h1 class="m-0">Attendance</h1>
					</div><!-- /.col -->
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active">Attendance Management</li>
						</ol>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.container-fluid -->
		</div>
		<!-- /.content-header -->

		<!-- Main content -->
		<div class="content">
			<div class="container-fluid">

				<div class="table-responsive-sm rounded" style="height: 500px; overflow-y: auto;">
					<table class="table table-light rounded table-hover ">
						<caption>List of session</caption>
						<thead class="thead-dark">
							<tr>
								<th scope="col">Date</th>
								<th scope="col">Time In</th>
								<th scope="col">Time Out</th>
								<th scope="col">Status</th>
								<th scope="col">Action</th>
							</tr>
						</thead>
						<tbody id="table-body">
							<!-- table data -->
						</tbody>
					</table>
				</div>
			</div><!-- /.container-fluid -->
		</div>
		<!-- /.content -->
	</div>
	<!-- /.content-wrapper -->

</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>

<script>
	$(function() {
		// Initial load of table data
		refreshTable();

		// Function to refresh the table
		function refreshTable() {
			const username = JSON.parse(localStorage.user);

			const url = `./../php/controller/attendanceController.php`;

			$.ajax({
				type: "GET",
				url: url,
				success: function(res) {
					const data = JSON.parse(res);
					// Clear existing table rows
					$("#table-body").empty();

					if (data.success) {
						data.data.map((data) => {
							$("#table-body").append(generateRowMarkup(data));
						});

					} else {
						$("#table-body").append(`<tr>
							<td colspan = "6" class = "text-center">
								<p class = "card-text" > No sessions </p> 
							</td> 
						</tr>`);
					}
				},
				error: handleError
			});
		}

		const generateRowMarkup = (data) => {

			const sessionDate = formatDate(data.date);

			const timeIn = data.user_timeIn == null ? null : formatDateTime(data.user_timeIn);
			const timeOut = data.user_timeOut == null ? null : formatDateTime(data.user_timeOut);

			// Dynamically set status color
			const statusColor = data.status == 1 ? "text-success" : "text-danger";
			const statusText = data.status == 1 ? "Active" : "Inactive";
			const buttonDisable = data.status == 1 ? "" : "disabled";
			const areaDisabled = data.status == 1 ? false : true;

			return `<tr>
						<td class="align-middle">${sessionDate}</td>
						<td class="align-middle">${timeIn}</td>
						<td class="align-middle">${timeOut}</td>
						<td class="align-middle ${statusColor}">${statusText}</td>
						<td class="align-middle">
							<a href="attendance.php?id=${data.session_id}" class="btn btn-sm btn-success ${buttonDisable}" role="button" aria-disabled="${areaDisabled}">Attend</a>
						</td>
					</tr>`;
		};
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

		function convertTo12HourFormat(time24) {
			// Extract hours and minutes
			const [hours, minutes] = time24.split(':');

			// Convert to 12-hour format
			let hours12 = parseInt(hours, 10);
			const ampm = hours12 >= 12 ? 'pm' : 'am';

			hours12 = hours12 % 12 || 12; // Handle midnight (12:00) as 12:00 am

			// Pad single-digit hours and minutes with leading zeros
			const formattedHours = hours12.toString().padStart(2, '0');
			const formattedMinutes = minutes.padStart(2, '0');

			return `${formattedHours}:${formattedMinutes} ${ampm}`;
		}
	});
</script>


<?php
include(__DIR__ . "/partials/foot.php");
?>