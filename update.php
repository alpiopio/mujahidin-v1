<?php
class update{
  public
		$table,
		$where = array(),
		$data = array();
	public function __construct ($table,$data = null,$where = null){
		$this->table = $this->escape($table);
		$this->data = $data;
		$this->where = $where;
	}

	public function update_execution(){
		if($this->data != null AND $this->where != null){
			$q = 'UPDATE '.$this->table.' SET';
			$q .= ' '.$this->set_value();
			$q .= ' WHERE '.$this->where($this->where);
			echo $q;
			$query = @mysql_query($q) or die ('Could not connect [3]: ' . mysql_error());
			if($query){
				echo 'SUKSES';
			}
		}else{
			echo 'NOTHING CHANGE';
		}
	}
	public function set_value(){
		$check = array_combine($this->set_specifies_field(),$this->data);
		foreach($check as $field => $value){
			$final[] = $field." = '".$value."'";
		}
		$set = implode(',',$final);
		return $set;
	}
	public function set_specifies_field(){
		$queries = 'SELECT * FROM '.$this->table;
		$result = @mysql_query($queries) or die ('Could not connect [1]: ' . mysql_error());
		$fields = @mysql_num_fields($result) or die ('Could not connect [2]: ' . mysql_error());//menghitung jumlah field
			for ($f=0; $f < $fields; $f++) {//berulang sejumlah fieldnya
				$field = mysql_field_name($result, $f);
				$final[] = $field;
			}
		return $final;
	}
	public function where($data){
		foreach($data as $field => $value){
			$q[] = $field." = ".$value;
		}
		$final = implode(' AND ',$q);
		return $final;
	}
	public function escape($escape){
		$this->escape = $escape;
		$this->escape = mysql_real_escape_string($this->escape);
		return $this->escape;
	}
}
/*------------------------------------------------------------------------------------------------------*/
require_once __DIR__.'/connection.php';
/*---------------------------------mengambil nilai dari pos automatic-----------------------------------*/ 
$q = 'SELECT * FROM '.$_POST['table'];
$result = @mysql_query($q) or die ('Could not connect [4]: ' . mysql_error());
$fields = @mysql_num_fields($result) or die ('Could not connect: ' . mysql_error());//menghitung jumlah field
for ($f=0; $f < $fields; $f++) {//berulang sejumlah fieldnya
	$field = mysql_field_name($result, $f);//cetak field yang ada dengan offsetnya
	$data[] = $_POST[$field];
}
$key = mysql_field_name($result,0);
$where = array($_POST[$key]=>$_POST[$where]);
/*----------------------------------------eksekusi perintah---------------------------------------------*/
$update = new update($_POST['table'],$data,$where);
$update->update_execution()
?>
