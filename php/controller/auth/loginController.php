<?php
require_once(__DIR__ . "/../../util/header.php");
require_once(__DIR__ . "/../../model/User.php");

use model\User;
use Ulid\Ulid;

//fetch request method
$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {

	case "POST": {
			login();
			break;
		}
	default: {
			$responseMessage = "Request method: {$requestMethod} not allowed!";
			response(false, ["message" => $responseMessage]);
			break;
		}
}

function login()
{
	$email = $_POST["email"];
	$password = $_POST["password"];

	$result = User::find($email, "email");
	if (!$result || !password_verify($password, $result["password"])) {
		response(false, ["message" => "Invalid Credentials"]);
		exit;
	}

	//trim the first word of the middlename
	$middlename = substr($result["middlename"], 0, 1) . ".";
	$fullname = $result["firstname"] . " " . $middlename . " " . $result["lastname"];

	$returnData = [
		"user_id" => $result["id"],
		"username" => $fullname,
		"role" => $result["role"]
	];

	response(true, ["data" => $returnData]);
}
