<?php
/*CLASS SELECT*/
class select{
  public
		$result = array(),
		$table,
		$where,
		$order,
		$limit,
		$offset,
		$rows,
		$row,
		$current_page,
		$pos,
		$ascend;
	public function __construct($table, $where = null, $order = null, $ascending, $limit = null, $current_page, $range, $rows = "*"){
		$this->table		= $this->escape($table);
		$this->where		= $where;
		$this->order		= $this->escape($order);
		$this->limit		= $this->escape($limit);
		$this->rows			= $this->escape($rows);
		$this->current_page = $this->escape($current_page);
		$this->range		= $this->escape($range);
		$this->ascend		= $this->escape($ascending);
		$this->proses();
	}
	public function proses(){
		$q = 'SELECT COUNT(*) AS num_result FROM '.$this->table.'';
		$query = @mysql_query($q) or die ('Could not connect: ' . mysql_error());
		$num_result = mysql_fetch_array($query);
		if($num_result){
			$this->num_result = $num_result['num_result'];
		}
		$this->total_page = ceil($this->num_result / $this->limit);
		if ($this->current_page > $this->total_page){
			$this->current_page = $this->total_page;
		}else if($this->current_page < 1){
			$this->current_page = 1;
		}
		$this->offset = ($this->current_page - 1)* $this->limit;
		$this->pos = $this->offset + 1;
		$this->setRange($this->range);
	}
	public function isFirst() {return $this->current_page == 1;}
	
	public function isLast() {return $this->current_page == $this->total_page;}
	
	public function hasPrev() {return $this->current_page > 1;}
	
	public function hasNext() {return $this->current_page < $this->total_page;}
	
	public function select_execution(){
		$q = 'SELECT '.$this->rows.' FROM '.$this->table;
			if($this->where != null){
				$q .= ' WHERE '.$this->where;
			}
			if($this->order != null){
				$q .= ' ORDER BY '.$this->order;
			}
			if($this->ascend){
				$q .= ' ASC';
			}else{
				$q .= ' DESC';
			}
			if($this->limit != null){
				$q .= ' LIMIT '.$this->limit;
			}
			if($this->offset != null){
				$q .= ' OFFSET '.$this->offset;
			}
		$query = @mysql_query($q) or die ('Could not connect: ' . mysql_error());
		$fields = @mysql_num_fields($query);//menghitung jumlah field
		/*---------------------------------------------------------------------------*/
		echo "<div id='table'>";
		/*---------------------------------------------------------------------------*/
		echo "<div id='header_box'>".$this->table."</div>";
		/*---------------------------------------------------------------------------*/
		echo "<form>";
		echo "<table><tr>";
		echo "<th>no</th>";
		for ($i=0; $i < $fields; $i++){ //Table Header
			$field = mysql_field_name($query, $i);//cetak field yang ada dengan offsetnya
			if($i != 0){//jika bukan id atau field 0 maka di cetak
				echo "<th>".$field."</th>";
			}
		}
		echo "<th></th>";
		echo "</tr>\n";
		/*---------------------------------------------------------------------------*/
		$fieldpos = mysql_field_name($query, 0);
		/*---------------------------------------------------------------------------*/
		while ($this->row = mysql_fetch_row($query)) { //Table body (berulang sejumlah row atau barisnya)
			echo "<tr id='tr_".$this->row[0]."'>";
			echo "<td>".$this->pos++."</td>";
			for ($f=0; $f < $fields; $f++) {//berulang sejumlah fieldnya
				if($f != 0){//jika bukan id atau field 0 maka di cetak
					echo "<td>".substr($this->row[$f],0,100)."</td>";//cetak field sesuai offsetnya
				}
			}//index.php?table=".$this->escape($this->table)."&row=".$row[0]."  id='".$row[0]."'
			
			echo "<td><a href='#' class='delete' kode='".$this->row[0]."' table='".$this->table."' field='".$fieldpos."'></a></td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<div id='footer_box'></div>";
		echo "</form>";
		/*---------------------------------------------------------------------------*/
		echo "<div id='pagination'>";
		$this->controler_pagination();
		$this->pagination();
		echo "</div>";
		/*---------------------------------------------------------------------------*/
		echo "</div>";
		/*---------------------------------------------------------------------------*/
					echo "<div id='box' class='box'>";
						echo "<div class='overlay'></div>";
						echo "<div class='centerpoint'>";
							echo "<div id='dialog'>";
								echo "<div id='header_box'>hapus</div>";
								echo "<div class='message'>";
								echo "Anda yakin ingin menghapus ini ? <input type='button' class='submit' value='delete'> <input type='button' class='cancel' value='cancel'>";
								echo "</div>";
							echo "</div>";
						echo "</div>";
					echo "</div>";
		
		/*if($query){
			while($rows = mysql_fetch_assoc($query)){
				$this->result[]=$rows;
			}
			return $this->result;
		}*/
	}
	public function setRange($range){
		$this->range = (int) $range;
		$this->interval = floor($this->range / 2);
		$this->residu = $this->range % 2;
	}
	public function minmax (&$min,&$max){
	$min = $this->current_page - $this->interval;
	$max = $this->current_page + $this->interval;
	
	if($this->residu == 0){
		$this->current_page < ($this->total_page / 2) ? $min++ : $max--;
	}
	
		if ($min < 1){
			$max -= $min - 1;
			$min = 1;
			$max < $this->total_page or $max = $this->total_page;
		}else if ($max > $this->total_page){
			$min -= $max - $this->total_page;
			$max = $this->total_page;
			$min >= 1 or $min = 1;
		}
	}
	public function isBegin(){
		return $this->current_page <= $this->interval + $this->residu;
	}
	
	public function isEnd(){
		return $this->current_page > $this->total_page - ($this->interval + $this->residu);
	}
	
	public function pagination(){
		echo "<div class='pagination'>";
		echo "<ul>";
		if($this->isFirst()){echo "<li><span>FIRST</span></li>";}else{echo "<li><a href='?page=1'>FIRST</a></li>";};
		if($this->hasPrev()){echo "<li><a href='?page=".($this->current_page-1)."'>PREV</a></li>";}else{echo "<li><span>PREV</span></li>";};
		if($this->isBegin()){}else{echo "<li><span> ... </span></i>";};

		for($this->minmax($i,$j);$i<=$j;$i++){
		if($this->current_page == $i){
			echo "<li><span>".$i."</span></li>";
		}else{
			echo "<li><a href='?page=".$i."'>".$i."</a></li>";
		}
		}

		if($this->isEnd()){}else{echo "<li><span> ... </span></li>";};
		if($this->hasNext()){echo "<li><a href='?page=".($this->current_page+1)."'>NEXT</a></li>";}else{echo "<li><span>NEXT</span></li>";};
		if($this->isLast()){echo "<li><span>LAST</span></li>";}else{echo "<li><a href='?page=".$this->total_page."'>LAST</a></li>";};
		echo "</ul>";
		echo "</div>";
	}
	public function controler_pagination(){
		echo "<div class='controler'>";
		echo "TOTAL ".$this->num_result." DATA / PAGE ".$this->current_page." OF ".$this->total_page; 
		echo "</div>";
	}
	public function escape($escape){
		$this->escape = $escape;
		$this->escape = mysql_real_escape_string($this->escape);
		return $this->escape;
	}
}
/*---------------------------------------------------------------------------------------------*/
require_once __DIR__.'/connection.php';
echo "<link href='pagination.css' rel='stylesheet'>";
echo "<link href='table.css' rel='stylesheet'>";
echo "<link href='confirmationbox.css' rel='stylesheet'>";
echo "<html>";
echo "<body>";

$get_input = array_change_key_case($_GET,CASE_LOWER);/*get user handler*/
$page = isset($get_input['page']) ? $get_input['page'] : 1;/*page dynamic*/
$where = isset($get_input['where']) ? "nama_produk LIKE '".$get_input['where']."%'" : null;/*where dynamic for search*/

$ascending = true;
$select = new select('produk',$where,'kode_produk',$ascending,5,$page,5);
$select->select_execution();

echo "</body>";
echo "</html>";
?>
<script type="text/javascript" src="jquery-1.8.3.js"></script>
<script>
$(document).ready(function(){

	$(".delete").live('click',function()
	{
	var id=$(this).attr('kode');
	var table = $(this).attr('table');
	var field = $(this).attr('field');
	
	$(".submit").attr({"id":id,"table":table,"field":field});
	
	$("#box").show();
	});
	
	$(".submit").live('click',function()
	{
		var id = $(this).attr('id');
		var table = $(this).attr('table');
		var field = $(this).attr('field');
		
		var tr = $(this).attr('tr_'+id);
		
		var dataString = 'id='+id+'&table='+table+'&field='+field;
		
			$.ajax({
				type: "POST",
				url: "connect.php",
				data: dataString,
				cache: false,
				success: function(e)
				{
				$("#tr_"+id).hide();
				$("#box").hide();
				window.location.reload(true);
				e.stopImmediatePropagation();
				}
			});
			return false;
	});
	
	$(".overlay,.cancel").live('click',function()
	{
	$(".box").hide();
	});
	
});
</script>
