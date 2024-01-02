<?php
require_once(__DIR__ . "/../../util/header.php");
require_once(__DIR__ . "/../../model/User.php");

use model\User;
use Ulid\Ulid;

//fetch request method
$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {

	case "POST": {
			registration();
			break;
		}
	default: {
			$responseMessage = "Request method: {$requestMethod} not allowed!";
			response(false, ["message" => $responseMessage]);
			break;
		}
}

function registration()
{
	$firstname = $_POST["firstname"];
	$middlename = $_POST["middlename"];
	$lastname = $_POST["lastname"];
	$contactNumber = $_POST["contactNumber"];
	$email = $_POST["email"];
	$street = $_POST["street"];
	$barangay = $_POST["barangay"];
	$municipality = $_POST["municipality"];
	$province = $_POST["province"];

	if (User::find($email, "email")) {
		response(false, ["message" => "Email already taken!"]);
		exit;
	}

	//generate id
	$id = Ulid::generate(true);

	//default password
	$password = password_hash("12345678", PASSWORD_DEFAULT);

	$result = User::create([
		"id" => $id,
		"firstname" => $firstname,
		"middlename" => $middlename,
		"lastname" => $lastname,
		"contact_number" => $contactNumber,
		"email" => $email,
		"password" => $password,
		"street" => $street,
		"barangay" => $barangay,
		"municipality" => $municipality,
		"province" => $province
	]);

	if (!$result) {
		response(false, ["message" => "Failed to register employee!"]);
		exit;
	}

	response(true, ["message" => "Employee registered successfully!"]);
}
