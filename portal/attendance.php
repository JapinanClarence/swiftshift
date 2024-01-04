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
						<h1 class="m-0">Attendance form</h1>
					</div><!-- /.col -->
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="index.php">Home</a></li>
							<li class="breadcrumb-item active">Attendance</li>
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
						<a class="btn-sm" href="index.php">
							Back
							<i class="fas fa-solid fa-arrow-left ml-2 text-center"></i>
						</a>

					</div>
				</div>
				<form id="attendance-form">
					<input type="hidden" id="timeout_status">

					<div class="row mb-3">
						<label for="date" class="col-sm-1 col-form-label">Date</label>
						<div class="col-sm-11">
							<input type="hidden" class="form-control" id="hidden_date" name="hidden_date">
							<input type="text" class="form-control" id="date" name="date" readonly>
						</div>
					</div>
					<div class="row mb-3">
						<label for="time_in" class="col-sm-1 col-form-label">Time in</label>
						<div class="col-sm-11">
							<input type="datetime-local" class="form-control" id="time_in" name="time_in" required>
						</div>
					</div>
					<div class="row mb-3">
						<label for="time_out" class="col-sm-1 col-form-label">Time out</label>
						<div class="col-sm-11">
							<input type="datetime-local" class="form-control" id="time_out" name="time_out" required>
						</div>
					</div>
					<button type="submit" class="btn btn-sm btn-primary" id="submit">Submit</button>
				</form>

			</div><!-- /.container-fluid -->
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
		// Function to get URL parameters
		function getUrlParameter(name) {
			name = name.replace(/[[]/, '\\[').replace(/[\]]/, '\\]');
			var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
			var results = regex.exec(location.search);
			return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
		}

		// Get the employee ID from the URL parameter
		const sessionId = getUrlParameter('id');

		const url = `./../php/controller/attendanceController.php?id=${sessionId}`;


		$.ajax({
			type: "GET",
			url: url,
			success: function(res) {
				const data = JSON.parse(res);
				const date = formatDate(data.data.date);
				const timeoutStatus = data.data.timeout_status;

				if (data.success) {
					if (timeoutStatus === 1) {
						// If timeout status is 1, disable time_in and enable time_out
						$("#time_in").prop("disabled", true);
						$('#time_out').prop('disabled', false);
					} else {
						// If timeout status is 0, disable time_out and enable time_in
						$('#time_out').prop('disabled', true);
						$("#time_in").prop("disabled", false);
					}

					$("#timeout_status").val(timeoutStatus);
					$("#hidden_date").val(data.data.date);
					$("#date").val(date);
				}
			},
			error: handleError,
		});


		$("#attendance-form").on("submit", function(e) {
			e.preventDefault();

			const userId = JSON.parse(localStorage.user);

			const timeout_status = $("#timeout_status").val();


			//gather form data
			let formtData;

			if (timeout_status == 1) {
				formData = {
					user_id: userId.user_id,
					session_id: sessionId,
					date: $("#hidden_date").val(),
					time_out: $("#time_out").val(),
					_method: "PATCH"
				};
			} else {
				formData = {
					user_id: userId.user_id,
					session_id: sessionId,
					date: $("#hidden_date").val(),
					time_in: $("#time_in").val(),
				};
			}
			console.log(formData);

			const url = "./../php/controller/attendanceController.php";

			$.ajax({
				type: "POST",
				url: url,
				data: formData,
				success: function(res) {
					const data = JSON.parse(res);
					console.log(data);
					if (data.success) {
						showToast("success", data.message);
					} else if (data.success == false) {
						showToast("error", data.message);
					}
				},
				error: handleError,
			});
		});

		// Your existing error handler
		function handleError(err) {
			console.log(err);
		}

		function formatDate(inputDate) {
			const date = new Date(inputDate);
			const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Month is zero-based
			const day = date.getDate().toString().padStart(2, '0');
			const year = date.getFullYear();

			return `${month}/${day}/${year}`;
		}

		function showToast(icon, title) {
			Toast.fire({
				icon: icon,
				title: title,
			});
		}
	})
</script>

<?php
include(__DIR__ . "/partials/foot.php");
?>