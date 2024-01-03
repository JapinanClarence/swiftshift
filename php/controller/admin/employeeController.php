<?php
require_once(__DIR__ . "/../../util/header.php");
require_once(__DIR__ . "/../../model/User.php");

use model\User;
use Ulid\Ulid;

//fetch request method
$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {

	case "POST": {
			if (isset($_POST["_method"])) {
				if ($_POST["_method"] === "DELETE") {
					deleteEmployee();
				} else if ($_POST["_method"] === "PATCH") {
					updateEmployee();
				}
			}
			break;
		}
	case "GET": {
			if (isset($_GET["id"]) || !empty($_GET["id"])) {
				fetchSingleEmployee();
			} else {
				fetchAllEmployee();
			}
			break;
		}
	default: {
			$responseMessage = "Request method: {$requestMethod} not allowed!";
			response(false, ["message" => $responseMessage]);
			break;
		}
}
function fetchAllEmployee()
{
	$results = User::find(EMPLOYEE_ROLE, "role", true);

	if (!$results) {
		response(false, ["message" => "No employees found!"]);
		exit;
	}
	response(true, ["data" => $results]);
}
function fetchSingleEmployee()
{
	$id = $_GET["id"];

	$result = User::find($id, "id");

	if (!$result) {
		response(false, ["message" => "Employee not found!"]);
		exit;
	}
	response(true, ["data" => $result]);
}
function deleteEmployee()
{
	$employeeId = $_POST["id"];

	if (!User::find($employeeId, "id")) {
		response(false, ["message" => "Employee not found!"]);
		exit;
	}

	if (!User::delete($employeeId)) {
		response(false, ["message" => "Failed to delete employee!"]);
		exit;
	}
	response(true, ["message" => "Employee deleted succesfully!"]);
}
function updateEmployee()
{
	$employeeId = $_POST["id"];
	$firstname = $_POST["firstname"];
	$middlename = $_POST["middlename"];
	$lastname = $_POST["lastname"];
	$contactNumber = $_POST["contactNumber"];
	$email = $_POST["email"];
	$street = $_POST["street"];
	$barangay = $_POST["barangay"];
	$municipality = $_POST["municipality"];
	$province = $_POST["province"];

	if (!User::find($employeeId, "id")) {
		response(false, ["message" => "Employee not found!"]);
		exit;
	}

	$result = User::update(
		$employeeId,
		[
			"firstname" => $firstname,
			"middlename" => $middlename,
			"lastname" => $lastname,
			"contact_number" => $contactNumber,
			"email" => $email,
			"street" => $street,
			"barangay" => $barangay,
			"municipality" => $municipality,
			"province" => $province
		]
	);

	if (!$result) {
		response(false, ["message" => "Failed to update employee!"]);
		exit;
	}
	response(true, ["message" => "Employee updated succesfully!"]);
}
