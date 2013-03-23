<?php
class insert{
  public
		$table,
		$data = array();
	public function __construct ($table,$data = null){
		$this->table = $this->escape($table);
		$this->data = $data;
	}
	public function insert_execution(){
		if($this->data != null AND $this->data!=null){
			$q	= "INSERT INTO ".$this->table;
			$q .= " (".$this->set_specifies_field().") VALUES";
			$q .= " (".$this->set_value().")";
			
			$query = @mysql_query($q) or die ('Could not connect [3]: ' . mysql_error());
			if($query){
				echo 'SUKSES';
			}
		}else{
			echo 'NOTHING CHANGE';
		}
	}
	public function set_value(){
		foreach($this->data as $temp){//setting value
			$value[] = "'".$temp."'";
		}
		$value = implode(',',$value);
		return $value;
	}
	public function set_specifies_field(){
		$queries = 'SELECT * FROM '.$this->table;
		$result = @mysql_query($queries) or die ('Could not connect [1]: ' . mysql_error());
		$fields = @mysql_num_fields($result) or die ('Could not connect [2]: ' . mysql_error());//menghitung jumlah field
			for ($f=0; $f < $fields; $f++) {//berulang sejumlah fieldnya
				$field = mysql_field_name($result, $f);
				$final[] = $field;
			}
		$specifies_field = implode(',',$final);
		return $specifies_field;
	}
	public function escape($escape){
		$this->escape = $escape;
		$this->escape = mysql_real_escape_string($this->escape);
		return $this->escape;
	}
}
/*------------------------------------------------------------------------------------------------------*/
require_once __DIR__.'/connection.php';
/*--------------------------------mengambil nilai dari pos automatic------------------------------------*/ 
$q = 'SELECT * FROM '.$_POST['table'];
$result = @mysql_query($q) or die ('Could not connect [4]: ' . mysql_error());
$fields = @mysql_num_fields($result) or die ('Could not connect: ' . mysql_error());//menghitung jumlah field
for ($f=0; $f < $fields; $f++) {//berulang sejumlah fieldnya
	$field = mysql_field_name($result, $f);//cetak field yang ada dengan offsetnya
	$data[] = $_POST[$field];
}
/*------------------------------------------------------------------------------------------------------*/
$insert = new insert($_POST['table'],$data);
$insert->insert_execution();
?>
