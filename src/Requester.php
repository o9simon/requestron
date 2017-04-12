<?php
namespace o9simon\requestron;

require_once("Settings.php");

/*
 * Class used to perform database operations. The SQL queries should be stored
 * in an XML file. The format should look like this:

 * <?xml version="1.0" encoding="UTF-8"?>
 * <requests>
 * 	<request name="query_name">
 *	<![CDATA[
 *		SQL query
 *	]]>
 *	</request>
 * </requests>
 *
 * The functions insert, delete and update are the same. The names only change
 * to make code more readable.
 */
class Requester {

	/*
	 * Retrieves the raw query from the XML document.
	 */
	private function get_raw_query($query_name) {
		$xml = simplexml_load_string(file_get_contents(Settings::XML_PATH));
		$result = $xml->xpath("//request[@name='$query_name']");
		return $result[0]->__toString();
	}

	/*
	 * Function used for queries that return a success boolean value.
	 * Used for insertions, deletions and updates.
	 */
	private function query($query_name, $params) {
		$conn = null;
		$stmt = null;
		
		try {
			$conn = new PDO(Settings::DB_CONN_STRING, Settings::DB_USER, Settings::DB_PASSWORD);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$stmt = $conn->prepare($this->get_raw_query($query_name));
			$result = $stmt->execute($params);
			
			// Close connection
			$stmt = null;
			$conn = null; 

			return $result;
		} catch (PDOException $e) {

			// Close connection
			$stmt = null;
			$conn = null; 

			return null;
		}
	}

	/*
	 * Returns all the results of a select query.
	 */
	public function select($query_name, $params = null) {
		$conn = null;
		$stmt = null;
		
		try {
			$conn = new PDO(Settings::DB_CONN_STRING, Settings::DB_USER, Settings::DB_PASSWORD);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$stmt = $conn->prepare($this->get_raw_query($query_name));
			$stmt->execute($params);
			
			$results = $stmt->fetchAll();

			// Close connection
			$stmt = null;
			$conn = null; 

			return $results;
		} catch (PDOException $e) {

			// Close connection
			$stmt = null;
			$conn = null; 

			return null;
		}
	}

	/*
	 * Returns the first result of a select query.
	 */
	public function select_first($query_name, $params = null) {
		$results = $this->select($query_name, $params);
		if ($results != null) {
			return $results[0];
		}
		return null;
	}

	/*
	 * Inserts a new record and returns the id.
	 */
	public function insert($query_name, $params) {
		$conn = null;
		$stmt = null;
		
		try {
			$conn = new PDO(Settings::DB_CONN_STRING, Settings::DB_USER, Settings::DB_PASSWORD);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$stmt = $conn->prepare($this->get_raw_query($query_name));
			$stmt->execute($params);
			
			$lastInsertId = $conn->lastInsertId();

			// Close connection
			$stmt = null;
			$conn = null; 

			return $lastInsertId;
		} catch (PDOException $e) {

			// Close connection
			$stmt = null;
			$conn = null; 

			return null;
		}
	}

	public function delete($query_name, $params) {
		return $this->query($query_name, $params);
	}

	public function update($query_name, $params) {
		return $this->query($query_name, $params);
	}

	public function count($query_name, $params = null) {
		$results = $this->select($query_name, $params);
		if ($results == null) {
			return 0;
		} else {
			return sizeof($results);
		}
	}

	public function exists($query_name, $params = null) {
		return $this->count($query_name, $params) > 0;
	}

}
