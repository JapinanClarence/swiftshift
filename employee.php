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
						<h1 class="m-0">Employees</h1>
					</div><!-- /.col -->
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active">Employee</li>
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
					<a class="btn-sm btn-primary" href="registration.php">
						Register Employee
					</a>
				</div>
				<div class="table-responsive-sm rounded" style="height: 500px; overflow-y: auto;">
					<table class="table table-light rounded table-hover ">
						<caption>List of employees</caption>
						<thead class="thead-dark">
							<tr>
								<th scope="col">Fullname</th>
								<th scope="col">Contact No.</th>
								<th scope="col">Email</th>
								<th scope="col">Address</th>
								<th scope="col">Registered At</th>
								<th scope="col">Action</th>
							</tr>
						</thead>
						<form id="delete-form">
							<tbody id="table-body">
								<!-- table data -->
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


<script>
	$(function() {
		const url = "./php/controller/admin/employeeController.php";

		$.ajax({
			type: "GET",
			url: url,
			success: function(res) {
				const data = JSON.parse(res);

				if (data.success) {
					localStorage.setItem("employees", JSON.stringify(data.data));
					data.data.map((data) => {
						$("#table-body").append(generateRowMarkup(data));
					});
				} else {
					$("#table-body").append(`
          <tr>
            <td colspan="6" class="text-center">
              <p class="card-text">No Registered Employees!</p>
            </td>
          </tr>
        `);
				}
			},
			error: handleError
		});

		const generateRowMarkup = (data) => {
			const fullname = data.firstname + " " + data.middlename.charAt(0) + ". " + data.lastname;

			const address =
				data.street +
				", " +
				data.barangay +
				", " +
				data.municipality +
				", " +
				data.province;
			const registeredDate = formatDateTime(data.created_at);

			return `<tr class="align-middle border-dark">
						<td>${fullname}</td>
						<td>${data.contact_number}</td>
						<td>${data.email}</td>
						<td>${address}</td>
						<td>${registeredDate}</td>
						<td class="d-flex align-items-center">
							<a href="edit_employee.php?id=${data.id}" class="mr-2">
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
					</tr>`;
		};

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
					const url = "./php/controller/admin/employeeController.php";
					const data = {
						_method: "DELETE",
						id: $(this).find('input[name="id"]').val().trim()
					};

					$.ajax({
						type: "POST",
						url: url,
						data: data,
						success: handleSuccess,
						error: handleError,
					});
				}
			});


		});

		// Success handler
		function handleSuccess(res) {
			const data = JSON.parse(res);
			console.log(data);

			if (data.success == true) {
				window.location.href = "employee.php";
				// 	setTimeout(() => {

				// }, 3000);

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
	});
</script>

<?php
include(__DIR__ . "/partials/foot.php");
?>