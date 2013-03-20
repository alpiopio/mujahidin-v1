<?php
/*CLASS DELETE*/
class delete{
  public
		$table,
		$data = array(),
		$where = array();
	public function __construct ($table,$where=null){
		$this->table = $this->escape($table);
		$this->where = $this->escape($where);
	}
	public function delete_execution(){
		if($this->where != null){
			$q = 'DELETE FROM '.$this->table;
			$q .= ' WHERE '.$this->where($this->where);
			$query = @mysql_query($q) or die ('Could not connect: ' . mysql_error());
			if($query){
				echo 'SUKSES';
			}
		}else{
			echo 'NOTHING CHANGE';
		}
	}
	public function escape($escape){
		$this->escape = $escape;
		$this->escape = mysql_real_escape_string($this->escape);
		return $this->escape;
	}
}
/*---------------------------------------------------------------------------------------------*/
if(isset($_POST)){
$where = $_POST['id'];
$table = $_POST['table'];
$field = $_POST['field'];
$delete = new delete($table,array($field=>$where));
$delete->delete_execution();
}
?>
