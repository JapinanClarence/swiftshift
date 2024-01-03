<?php
require_once(__DIR__ . "/../../util/header.php");
require_once(__DIR__ . "/../../model/Session.php");

use model\Session;
use Ulid\Ulid;

//fetch request method
$requestMethod = $_SERVER["REQUEST_METHOD"];
switch ($requestMethod) {

	case "POST": {
			if (isset($_POST["_method"])) {
				if ($_POST["_method"] === "DELETE") {
					deleteSession();
				} else if ($_POST["_method"] === "PATCH") {
					updateSessionStatus();
				}
			} else {
				createSession();
			}
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
	$results = Session::read();

	if (!$results) {
		response(false, ["message" => "No sessions found!"]);
		exit;
	}
	response(true, ["data" => $results]);
}
function createSession()
{
	$userId = $_POST["user_id"];
	$date = $_POST["date"];
	$timeIn = $_POST["time_in"];
	$timeOut = $_POST["time_out"];

	//generate id
	$id = Ulid::generate(true);

	$result = Session::create([
		"id" => $id,
		"date" => $date,
		"time_in" => $timeIn,
		"time_out" => $timeOut,
		"user_id" => $userId
	]);

	if (!$result) {
		response(false, ["message" => "Failed to add session!"]);
		exit;
	}

	response(true, ["message" => "Session added!"]);
}
function deleteSession()
{
	$sessionId = $_POST["id"];

	if (!Session::find($sessionId, "id")) {
		response(false, ["message" => "Session not found!"]);
		exit;
	}

	if (!Session::delete($sessionId)) {
		response(false, ["message" => "Failed to delete session!"]);
		exit;
	}
	response(true, ["message" => "Session deleted succesfully!"]);
}
function updateSessionStatus()
{
	$sessionId = $_POST["id"];
	$sessionStatus = $_POST["status"];


	if (!Session::find($sessionId, "id")) {
		response(false, ["message" => "Session not found!"]);
		exit;
	}

	$result = Session::update(
		$sessionId,
		[
			"status" => $sessionStatus
		]
	);

	if (!$result) {
		response(false, ["message" => "Failed to update status!"]);
		exit;
	}
	response(true, ["message" => "Status updated succesfully!"]);
}
