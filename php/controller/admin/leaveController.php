<?php
require_once(__DIR__ . "/../../util/header.php");
require_once(__DIR__ . "/../../model/User.php");
require_once(__DIR__ . "/../../model/Leave.php");


use Ulid\Ulid;
use model\User;
use model\Leave;

//fetch request method
$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {

	case "POST": {
			if (isset($_POST["action"])) {

				if ($_POST["action"] == "approve") {
					approveRequest();
				} else if ($_POST["action"] == "deny") {
					denyRequest();
				}
			}
			break;
		}
	case "GET": {
			if (isset($_GET["id"]) || !empty($_GET["id"])) {
				fetchSingleLeaveRequest();
			} else {
				fetchLeaveRequests();
			}

			break;
		}
	default: {
			$responseMessage = "Request method: {$requestMethod} not allowed!";
			response(false, ["message" => $responseMessage]);
			break;
		}
}

function approveRequest()
{
	$leave_id = $_POST["id"];
	$leave_response = $_POST["response"];

	if (!Leave::find($leave_id, "id")) {
		response(false, ["message" => "Leave request not found!"]);
		exit;
	}

	$result = Leave::update(
		$leave_id,
		[
			"status" => $leave_response
		]
	);

	if (!$result) {
		response(false, ["message" => "Request leave failed"]);
		exit;
	}
	response(true, ["message" => "Leave request succesful"]);
}

function denyRequest()
{
	$leave_id = $_POST["id"];
	$leave_response = $_POST["response"];

	if (!Leave::find($leave_id, "id")) {
		response(false, ["message" => "Leave request not found!"]);
		exit;
	}

	$result = Leave::update(
		$leave_id,
		[
			"status" => $leave_response
		]
	);

	if (!$result) {
		response(false, ["message" => "Request leave failed"]);
		exit;
	}
	response(true, ["message" => "Leave request succesful"]);
}
function fetchLeaveRequests()
{
	$result = Leave::read();

	if (!$result) {
		response(false, ["message" => "No requests found"]);
		exit;
	}
	response(true, ["data" => $result]);
}
function fetchSingleLeaveRequest()
{
	$leaveId = $_GET["id"];

	$result = Leave::find($leaveId, "id");

	if (!$result) {
		response(false, ["message" => "Request not found"]);
		exit;
	}

	response(true,  $result);
}
