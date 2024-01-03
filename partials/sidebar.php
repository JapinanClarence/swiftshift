<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
	<!-- Brand Logo -->
	<a href="#" class="brand-link">
		<img src="assets/images/SwiftShift-Logo.jpg" alt="SwiftShift Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
		<span class="brand-text font-weight-light">SwiftShift</span>
	</a>

	<!-- Sidebar -->
	<div class="sidebar">
		<!-- Sidebar user panel (optional) -->
		<div class="user-panel mt-3 pb-3 mb-3 d-flex">
			<div class="image">
				<img src="assets/images/user-profile.png" class="img-circle elevation-2" alt="User Image">
			</div>
			<div class="info">
				<a id="user-profile" href="#" class="d-block">Admin</a>
			</div>
		</div>
		<!-- Sidebar Menu -->
		<nav class="mt-2">
			<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
				<li class="nav-item">
					<a href="index.php" class="nav-link">
						<i class="nav-icon fas fa-tachometer-alt"></i>
						<p>
							Dashboard
							<!-- <i class="right fas fa-angle-left"></i> -->
						</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="employee.php" class="nav-link">
						<i class="nav-icon fas fa-solid fa-user"></i>
						<p>
							Employee Management
						</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="session.php" class="nav-link">
						<i class="nav-icon fas fa-solid fa-clipboard"></i>
						<p>
							Session Management
						</p>
					</a>
				</li>
			</ul>
		</nav>

	</div>
	<div class="border-top border-secondary" style="position: fixed; bottom:0px;">
		<a href="#" class="nav-link text-light logout-link">
			<i class="nav-icon fas fa-sign-out-alt "></i>
			Logout
		</a>
	</div>
	<!-- /.sidebar -->
</aside>

<script src="plugins/jquery/jquery.min.js"></script>
<script>
	$(document).ready(function() {
		// Get the current page URL
		var currentUrl = window.location.href;

		// Loop through each navigation link
		$('.nav-link').each(function() {
			// Get the href attribute of the link
			var linkUrl = $(this).attr('href');

			// Check if the current URL contains the link URL
			if (currentUrl.includes(linkUrl)) {
				// Add the 'active' class to the current navigation link
				$(this).addClass('active');
			}
		});

		// Logout function
		$(".logout-link").on("click", function(e) {
			e.preventDefault();

			// Clear localStorage
			localStorage.removeItem("user");

			// Redirect to the login page
			window.location.href = "login.php";
		});

		const username = JSON.parse(localStorage.user);
		$("#user-profile").text(username.username);

	});
</script>