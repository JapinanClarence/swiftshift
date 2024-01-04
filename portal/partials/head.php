<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SwiftShift</title>
	<link rel="shortcut icon" href="./../assets/images/SwiftShift-Logo.jpg" type="image/x-icon">

	<!-- css -->
	<link rel="stylesheet" href="./../assets/css/style.css">
	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome Icons -->
	<link rel="stylesheet" href="./../plugins/fontawesome-free/css/all.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="./../dist/css/adminlte.min.css">
	<!-- Sweet alert -->
	<script src="./../plugins/sweetalert2/sweetalert2.all.js"></script>
	<link rel="./../stylesheet" href="plugins/sweetalert2/sweetalert2.min.css">
	<script>
		const Toast = Swal.mixin({
			toast: true,
			position: 'bottom-start',
			showConfirmButton: false,
			timer: 3000,
			timerProgressBar: true,
			didOpen: (toast) => {
				toast.addEventListener('mouseenter', Swal.stopTimer)
				toast.addEventListener('mouseleave', Swal.resumeTimer)
			}
		})
	</script>
</head>

<body class="hold-transition sidebar-mini">