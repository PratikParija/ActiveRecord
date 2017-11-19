<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

define('DATABASE', 'pp285');
define('USERNAME', 'pp285');
define('PASSWORD', 'tOLTS1FCy');
define('CONNECTION', 'sql.njit.edu');

class dbConn{

	//variable to hold connection object.
	protected static $db;

	//private construct - class cannot be instated externally.
	private function __construct(){
		
		try{
			//assign PDO object to db variable
			self::$db = new PDO('mysql:host=' . CONNECTION .
			';dbname=' . DATABASE, USERNAME, PASSWORD);

		}catch(PDOException $e){
			//Output error - would normally log this error file rather than output to user
			echo "Connection Error: " . $e->getMessage();
		}
	}

	//get connection func. Static method - accessible without instantiation
	public static function getConnection(){
		
		//Guaranteed single instance, if no connection object exists then create one.
		if(!self::$db){
			//new connection object
			new dbConn();

		}

		//return connection
		return self::$db;

	}
}

class collection{
	static public function create(){
		$model = new static::$modelName;

		return $model;

	}

	static public function findAll(){
		$db = dbConn::getConnection();
		$tableName = get_called_class();
		$sql = 'SELECT * FROM ' . $tableName;
		$statement = $db->prepare($sql);
		$statement->execute();
		$class = static::$modelName;
		$statement->setFetchMode(PDO::FETCH_CLASS, $class);
		$recordSet = $statement->fetchAll();
		return $recordSet;

	}

	static public function findOne($id){
		$db = dbConn::getConnection();
		$tableName = get_called_class();
		$sql = 'SELECT * FROM ' . $tableName. " where id=" . $id;
		$statement = $db->prepare($sql);
		$statement->execute();
		$class = static::$modelName;
		$statement->setFetchMode(PDO::FETCH_CLASS, $class);
		$recordSet = $statement->fetchAll();
		return $recordSet;
	}

}

class accounts extends collection{
	protected static $modelName = 'accounts';

}

class todos extends collection{
	protected static $modelName = 'todos';
}

class model{
	//protected $tableName;

    static $columnString;
    static $valueString;

	public function save(){



		if(static::$id == '')

		{
            $db = dbConn::getConnection();
            $array = get_object_vars($this);
            static::$columnString = implode(', ', $array);
            static::$valueString = implode( ', ', array_fill(0,count($array),'?')) ;
			$sql = $this->insert();
            $statement = $db->prepare($sql);
            $statement->execute(static::$value);

         // echo $sql;


		}else{
            $db = dbConn::getConnection();
			$sql = $this->update();
            $statement = $db->prepare($sql);
           $statement->execute(static::$value);

		}



	}
    private function insert(){

        $sql = 'INSERT INTO ' . static::$tableName.  ' ('.static::$columnString.')' .' VALUES (' . static::$valueString. ')';

        return $sql;
        //Implode for the keys & also one for the values
      //  $statement = $db->prepare($sql);
     //   $statement->execute();

	}
    private function update(){

        $sql = "UPDATE " . static::$tableName . " SET " .static::$columnName."='" .static::$colmunValue . "' WHERE id=" . static::$id;

		return $sql;

	}
    public function delete(){

		//$tableName = static::$tableName;

		$db = dbConn::getConnection();
		$sql = 'DELETE from ' . static::$tableName . " WHERE id=" . static::$id;
		//echo $sql;
		$statement = $db->prepare($sql);
        $statement->execute();

        echo 'row with id ' . $this->id . ' has been deleted';


	}

}

class account extends model{
	

}

class todo extends model{
	//public $id;
	public $owneremail = 'owneremail';
	public $ownerid = 'ownerid';
	public $createddate = 'createddate';
	public $duedate = 'duedate';
	public $message = 'message';
	public $isdone = 'isdone';
    static $tableName = 'todos';
    static $id = '9';

   static $value = array('jnaz@njit.edu','112544','2017-11-28', '2017-12-01', 'test', '0');
   static $columnName = 'owneremail';
   static $colmunValue = 'test@test.com';

}


class Table{
 static function makeTable($result){

		echo '<table>';
		foreach ($result as $data){
			echo '<tr>';

			foreach ( $data as $colums){
				echo '<td>';
				echo $colums;
                echo '</td>';

			}

			echo '</tr>';

		}
        echo '</table>';

	}

}

class stringFunction{

	static function printString($sting){
		echo '<h1>'.$sting.'</h1>';
	}

}

stringFunction::printString('This is the find all statement for todo table');
$record = todos::create();
$data = $record->findAll();
Table::makeTable($data);
echo '<br>';
echo '<br>';


stringFunction::printString('This is the find one statement for todo table where id = 7');
$record = todos::create();
$data = $record->findOne(7);
Table::makeTable($data);
echo '<br>';
echo '<br>';

stringFunction::printString('This is the find all statement for Accounts table');
$record = accounts::create();
$data = $record->findAll();
Table::makeTable($data);
echo '<br>';
echo '<br>';

stringFunction::printString('This is the find one statement for Accounts table where id = 5');
$record = accounts::create();
$data = $record->findOne(5);
Table::makeTable($data);
echo '<br>';
echo '<br>';

stringFunction::printString('Update owneremail column on row 9 ');
$obj = new Todo;
$obj->save();
$record = todos::create();
$data = $record->findAll();
Table::makeTable($data);

