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
						<h1 class="m-0">Leave Request</h1>
					</div><!-- /.col -->
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Home</a></li>
							<li class="breadcrumb-item active">Manage Request</li>
						</ol>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.container-fluid -->
		</div>
		<!-- /.content-header -->

		<!-- Main content -->
		<div class="content">
			<div class="container-fluid">
				<div class="px-2">
					<div class="d-flex justify-content-end mb-3">
						<a class="btn-sm" href="leave.php">
							Back
							<i class="fas fa-solid fa-arrow-left ml-2 text-center"></i>
						</a>
					</div>
				</div>
				<div class="mx-10 container">
					<div class="row">
						<div class="col-md-3">
							<div class="card  h-100">
								<div class="card-body text-center">
									<div class="mt-3 mb-4 d-flex justify-content-center align-items-center">
										<img src="./assets/images/user-profile.png" alt="avatar" class="rounded-circle img-fluid" style="width: 90px;">
									</div>
									<h5 id="username" class="my-3">Clarence Japinan</h5>
									<p class="text-muted mb-1">japinanclarence@gmail.com</p>
									<p id="address" class="text-muted mb-1 font-weight-bold">Davao Oriental</p>
								</div>
							</div>
						</div>
						<div class=col-md-9>
							<div class="card card-primary card-outline h-100">
								<div class="card-body box-profile">
									<input type="hidden" id="leave_id">
									<h3 class="profile-username text-center mb-3">Leave Request</h3>

									<ul class="list-group list-group-unbordered mb-3">
										<li class="list-group-item">
											<b>Date Requested</b> <a id="date_requested" class="float-right"></a>
										</li>
										<li class="list-group-item">
											<b>Start Date</b> <a id="start_date" class="float-right"></a>
										</li>
										<li class="list-group-item">
											<b>End Date</b> <a id="end_date" class="float-right"></a>
										</li>
										<li class="list-group-item">
											<b>Reason</b> <a id="reason" class="float-right"></a>
										</li>

									</ul>

									<button type="submit" id="approve_response" class="btn btn-success btn-block" value="1"><b>Approve</b></button>
									<button type="submit" id="deny_response" class="btn btn-danger btn-block" value="0"><b>Deny</b></button>
								</div>

							</div>
						</div>
					</div>

				</div>

			</div><!-- /.container-fluid -->
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
			// Function to get URL parameters
			function getUrlParameter(name) {
				name = name.replace(/[[]/, '\\[').replace(/[\]]/, '\\]');
				var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
				var results = regex.exec(location.search);
				return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
			}

			// Get the employee ID from the URL parameter
			const leaveId = getUrlParameter('id');

			// Check if the leaveId is valid before making the AJAX request
			if (leaveId) {
				const url = `./php/controller/admin/leaveController.php?id=${leaveId}`;

				// Make an AJAX request to get employee information
				$.ajax({
					type: "GET",
					url: url,
					success: function(res) {
						const data = JSON.parse(res);

						if (data.success) {

							const start_date = formatDate(data.start_date);
							const end_date = formatDate(data.end_date);
							const requestDate = formatDateTime(data.created_at);

							$("#start_date").text(start_date);
							$("#end_date").text(end_date);
							$("#date_requested").text(requestDate);
							$("#reason").text(data.reason);
							$("#leave_id").val(data.id);
						} else {
							showToast("error", data.message);
							window.location.href = "leave.php";
						}
					},
					error: handleError
				});
			} else {
				showToast("error", "Leave ID not found in the URL");
				window.location.href = "leave.php";
			}


			$("#approve_response").on("click", function(e) {
				e.preventDefault();
				// const leave_response = approve == 1 ? approve : deny;
				// console.log(leave_response)
				//gather form data
				const formData = {
					id: $("#leave_id").val(),
					action: "approve",
					response: $("#approve_response").val(),
				};
				console.log(formData);

				const url = "./php/controller/admin/leaveController.php";
				$.ajax({
					type: "POST",
					url: url,
					data: formData,
					success: handleSuccess,
					error: handleError,
				});
			});

			$("#deny_response").on("click", function(e) {
				e.preventDefault();
				// const leave_response = approve == 1 ? approve : deny;
				// console.log(leave_response)
				//gather form data
				const formData = {
					id: $("#leave_id").val(),
					action: "deny",
					response: $("#deny_response").val(),
				};
				console.log(formData);

				const url = "./php/controller/admin/leaveController.php";
				$.ajax({
					type: "POST",
					url: url,
					data: formData,
					success: handleSuccess,
					error: handleError,
				});
			});
			// Success handler
			function handleSuccess(res) {
				const data = JSON.parse(res);
				console.log(data);

				if (data.success == true) {
					showToast("success", "Response submitted!");
				} else if (data.success == false) {
					showToast("error", data.message);
				}
			}
			// Error Handler
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