<?php
require_once(__DIR__ . "/../util/header.php");
require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/Session.php");
require_once(__DIR__ . "/../model/Attendance.php");

use model\User;
use model\Session;
use model\Attendance;

//fetch request method
$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {

	case "POST": {
			if (isset($_POST["_method"])) {
				if ($_POST["_method"] === "PATCH") {
					updateProfile();
				}
			}
			break;
		}
	case "GET": {
			if (isset($_GET["id"]) || !empty($_GET["id"])) {
				fetchProfile();
			}
			break;
		}
	default: {
			$responseMessage = "Request method: {$requestMethod} not allowed!";
			response(false, ["message" => $responseMessage]);
			break;
		}
}

function fetchProfile()
{
	$user = fetchUserDetails();

	if (!$user) {
		response(false, ["message" => "Employee not found!"]);
		exit;
	}

	$sessionAttended = fetchAttendance();

	//if session is null, only return user info
	if (!$sessionAttended) {
		$user["isNull"] = true;
		response(true, $user);
	} else {
		$returnData = [];
		foreach ($sessionAttended as $session) {
			// dd($session);
			if ($session) {
				$sessionData = Session::find($session["session_id"], "id");

				$status = empty($session["time_out"]) ? "Incomplete" : "Done";

				$returnData[] = [
					"session_id" => $session["session_id"],
					"attendance_id" => $session["id"],
					"date" => $sessionData["date"],
					"time_in" => $session["time_in"],
					"time_out" => $session["time_out"],
					"status" => $status
				];
			}
		}
		$user["session_data"] = $returnData;

		response(true, $user);
	}
}
function fetchUserDetails()
{
	$id = $_GET["id"];

	$result = User::find($id, "id");

	if (!$result) {
		return null;
		exit;
	}

	return $result;
}

function fetchAttendance()
{
	$sessions = Session::read();

	if (!$sessions) {
		return false;
		exit;
	}

	foreach ($sessions as $session) {
		$attendance = Attendance::find($session["id"], "session_id");

		$results[] = $attendance;
	}

	return $results;
}

function updateProfile()
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
