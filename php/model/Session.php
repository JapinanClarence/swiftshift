<?php

namespace model;

use model\Model;

require_once(__DIR__ . "/Model.php");

class Session extends Model
{
	protected $table = "session";
}
