<?php
require_once(__DIR__ . "/../util/header.php");
require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/Session.php");
require_once(__DIR__ . "/../model/Attendance.php");

use model\Session;
use Ulid\Ulid;
use model\Attendance;

//fetch request method
$requestMethod = $_SERVER["REQUEST_METHOD"];
switch ($requestMethod) {

	case "POST": {

			if (isset($_POST["_method"]) && $_POST["_method"] === "PATCH") {
				sessionTimeOut();
			} else {
				sessionTimeIn();
			}

			break;
		}
	case "GET": {
			if (isset($_GET["id"]) || !empty($_GET["id"])) {
				fetchSessionData();
			} else {
				fetchSessions();
			}

			break;
		}
	default: {
			$responseMessage = "Request method: {$requestMethod} not allowed!";
			response(false, ["message" => $responseMessage]);
			break;
		}
}
function fetchSessionData()
{
	$sessionId = $_GET["id"];

	$session = Session::find($sessionId, "id");

	if (!$session) {
		response(false, ["message" => "Session not found!"]);
		exit;
	}
	$attendance = Attendance::find($sessionId, "session_id");

	$status = $attendance == null ? 0 : 1;

	$timeIn = $attendance === null ? null :  $attendance["time_in"];
	$timeOut = $attendance === null ? null :  $attendance["time_out"];

	$returnData = [
		"session_id" => $sessionId,
		"date" => $session["date"],
		"time_in" => $timeIn,
		"time_out" => $timeOut,
		"timeout_status" => $status
	];
	response(true, ["data" => $returnData]);
}
function fetchSessions()
{

	$sessions = Session::read();

	if (!$sessions) {
		response(false, ["message" => "No sessions found!"]);
		exit;
	}

	$returnData = [];

	foreach ($sessions as $session) {
		// dd($session);
		$attendance = Attendance::find($session["id"], "session_id");

		if (!$attendance) {
			$returnData[] = [
				"session_id" => $session["id"],
				"date" => $session["date"],
				"status" => $session["status"],
				"user_timeIn" => null,
				"user_timeOut" => null,
			];
		} else {
			$returnData[] = [
				"session_id" => $session["id"],
				"attendance_id" => $attendance["id"],
				"date" => $session["date"],
				"status" => $session["status"],
				"user_timeIn" => $attendance["time_in"],
				"user_timeOut" => $attendance["time_out"],
			];
		}
	}

	response(true, ["data" => $returnData]);
}
function sessionTimeIn()
{
	$userId = $_POST["user_id"];
	$sessionId = $_POST["session_id"];
	$timeIn = $_POST["time_in"];

	$session = Session::find($sessionId, "id");

	if (!$session) {
		response(false, ["message" => "Session not found"]);
		exit;
	}
	$sessionStatus = intval($session["status"]);

	if ($sessionStatus !== ACTIVE_SESSION) {
		response(false, ["message" => "Session inactive, time in attempt failed"]);
		exit;
	}


	// Create a DateTime object from the input string
	$dateTime = new DateTime($timeIn);

	// Format the DateTime object in the desired format
	$formattedDateTime = $dateTime->format('Y-m-d H:i:s');

	//generate id
	$id = Ulid::generate(true);

	$result = Attendance::create([
		"id" => $id,
		"session_id" => $sessionId,
		"time_in" => $formattedDateTime,
		"employee_id" => $userId
	]);

	if (!$result) {
		response(false, ["message" => "Failed to time in!"]);
		exit;
	}

	response(true, ["message" => "Time in succesfull!"]);
}

function sessionTimeOut()
{
	$sessionId = $_POST["session_id"];
	$timeOut = $_POST["time_out"];

	$session = Session::find($sessionId, "id");

	if (!$session) {
		response(false, ["message" => "Session not found!"]);
		exit;
	}

	$sessionStatus = intval($session["status"]);

	if ($sessionStatus !== ACTIVE_SESSION) {
		response(false, ["message" => "Session inactive, time in attempt failed"]);
		exit;
	}

	$attendance = Attendance::find($sessionId, "session_id");

	// Create a DateTime object from the input string
	$dateTime = new DateTime($timeOut);

	// Format the DateTime object in the desired format
	$formattedDateTime = $dateTime->format('Y-m-d H:i:s');

	$result = Attendance::update(
		$attendance["id"],
		[
			"time_out" => $formattedDateTime
		]
	);

	if (!$result) {
		response(false, ["message" => "Timeout failed!"]);
		exit;
	}
	response(true, ["message" => "Timeout succesfull!"]);
}
