<?php
require_once(__DIR__ . "/../../util/header.php");
require_once(__DIR__ . "/../../model/Session.php");
require_once(__DIR__ . "/../../model/Attendance.php");
require_once(__DIR__ . "/../../model/User.php");

use model\Attendance;
use model\Session;
use model\User;
use Ulid\Ulid;

//fetch request method
$requestMethod = $_SERVER["REQUEST_METHOD"];
switch ($requestMethod) {

	case "GET": {
			fetchAttendees();
			break;
		}
	default: {
			$responseMessage = "Request method: {$requestMethod} not allowed!";
			response(false, ["message" => $responseMessage]);
			break;
		}
}

function fetchAttendees()
{
	$activeSession = fetchActiveSession();

	//fetch all attendance
	if (!$activeSession) {
		$results = Attendance::read();

		if (!$results) {
			response(
				true,
				[
					"isNull" => true,
					"message" => "No employees attended",
					"current_date" => null,
					"employees_count" => count(fetchEmployees())
				]
			);
			exit;
		}

		$attendanceCount = count($results);

		foreach ($results as $result) {
			$userDetail = User::find($result["employee_id"], "id");

			$middlename = substr($userDetail["middlename"], 0, 1) . ".";
			$fullname = $userDetail["firstname"] . " " . $middlename . " " . $userDetail["lastname"];

			$returnData[] = [
				"id" => $result["id"],
				"session_id" => $result["session_id"],
				"employee_id" =>	$result["employee_id"],
				"employee_name" => $fullname,
				"email" => $userDetail["email"],
				"contact_number" => $userDetail["contact_number"],
				"time_in" => $result["time_in"],
				"time_out" => $result["time_out"]
			];
		}

		response(true, [
			"current_date" => null,
			"attendees_count" => $attendanceCount,
			"employees_count" => count(fetchEmployees()),
			"data" => $returnData
		]);
	} else {
		$results = Attendance::find($activeSession["id"], "session_id", true);

		if (!$results) {
			response(
				true,
				[
					"isNull" => true,
					"message" => "No employees attended",
					"current_date" => $activeSession["date"],
					"employees_count" => count(fetchEmployees())
				]
			);
			exit;
		}

		$attendanceCount = count($results);

		foreach ($results as $result) {
			$userDetail = User::find($result["employee_id"], "id");

			$middlename = substr($userDetail["middlename"], 0, 1) . ".";
			$fullname = $userDetail["firstname"] . " " . $middlename . " " . $userDetail["lastname"];

			$returnData[] = [
				"id" => $result["id"],
				"session_id" => $result["session_id"],
				"employee_id" =>	$result["employee_id"],
				"employee_name" => $fullname,
				"email" => $userDetail["email"],
				"contact_number" => $userDetail["contact_number"],
				"time_in" => $result["time_in"],
				"time_out" => $result["time_out"]
			];
		}

		response(true, [
			"current_date" => $activeSession["date"],
			"attendees_count" => $attendanceCount,
			"employees_count" => count(fetchEmployees()),
			"data" => $returnData
		]);
	}
}
function fetchActiveSession()
{
	$results = Session::find(ACTIVE_SESSION, "status");

	if (!$results) {
		return null;
		exit;
	}

	return $results;
}
function fetchEmployees()
{
	$results = User::find(EMPLOYEE_ROLE, "role", true);

	if (!$results) {
		return null;
		exit;
	}

	return $results;
}
