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
						<h1 class="m-0">User Profile</h1>
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
						<a class="btn-sm" href="profile.php">
							Back
							<i class="fas fa-solid fa-arrow-left ml-2 text-center"></i>
						</a>

					</div>
				</div>
				<form id="edit-form">
					<input type="hidden" id="_method" value="PATCH">
					<input type="hidden" id="id">
					<div class="row mb-3">
						<label for="firstname" class="col-sm-1 col-form-label">Firstname</label>
						<div class="col-sm-11">
							<input type="text" class="form-control" id="firstname" name="firstname" required>
						</div>
					</div>
					<div class="row mb-3">
						<label for="middlename" class="col-sm-1 col-form-label">Middlname</label>
						<div class="col-sm-11">
							<input type="text" class="form-control" id="middlename" name="middlename" required>
						</div>
					</div>
					<div class="row mb-3">
						<label for="lastname" class="col-sm-1 col-form-label">Lastname</label>
						<div class="col-sm-11">
							<input type="text" class="form-control" id="lastname" name="lastname" required>
						</div>
					</div>
					<div class="row mb-3">
						<label for="contactNumber" class="col-sm-1 col-form-label">Contact No.</label>
						<div class="col-sm-11">
							<input type="text" class="form-control" id="contactNumber" name="contactNumber" required>
						</div>
					</div>
					<div class="row mb-3">
						<label for="email" class="col-sm-1 col-form-label">Email</label>
						<div class="col-sm-11">
							<input type="email" class="form-control" id="email" name="email" required>
						</div>
					</div>
					<div class="row mb-3">
						<label for="street" class="col-sm-1 col-form-label">Street</label>
						<div class="col-sm-11">
							<input type="text" class="form-control" id="street" name="street" required>
						</div>
					</div>
					<div class="row mb-3">
						<label for="barangay" class="col-sm-1 col-form-label">Barangay</label>
						<div class="col-sm-11">
							<input type="text" class="form-control" id="barangay" required>
						</div>
					</div>
					<div class="row mb-3">
						<label for="municipality" class="col-sm-1 col-form-label">Municipality</label>
						<div class="col-sm-11">
							<input type="text" class="form-control" id="municipality" required>
						</div>
					</div>
					<div class="row mb-3">
						<label for="province" class="col-sm-1 col-form-label">Province</label>
						<div class="col-sm-11">
							<input type="text" class="form-control" id="province" required>
						</div>
					</div>
					<button type="submit" class="btn btn-sm btn-primary" id="submit">Update Profile</button>
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


		const url = `./../php/controller/userController.php?id=${userId.user_id}`;

		// Make an AJAX request to get employee information
		$.ajax({
			type: "GET",
			url: url,
			success: function(res) {
				const data = JSON.parse(res);

				if (data.success) {

					// Fill the form fields with the existing information
					$("#id").val(data.id);
					$("#firstname").val(data.firstname);
					$("#middlename").val(data.middlename);
					$("#lastname").val(data.lastname);
					$("#contactNumber").val(data.contact_number);
					$("#email").val(data.email);
					$("#street").val(data.street);
					$("#barangay").val(data.barangay);
					$("#municipality").val(data.municipality);
					$("#province").val(data.province);
				} else {
					showToast("error", data.message);
					window.location.href = "employee.php";
				}
			},
			error: handleError
		});



		$("#edit-form").on("submit", function(e) {
			e.preventDefault();

			//gather form data
			const formData = {
				_method: $("#_method").val().trim(),
				id: $("#id").val().trim(),
				firstname: $("#firstname").val().trim(),
				lastname: $("#lastname").val().trim(),
				middlename: $("#middlename").val().trim(),
				contactNumber: $("#contactNumber").val().trim(),
				email: $("#email").val().trim(),
				street: $("#street").val().trim(),
				barangay: $("#barangay").val().trim(),
				municipality: $("#municipality").val().trim(),
				province: $("#province").val().trim(),
			};

			console.log(formData);
			const url = "./../php/controller/userController.php";
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
				showToast("success", "Employee updated successfully!");
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