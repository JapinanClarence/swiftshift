<?php

namespace model;

use model\Model;

require_once(__DIR__ . "/Model.php");

class User extends Model
{
	protected $table = "users";
}
