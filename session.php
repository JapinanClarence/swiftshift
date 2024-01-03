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
						<h1 class="m-0">Session</h1>
					</div><!-- /.col -->
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active">Session Management</li>
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
					<a class="btn-sm btn-primary" href="#">
						Create Session
					</a>
				</div>
				<div class="table-responsive-sm rounded" style="height: 500px; overflow-y: auto;">
					<table class="table table-light rounded table-hover ">
						<caption>List of session</caption>
						<thead class="thead-dark">
							<tr>
								<th scope="col">Session No.</th>
								<th scope="col">Date</th>
								<th scope="col">Time In/Out</th>
								<th scope="col">Status</th>
								<th scope="col">Created At</th>
								<th scope="col">Action</th>
							</tr>
						</thead>
						<form id="delete-form">
							<tbody id="table-body">
								<!-- table data -->
								<tr>
									<td>01</td>
									<td>01/03/2023</td>
									<td>08:00am/04:00pm</td>
									<td class="text-success font-weight-bold">Active</td>
									<td>01/03/2023</td>
									<td class="d-flex align-items-center">
										<a href="#" class="mr-2">
											<i class="fas fa-edit"></i>
										</a>
										<form class="delete-form">
											<input type="hidden" name="_method" value="DELETE">
											<input type="hidden" name="id" value="${data.id}">
											<button class="btn delete-btn" type="submit">
												<i class="fas fa-trash text-danger"></i>
											</button>
										</form>
									</td>
								</tr>
							</tbody>
						</form>
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
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<?php
include(__DIR__ . "/partials/foot.php");
?>