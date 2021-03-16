<?php
/**
 * @author Iuri Cardoso Araújo 
 */
namespace system\mvc;

use system\mvc\Persistence;

/*
 * Métodos muito utilizados por Models inspirado no Eloquent do Laravel
 */
class BaseModel extends Persistence
{
	protected $database;

	protected $table = 'teste';

	protected $primaryKey = 'id';

	protected $autoincremting = true;

	protected $timestamps = false;

	//para um
	private $data = [];

	private $columns = false;
	
	/** vetor de statments where */
	private $where = [];

	/** valores do where para bind */
	private $whereBind = [];

	/** order by */
	private $orderBy = false;

	/** define valores para limit caso haja */
	private $limit = false;

	public function __construct()
	{
		parent::__construct();
		$this->database = app_config('database','database');
	}

	public function __destruct()
	{
		$this->closeConnection();
	}

	/** Faz com que atributos sejam 'criados' dinamicamente */
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}

    public function getColumns()
    {
        return $this->data;
    }

    public function setColumn($column,$value)
    {
        $this->data[$column] = $value;
    }
    
	public function all()
	{
		$query = "SELECT * FROM `{$this->database}`.`$this->table`";
		return $this->selectApply($query);
	}

	public function find($id = null)
	{
		$query = "SELECT * FROM `{$this->database}`.`$this->table` WHERE `{$this->primaryKey}` = {$id}";
		$result = $this->selectApply($query);

		$this->data = $result[0] ?? $result;
		
		return $this->data;
	}

	public function findOrNew($id)
	{
		$model = $this->find($id);
		$lastId = $this->getLastIdInserted();

		//Não persiste no banco, precisa de um save antes;
		if(empty($model)) {
            // $this->data[$this->primaryKey] = $lastId + 1; 
            return $this;
        } 
        else return $model;
	}

	public function getLastIdInserted()
	{
		$query = "SELECT {$this->primaryKey} FROM `{$this->database}`.`$this->table` ORDER BY `$this->table`.`{$this->primaryKey}` DESC LIMIT 1";
		$result = $this->selectApply($query);

        $teste = $result[0][$this->primaryKey] ?? $result[$this->primaryKey] ?? 0;
        return $result[0][$this->primaryKey] ?? $result[$this->primaryKey] ?? 0;
	}

	public function save()
	{
		if(empty($this->data)) {
            throw new \Exception(sprintf('Entidade %s não possui nenhum valor à ser salvo', $this->table));
        }

		$isUpdate = (array_key_exists($this->primaryKey, $this->data) && !empty($this->data[$this->primaryKey]));

		//se já existe, atualiza
		if($isUpdate){

			$primaryKey = $this->data[$this->primaryKey];

			$bindPrepareUpdate = $this->bindPrepareUpdate();
			$query = "UPDATE `{$this->database}`.`$this->table` SET {$bindPrepareUpdate} WHERE `{$this->primaryKey}` = {$primaryKey}";
			
			$this->queryApply($query, array_values($this->data));
		}

		//se não existe, insere um novo
		else{

			$columns = array_keys($this->data);
			$columns = implode(',',$columns);
			
			$values = array_values($this->data);
			$bindPrepareInsert = $this->bindPrepareInsert();

			$query = "INSERT INTO `{$this->database}`.`$this->table` ({$columns}) VALUES ({$bindPrepareInsert})";
			$this->queryApply($query, $values);
		}
		
	}

	public function create($data)
	{
		$sql = "INSERT INTO {$this->table} ";
	
		$columnsData = array_keys($data);
		$valuesData = array_values($data);
		
		$values = [] ; 
		foreach($valuesData as $value){
			//prepara a query para o bind
			array_push($values, '?'); 
		}

		$columns = implode(",", $columnsData);
		$values = implode(",", $values);
	
		$sql .= "({$columns}) VALUES({$values})";
		$this->queryApply($sql, $valuesData);
	}

	public function addColumns($columns = [])
	{
		$this->columns = $columns;
		return $this;
	}

	public function where($key, $value, $operation = '=')
	{
		// if(strtoupper($operation) == 'LIKE') $value = "%{$value}%";
		$operation = strtoupper($operation);
		$statment = " AND $key {$operation} ?";
		array_push($this->where,$statment);
		array_push($this->whereBind,$value);
		return $this;
	}

	public function orWhere($key, $value, $operation = '=')
	{
		$operation = strtoupper($operation);
		$statment = " OR $key {$operation} ?";
		array_push($this->where,$statment);
		array_push($this->whereBind,$value);
		return $this;
	}

	public function whereRaw($statment,$values = [])
	{
		$statment = " AND {$statment}";
		array_push($this->where,$statment);
		foreach($values as $value) array_push($this->whereBind,$value);
		return $this;
	}

	public function orderBy($orderBy = 'id', $order = 'ASC')
	{
		$this->orderBy = "$orderBy {$order}" ;	
		return $this;
	}
	
	public function limit($limit = 1)
	{
		$this->limit = $limit ;	
		return $this;
	}

	public function get()
	{
		$columns = $this->columns ? implode(',',$this->columns) : '*';
		$query = "SELECT {$columns} FROM `{$this->database}`.`$this->table` WHERE 1=1";
		if(!empty($this->where)) {
			foreach($this->where as $whereStatment){
				$query .= $whereStatment;
			}
		}

		/** inclui order by caso haja */
		$query = $this->orderBy ? "{$query} ORDER BY {$this->orderBy}" : $query;

		/** inclui limit by caso haja */
		$query = $this->limit ? "{$query} LIMIT {$this->LIMIT}" : $query;

		return $this->selectApply($query,$this->whereBind);
	}

	public function first()
	{
		$data = $this->get();
		if(is_array($data) && !empty($data)) $data = $data[0];
		return $data;
	}

	public function update()
	{

	}

	public function delete()
	{

	}

	// public function toArray()
	// {

	// }

	// public function toJson()
	// {
	// 	return json_encode($this->data);
	// }

	private function bindPrepareInsert()
	{
		$sql = [];
		foreach($this->data as $key => $value){
			//prepara a query para o bind
			array_push($sql,'?');
		}
		return implode(',',$sql);
	}

	private function bindPrepareUpdate()
	{
		$sql = [];
		foreach($this->data as $key => $value){
			//prepara a query para o bind
			$sql[] = "`{$key}` = ? "; 
		}
		return implode(',',$sql);
	}

}