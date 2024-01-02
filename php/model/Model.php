<?php

namespace model;

require_once(__DIR__ . "/../util/util.php"); //helper methods
require_once(__DIR__ . "/../database/Database.php");
require_once(__DIR__ . "/../util/response.php");

use database\Database;
use Exception;

class Model
{
	//set table name
	protected $table;
	//actual folder location
	protected $uploadFolderLocation;
	/**
	 * Create new item 
	 * @return bool true if successful
	 */
	public static function create(array $data, $condition = null)
	{
		//create new static instance
		$instance = new static();
		$table = $instance->table;

		try {
			if (empty($table)) {
				throw new Exception("Table name not specified");
			}

			//get the array key of the data
			$columns = array_keys($data);

			//convert array to string with a comma separator;
			$columnName = implode(", ", $columns);

			$placeholders = ":" . implode(", :", $columns);

			//queries the database
			$query = "INSERT INTO $table ($columnName) VALUES ($placeholders)";

			if (!is_null($condition)) {
				$query = "INSERT INTO $table ($columnName) VALUES ($placeholders) WHERE $condition";
			}

			//execute the prepared statement
			$stmt = Database::connect()->prepare($query);

			foreach ($data as $key => $value) {
				if (in_array($key, $columns)) {
					//remove html characters and tags
					$value = htmlspecialchars(strip_tags($value));
					$stmt->bindValue(":$key", $value);
				}
			}
			return $stmt->execute();
		} catch (Exception $e) {
			$responseMessage = "Error: {$e->getMessage()} on line {$e->getLine()}";
			response(false, ["message" => $responseMessage]);
		}
	}
	/**
	 * Returns all items from the table
	 * @param array|null $columns the columns to be returned. Set null to fetch all columns
	 * @return array items
	 */
	public static function read(array $columns = null)
	{
		//create new static instance
		$instance = new static();
		$table = $instance->table;

		try {
			if (empty($table)) {
				throw new Exception("Table name not specified");
			}

			$query = "SELECT * FROM $table";

			if (!is_null($columns)) {
				//convert array to string
				$columns = implode(", ", $columns);
				$query = "SELECT $columns FROM $table";
			}

			$stmt = Database::connect()->prepare($query);

			$stmt->execute();

			$rowCount = $stmt->rowCount();

			if ($rowCount == 0) {
				return null;
			}

			$result = $stmt->fetchAll();

			return $result;
		} catch (Exception $e) {
			$responseMessage = "Error: {$e->getMessage()} on line {$e->getLine()}";
			response(false, ["message" => $responseMessage]);
		}
	}
	/**
	 * Fetch a specific item
	 * @param mixed $value 
	 * @param string $condtion
	 * @param bool $all set true to fetch all rows
	 * @return array item
	 */
	public static function find($value, $condition, $fetchAll = false)
	{
		//create new static instance
		$instance = new static();
		$table = $instance->table;
		try {
			if (empty($table)) {
				throw new Exception("Table name not specified");
			}

			$query = "SELECT * FROM $table WHERE $condition = :$condition";

			$stmt = Database::connect()->prepare($query);

			//bind data
			$stmt->bindParam(":$condition", $value);

			$stmt->execute();
			//count result
			$rowCount = $stmt->rowCount();
			// dd($rowCount);
			if ($rowCount == 0) {
				return null;
				exit;
			}


			//returns all rows
			if ($fetchAll === true) {
				$result = $stmt->fetchAll();
				return $result;
				exit;
			}

			$result = $stmt->fetch();
			return $result;
		} catch (Exception $e) {
			$responseMessage = "Error: {$e->getMessage()} on line {$e->getLine()}";
			response(false, ["message" => $responseMessage]);
		}
	}

	/**
	 * Returns specific item from the table based on the condition
	 * @param array $condition the condition colum
	 * @param bool $fetchAll set true to fetch all 
	 * @param string $logicalOperator
	 */
	public static function where(array $conditions, $fetchAll = false, string $logicalOperator = "AND")
	{
		$instance = new static();
		$table = $instance->table;
		try {
			if (empty($table)) {
				throw new Exception('Table name not specified.');
			}

			$whereClause = self::buildWhereClause($conditions, $logicalOperator);

			$query = "SELECT * FROM {$table} {$whereClause}";
			$stmt = Database::connect()->prepare($query);

			foreach ($conditions as $key => $value) {

				// Check if the value is an array (for IN condition)
				if (is_array($value)) {
					// Check for "!=" in the array key for NOT IN condition
					if (strpos($key, '!=') !== false) {
						$key = trim(str_replace('!=', '', $key));
					}

					// Ensure parameter names have a consistent format with ":" prefix and numeric suffix
					foreach ($value as $index => $val) {
						$placeholder = ":{$key}_{$index}";
						$stmt->bindValue($placeholder, $val);
					}
				} else {
					// For other conditions, use the original parameter name with ":" prefix
					$placeholder = ":{$key}";
					$stmt->bindValue($placeholder, $value);
				}
			}

			$stmt->execute();

			if ($fetchAll === true) {
				$result = $stmt->fetchAll();
			} else {
				$result = $stmt->fetch();
			}

			return $result;
		} catch (Exception $e) {
			$responseMessage = "Error: {$e->getMessage()} on line {$e->getLine()}";
			response(false, ["message" => $responseMessage]);
		}
	}
	// Build the WHERE clause for the query
	protected static function buildWhereClause(array $conditions, string $logicalOperator = 'AND')
	{
		if (empty($conditions)) {
			return ''; // Return an empty string if no conditions are provided
		}

		$validOperators = ['AND', 'OR'];
		$logicalOperator = strtoupper($logicalOperator);
		$logicalOperator = in_array($logicalOperator, $validOperators) ? $logicalOperator : 'AND';

		$whereClause = 'WHERE ';
		$conditionsArray = [];

		foreach ($conditions as $column => $value) {
			// If the value is an array, use IN operator
			if (is_array($value)) {
				$notIn = false;
				$comparisonOperator = 'IN';

				// Check for "!=" in the array key for NOT IN condition
				if (strpos($column, '!=') !== false) {
					$notIn = true;
					$comparisonOperator = 'NOT IN';
					$column = trim(str_replace('!=', '', $column));
				}

				$paramNames = [];
				foreach ($value as $index => $val) {
					$paramName = ":{$column}_{$index}";
					$paramNames[] = $paramName;
					$conditions[$paramName] = $val;
				}

				$conditionValues = implode(',', $paramNames);
				$conditionsArray[] = "$column $comparisonOperator ($conditionValues)";
				unset($conditions[$column]);

				if ($notIn) {
					$conditionsArray[count($conditionsArray) - 1] = "{$conditionsArray[count($conditionsArray) - 1]}";
				}

				unset($conditions[$column]);
			} else {
				$comparisonOperator = '=';
				$conditionValue = ":$column";

				// Check for comparison operators other than '='
				if (strpos($column, ' ') !== false) {

					[$column, $comparisonOperator] = explode(' ', $column, 2); //separates column string (column and operator) and set to a variable

					$conditionValue = ":$column";
				}

				$conditionsArray[] = "$column $comparisonOperator $conditionValue";
			}
		}

		$whereClause .= implode(" $logicalOperator ", $conditionsArray);

		return $whereClause;
	}

	/**
	 * Update an item
	 * @return bool true if success
	 */
	public static function update($id, array $data)
	{
		//create new static instance
		$instance = new static();
		$table = $instance->table;

		try {
			if (empty($table)) {
				throw new Exception("Table name not specified");
			}

			$updateValues = "";

			$columns = array_keys($data);

			foreach ($columns as $column) {
				if (isset($data[$column])) {
					$updateValues .= $column . " = :" . $column . ", ";
				}
			}

			//strip comma from the end of the string
			$updateValues = rtrim($updateValues, ", ");

			$query = "UPDATE $table SET $updateValues WHERE id = :id";

			$stmt = Database::connect()->prepare($query);

			foreach ($data as $key => $value) {
				if (in_array($key, $columns)) {
					//clean data
					$value =  htmlspecialchars(strip_tags($value));
					$stmt->bindValue(":$key", $value);
				}
			}
			$stmt->bindValue(":id", $id);

			return $stmt->execute();
		} catch (Exception $e) {
			$responseMessage = "Error: {$e->getMessage()} on line {$e->getLine()}";
			response(false, ["message" => $responseMessage]);
		}
	}
	/**
	 * Delete an item
	 * @return bool true if sucess
	 */
	public static function delete($id)
	{
		//create new static instance
		$instance = new static();
		$table = $instance->table;

		try {
			if (empty($table)) {
				throw new Exception("Table name not specified");
			}

			$query = "DELETE FROM $table WHERE id = :id";

			$stmt = Database::connect()->prepare($query);

			$stmt->bindValue(":id", $id);

			return $stmt->execute();
		} catch (Exception $e) {
			$responseMessage = "Error: {$e->getMessage()} on line {$e->getLine()}";
			response(false, ["message" => $responseMessage]);
		}
	}
}
