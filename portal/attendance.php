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
				<form id="add-session-form">
					<div class="row mb-3">
						<label for="date" class="col-sm-1 col-form-label">Date</label>
						<div class="col-sm-11">
							<input type="date" class="form-control" id="date" name="date" required>
						</div>
					</div>
					<div class="row mb-3">
						<label for="time_in" class="col-sm-1 col-form-label">Time in</label>
						<div class="col-sm-11">
							<input type="time" class="form-control" id="time_in" name="time_in" required>
						</div>
					</div>
					<div class="row mb-3">
						<label for="time_out" class="col-sm-1 col-form-label">Time out</label>
						<div class="col-sm-11">
							<input type="time" class="form-control" id="time_out" name="time_out" required>
						</div>
					</div>
					<button type="submit" class="btn btn-sm btn-primary" id="submit">Add Session</button>
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


<?php
include(__DIR__ . "/partials/foot.php");
?>