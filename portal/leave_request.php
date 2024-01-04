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
						<a class="btn-sm" href="leave.php">
							Back
							<i class="fas fa-solid fa-arrow-left ml-2 text-center"></i>
						</a>

					</div>
				</div>
				<form id="leave-form">
					<div class="row mb-3">
						<label for="start_date" class="col-sm-1 col-form-label">Start Date</label>
						<div class="col-sm-11">
							<input type="date" class="form-control" id="start_date" name="start_date" required>
						</div>
					</div>
					<div class="row mb-3">
						<label for="end_date" class="col-sm-1 col-form-label">End Date</label>
						<div class="col-sm-11">
							<input type="date" class="form-control" id="end_date" name="end_date" required>
						</div>
					</div>
					<div class="row mb-3">
						<label for="lastname" class="col-sm-1 col-form-label">Reason</label>
						<div class="col-sm-11">
							<textarea class="form-control" placeholder="Enter leave reason..." rows="10" id="reason"></textarea>

						</div>
					</div>
					<button type="submit" class="btn btn-sm btn-primary" id="submit">Submit Request</button>
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
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>

<script>
	$(function() {
		const userId = JSON.parse(localStorage.getItem("user"));


		const url = `./../php/controller/leaveController.php?id=${userId.user_id}`;


		$("#leave-form").on("submit", function(e) {
			e.preventDefault();

			//gather form data
			const formData = {
				start_date: $("#start_date").val(),
				end_date: $("#end_date").val(),
				reason: $("#reason").val(),
			};

			console.log(formData);

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


			if (data.success == true) {
				showToast("success", "Leave request sent!");
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
		// Error Handler
		function handleError(err) {
			console.log(err);
		}
	});
</script>

<?php
include(__DIR__ . "/partials/foot.php");
?>