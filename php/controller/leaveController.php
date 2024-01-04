<?php
require_once(__DIR__ . "/../util/header.php");
require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/Leave.php");


use Ulid\Ulid;
use model\User;
use model\Leave;

//fetch request method
$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {

	case "POST": {
			if (isset($_POST["_method"])) {
				if ($_POST["_method"] === "DELETE") {
					deleteRequest();
				}
			} else {
				requestLeave();
			}
			break;
		}
	case "GET": {
			fetchLeaveRequests();
			// if (isset($_GET["id"]) || !empty($_GET["id"])) {
			// 	fetchProfile();
			// }
			break;
		}
	default: {
			$responseMessage = "Request method: {$requestMethod} not allowed!";
			response(false, ["message" => $responseMessage]);
			break;
		}
}

function requestLeave()
{
	$user_id = $_GET["id"];
	$start_date = $_POST["start_date"];
	$end_date = $_POST["end_date"];
	$reason = $_POST["reason"];

	if (!User::find($user_id, "id")) {
		response(false, ["message" => "User not found!"]);
		exit;
	}
	$id = Ulid::generate(true);

	$result = Leave::create([
		"id" => $id,
		"employee_id" => $user_id,
		"start_date" => $start_date,
		"end_date" => $end_date,
		"reason" => $reason
	]);

	if (!$result) {
		response(false, ["message" => "Request leave failed"]);
		exit;
	}
	response(true, ["message" => "Leave request succesful"]);
}
function fetchLeaveRequests()
{
	$user_id = $_GET["id"];

	if (!User::find($user_id, "id")) {
		response(false, ["message" => "User not found!"]);
		exit;
	}

	$result = Leave::find($user_id, "employee_id", true);

	if (!$result) {
		response(false, ["message" => "No requests found"]);
		exit;
	}
	response(true, ["data" => $result]);
}
function deleteRequest()
{
	$id = $_POST["id"];

	if (!Leave::find($id, "id")) {
		response(false, ["message" => "Leave request not found!"]);
		exit;
	}

	$result = Leave::delete($id);

	if (!$result) {
		response(false, ["message" => "Delete Failed"]);
		exit;
	}
	response(true, ["message" => "Delete succesfull"]);
}
