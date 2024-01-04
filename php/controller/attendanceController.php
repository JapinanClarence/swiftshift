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
			sessionTimeIn();
			break;
		}
	case "GET": {
			fetchSessions();
			break;
		}
	default: {
			$responseMessage = "Request method: {$requestMethod} not allowed!";
			response(false, ["message" => $responseMessage]);
			break;
		}
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

	$session = Session::find($sessionId, "session_id");

	if (!$session) {
		response(false, ["message" => "Session not found"]);
		exit;
	}

	if ($session["status"] !== ACTIVE_SESSION) {
		response(false, ["message" => "Session inactive, time in attempt failed"]);
		exit;
	}

	//generate id
	$id = Ulid::generate(true);

	$result = Attendance::create([
		"id" => $id,
		"session_id" => $sessionId,
		"time_in" => $timeIn,
		"user_id" => $userId
	]);

	if (!$result) {
		response(false, ["message" => "Failed to add time in!"]);
		exit;
	}

	response(true, ["message" => "Time in succesfull!"]);
}

function sessionTimeOut()
{
	$sessionId = $_POST["id"];
	$timeOut = $_POST["time_out"];

	$session = Session::find($sessionId, "id");

	if (!$session) {
		response(false, ["message" => "Session not found!"]);
		exit;
	}

	if ($session["status"] !== ACTIVE_SESSION) {
		response(false, ["message" => "Session inactive, time in attempt failed"]);
		exit;
	}

	$result = Session::update(
		$sessionId,
		[
			"time_out" => $timeOut
		]
	);

	if (!$result) {
		response(false, ["message" => "Timeout failed!"]);
		exit;
	}
	response(true, ["message" => "Timeout succesfull!"]);
}
