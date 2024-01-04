<?php
include(__DIR__ . "/partials/head.php");
?>
<main class="vh-100 sm d-flex align-items-center">
	<div class="container" style="width: 500px;">
		<div id="image-section" class="mx-auto">
			<img src="assets/images/SwiftShift-Logo.jpg" style="width: 100%">
		</div>
		<div id="login-section">
			<form id="login-form">
				<!-- Email input -->
				<div class="form-outline mb-3">
					<input type="email" id="email" class="form-control" value="" />
					<label class="form-label" for="email">Email address</label>
				</div>

				<!-- Password input -->
				<div class="form-outline mb-2">
					<input type="password" id="password" class="form-control" value="" />
					<label class="form-label" for="password">Password</label>
					<div>
						<!-- Error message for invalid credentials -->
						<span id="error-message" class="text-xs text-danger"></span>
					</div>
				</div>

				<!-- Submit button -->
				<button type="submit" class="btn btn-primary btn-block">Sign in</button>
			</form>
		</div>

	</div>

</main>


<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<script>
	$(function() {
		$("#login-form").on("submit", function(e) {
			e.preventDefault();

			// Gather form data
			const formData = {
				email: $("#email").val().trim(),
				password: $("#password").val().trim(),
			};

			const url = "./php/controller/auth/loginController.php";
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
				localStorage.setItem("user", JSON.stringify(data.data));

				window.location.href = "index.php";
			} else if (data.success == false) {
				// Show error message and retain entered credentials
				$("#error-message").text("Invalid credentials. Please try again.");
				$("#email").val(formData.email);
				$("#password").val(formData.password);
			}
		}

		// Error Handler
		function handleError(err) {
			console.log(err);
		}
	});

	// Check if user is logged in
	function checkLoggedIn() {
		const userData = localStorage.getItem("user");
		console.log(userData);
		if (userData) {
			if (userData.role == 1) {
				// Redirect to login page if user data is not found
				window.location.href = "index.php";
			} else {
				window.location.href = "portal/index.php";
			}

		}
	}

	// Call the function on page load
	$(document).ready(function() {
		checkLoggedIn();
	});
</script>

<?php
include(__DIR__ . "/partials/foot.php");
?>