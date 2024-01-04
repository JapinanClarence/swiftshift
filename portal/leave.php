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
						<h1 class="m-0">Leave Form</h1>
					</div><!-- /.col -->
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active">Edit Profile</li>
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
						<a class="btn-sm" href="leave_request.php">
							Request Leave
							<i class="fas fa-solid fa-plus ml-2 text-center"></i>
						</a>

					</div>
				</div>
				<div class="table-responsive-sm rounded" style="height: 500px; overflow-y: auto;">
					<table class="table table-light rounded table-hover ">
						<caption>Leave requests</caption>
						<thead class="thead-dark">
							<tr>
								<th scope="col">Start Date</th>
								<th scope="col">End Date</th>
								<th scope="col">Reason</th>
								<th scope="col">Status</th>
								<th scope="col">Date Requested</th>
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
		const userId = JSON.parse(localStorage.getItem("user"));

		function populateTable(data) {
			$("#table-body").empty();

			if (data.success) {
				localStorage.setItem("leave_requests", JSON.stringify(data.data));

				data.data.map((data) => {
					$("#table-body").append(generateRowMarkup(data));
				});
			} else {
				$("#table-body").append(`
			<tr>
				<td colspan="6" class="text-center">
					<p class="card-text">No Requests!</p>
				</td>
			</tr>
		`);
			}
		}

		function refreshTable() {
			const url = `./../php/controller/leaveController.php?id=${userId.user_id}`;

			$.ajax({
				type: "GET",
				url: url,
				success: function(res) {
					const data = JSON.parse(res);
					populateTable(data);
				},
				error: handleError,
			});
		}

		const generateRowMarkup = (data) => {
			const start_date = formatDate(data.start_date);
			const end_date = formatDate(data.end_date);
			const requestDate = formatDateTime(data.created_at);
			// Dynamically set status color
			let statusColor, statusText;

			// console.log(data.id);
			if (data.status == 1) {
				statusColor = "text-success";
				statusText = "Approved";
			} else if (data.status == 2) {
				statusColor = "text-secondary";
				statusText = "Pending";
			} else if (data.status == 0) {
				statusColor = "text-danger";
				statusText = "Denied";
			} else {
				// Handle other status values if needed
				statusColor = "text-muted"; // Default color for unknown status
				statusText = "unknown";
			}

			return `<tr>
			<td class="align-middle">${start_date}</td>
			<td class="align-middle">${end_date}</td>
			<td class="align-middle">${data.reason}</td>
			<td class="align-middle ${statusColor}">${statusText}</td>
			<td class="align-middle">${requestDate}</td>
			<td class="align-middle d-flex align-items-center">
				<form class="delete-form">
					<input type="hidden" name="_method" value="DELETE">
					<input type="hidden" name="id" value="${data.id}">
					<button class="btn delete-btn" type="submit">
						<i class="fas fa-trash text-danger"></i>
					</button>
				</form>
			</td>
		</tr>`;
		};

		// AJAX request to populate the table initially
		refreshTable();

		$(document).on("submit", ".delete-form", function(e) {
			e.preventDefault();

			// Add a confirmation before deleting
			Swal.fire({
				title: "Are you sure?",
				text: "You won't be able to revert this!",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Yes, delete it!"
			}).then((result) => {
				if (result.isConfirmed) {
					const url = "./../php/controller/leaveController.php";
					const data = {
						_method: "DELETE",
						id: $(this).find('input[name="id"]').val().trim()
					};

					$.ajax({
						type: "POST",
						url: url,
						data: data,
						success: function(res) {
							const data = JSON.parse(res);

							if (data.success == true) {
								// Refresh the table after successful delete
								refreshTable();
							} else if (data.success == false) {
								showToast("error", data.message);
							}
						},
						error: handleError,
					});
				}
			});
		});

		// Success handler
		function handleSuccess(res) {
			const data = JSON.parse(res);

			if (data.success == true) {
				// Refresh the table after successful operation
				refreshTable();
			} else if (data.success == false) {
				showToast("error", data.message);
			}
		}

		// Your existing error handler
		function handleError(err) {
			console.log(err);
		}

		// Your existing showToast function
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