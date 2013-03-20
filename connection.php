<?
/*CLASS CONNECTION*/
class connection{
  public
		$host,
		$user,
		$password,
		$database,
		$connection = false;
	public function __construct ($host='localhost',$user='root',$password='',$database='test'){
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
		$this->database = $database;
	}
	public function connect() {
		if(!$this->connection)
        {
			$connect = @mysql_connect($this->host,$this->user,$this->password) or die('Could not connect: ' . mysql_error());
			if($connect){
				$select_db = @mysql_select_db($this->database) or die('Could not connect: ' . mysql_error());
				if($select_db){
					$this->connection = true;
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	public function disconnect() {
		if($this->connection){
			if(@mysql_close()){
				$this->connection = false;
				return false;
			}else{
				return true;
			}
		}
	}
	public function exist(){
		$q = 'SHOW TABLES FROM '.$this->database.'';
		$query = @mysql_query($q) or die ('Could not connect: ' . mysql_error());
		while($rows = mysql_fetch_row($query)){
			$this->exist[]=$rows;
		}
		return $this->exist;
	}
}
/*---------------------------------------------------------------------------------------------*/
$connection = new connection('localhost','root','','test');
$connection->connect();
?>
