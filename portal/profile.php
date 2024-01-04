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
		<div class="content p-4">
			<div class="row mb-2">
				<div class="col-lg-4 mb-sm-3 mb-lg-0">
					<div class="card mb-4 h-100">
						<div class="card-body text-center">
							<div class="mt-3 mb-4 d-flex justify-content-center align-items-center">
								<img src="./../assets/images/user-profile.png" alt="avatar" class="rounded-circle img-fluid" style="width: 90px;">
							</div>
							<h5 id="username" class="my-3"></h5>

							<p id="address" class="text-muted mb-1"></p>
						</div>
					</div>
				</div>
				<div class="col-lg-8">
					<div class="card mb-4 h-100">
						<div class="card-header d-flex align-items-center justify-content-end">
							<a href="edit-profile.php" class="text-secondary">
								Edit
								<i class="ml-2 fas fa-solid fa-pen"></i>
							</a>

						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-sm-3">
									<p class="mb-0">Full Name</p>
								</div>
								<div class="col">
									<p id="fullname" class="text-muted mb-0"></p>
								</div>
							</div>
							<hr>
							<div class="row">
								<div class="col-sm-3">
									<p class="mb-0">Email</p>
								</div>
								<div class="col-sm-9">
									<p id="email" class="text-muted mb-0"></p>
								</div>
							</div>
							<hr>
							<div class="row">
								<div class="col-sm-3">
									<p class="mb-0">Mobile</p>
								</div>
								<div class="col-sm-9">
									<p id="contact_number" class="text-muted mb-0"></p>
								</div>
							</div>
							<hr>
							<div class="row">
								<div class="col-sm-3">
									<p class="mb-0">Status</p>
								</div>
								<div class="col-sm-9">
									<p id="status" class="mb-0 text-capitalize text-danger"></p>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="d-flex justify-content-between">
						<h4>Sessions:</h4>
					</div>

					<div class="table-responsive-sm rounded" style="height: 200px; overflow-y: auto;">
						<table class="table table-hover table-light rounded">
							<thead class="thead-dark">
								<tr>
									<th scope="col">Date</th>
									<th scope="col">Time In</th>
									<th scope="col">Time Out</th>
									<th scope="col">Attendance Status</th>
								</tr>
							</thead>
							<tbody id="table-body">

							</tbody>
						</table>

					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->

</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="./../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="./../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="./../dist/js/adminlte.min.js"></script>
<script>
	$(function() {
		// Initial load of table data
		refreshTable();

		// Function to refresh the table
		function refreshTable() {
			const username = JSON.parse(localStorage.user);

			const url = `./../php/controller/userController.php?id=${username.user_id}`;

			$.ajax({
				type: "GET",
				url: url,
				success: function(res) {
					const data = JSON.parse(res);

					// Clear existing table rows
					$("#table-body").empty();

					if (data.success) {
						localStorage.setItem("user-profile", JSON.stringify(data.data));
						const fullname = data.firstname + " " + data.middlename.charAt(0) + ". " + data.lastname;

						const address =
							data.street +
							", " +
							data.barangay +
							", " +
							data.municipality +
							", " +
							data.province;

						$("#username").text(fullname);
						$("#address").text(address);
						$("#fullname").text(fullname);
						$("#email").text(data.email);
						$("#contact_number").text(data.contact_number);

						localStorage.setItem("employees", JSON.stringify(data.data));
						// console.log(data.session_data);
						if (data.isNull) {
							$("#table-body").append(`
								<tr>
									<td colspan="6" class="text-center">
										<p class="card-text">No sessions</p>
									</td>
								</tr>
							`);
						} else {
							if (!data.session_data.length) {
								$("#table-body").append(`
								<tr>
									<td colspan="6" class="text-center">
										<p class="card-text">No sessions</p>
									</td>
								</tr>
							`);
							} else {
								data.session_data.map((data) => {
									$("#table-body").append(generateRowMarkup(data));
								});
							}

						}
					}
				},
				error: handleError
			});
		}

		const generateRowMarkup = (data) => {

			const sessionDate = formatDate(data.date);

			const timeIn = formatDateTime(data.time_in);
			const timeOut = data.time_out == null ? null : formatDateTime(data.time_out);
			const statusColor = data.status == "Incomplete" ? "text-danger" : "text-success";
			console.log(data.status);
			return `<tr>
						<td class="align-middle">${sessionDate}</td>
						<td class="align-middle">${timeIn}</td>
						<td class="align-middle">${timeOut}</td>
						<td class="align-middle font-weight-bold ${statusColor}">${data.status}</td>
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
	});
</script>


<?php
include(__DIR__ . "/partials/foot.php");
?>