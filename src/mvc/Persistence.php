<?php
/**
 * @author Iuri Cardoso Araújo 
 */
namespace system\mvc;

use \PDO;
use PDOException;

/** Classe que auxilia na conexão e aplicação de queries no banco de dados */
class Persistence
{
	private $conn;
	
	public function __construct()
	{
		$conn = app_config('database');
		try {
			$this->conn = new PDO('mysql:host='. $conn['hostname'] . ';dbname=' . $conn['database'] . ';charset=utf8', $conn['username'], $conn['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// $this->startTransaction();

		} catch (PDOException $e) {

            try {  //caso o erro de unknown database, tenta sem o parametro de database
                $this->conn = new PDO('mysql:host='. $conn['hostname'] . ';charset=utf8', $conn['username'], $conn['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            } catch (\PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
                die();
            }
		}
	}

	protected function getConnection(){
		return $this->conn;
	}

	protected function startTransaction()
    {
        $this->conn->beginTransaction();
    }

	protected function closeConnection(){
		$this->conn = null;
	}

	protected function submitTransaction()
    {
        try {
            $this->conn->commit();
        } catch(PDOException $e) {
            $this->conn->rollBack();
            return false;
        }

          return true;
    }

	private function setParams($statment, $parameters = array())
	{
		if ($this->array_key_first($parameters) == '0') {
			$size = count($parameters);
			for ($i = 0; $i < $size; $i++) $this->setParam($statment, ($i + 1), $parameters[$i]);
		} else foreach ($parameters as $key => $value) {
			$this->setParam($statment, $key, $value);
		}
	}

	private function setParam($statment, $key, $value)
	{
		// echo "<br> bindParam($key,$value)";
		if (is_int($value)) $statment->bindParam($key, $value, PDO::PARAM_INT);
		else {$statment->bindParam($key, $value,PDO::PARAM_STR);}    
	}

	public function queryBuilder($comandoSQL, $params = array())
	{
		$stmt = $this->conn->prepare($comandoSQL);

		$this->setParams($stmt, $params);

		// debug banco
		if (isset($GLOBALS['dbg_pdo']) && $GLOBALS['dbg_pdo'] == 1)
			print_r($stmt);

		$stmt->execute();
		// $this->submitTransaction();

		return $stmt;
	}

	//apply com retorno
	public function selectApply($comando, $params = array())
	{
		$stmt = $this->queryBuilder($comando, $params);
		return $stmt->fetchALL(PDO::FETCH_ASSOC);
	}

	//apply sem retorno
	public function queryApply($comando, $params = array())
	{
		return $this->queryBuilder($comando, $params);
	}

	public function useDatabase($comando, $params = array())
	{
		$this->queryBuilder($comando, $params);
	}

	private function array_key_first(array $arr)
	{
		foreach ($arr as $key => $unused) {
			return $key;
		}

	}

}