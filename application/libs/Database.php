<?
class Database extends PDO{
	public function __construct(){

		try{
			parent::__construct('mysql:host=' . DB_HOST . ';port=3311;dbname=' . DB_NAME, DB_USER, DB_PW);
			$this->query("set names utf8");

			// Functions
				$f .= "DROP FUNCTION IF EXISTS nextOrderID;
					DELIMITER $$
					CREATE FUNCTION nextOrderID()
					  RETURNS VARCHAR(15)
					BEGIN
					  DECLARE orderPrefix VARCHAR(5);
					  DECLARE newOrderId VARCHAR(15);
					  DECLARE mainKey VARCHAR(15);
					  DECLARE cYear VARCHAR(5);
					  DECLARE cMonth VARCHAR(5);
					  DECLARE prevKey INT DEFAULT 0;
					  DECLARE prevKeyStr VARCHAR(5) DEFAULT '0000';

					  SET orderPrefix = 'DIUSS';
					  SET cYear 	= SUBSTR(YEAR(NOW()),3);
					  SET cMonth 	= MONTH(NOW());

					  IF LENGTH(cMonth) <= 1 THEN
						SET cMonth = CONCAT('0',cMonth);
					  END IF;

					  SET mainKey = CONCAT(orderPrefix,cYear,cMonth);

					  SELECT REPLACE(orderKey,mainKey,'') INTO prevKey FROM `orders` WHERE orderKey LIKE CONCAT(mainKey,'%') ORDER BY orderedAt DESC LIMIT 0,1;

					  SET prevKey = prevKey + 1;

					  IF LENGTH(prevKey) = 1 THEN
						SET prevKeyStr = CONCAT('000',prevKey);
					  ELSEIF LENGTH(prevKey) = 2 THEN
						SET prevKeyStr = CONCAT('00',prevKey);
					  ELSEIF LENGTH(prevKey) = 3 THEN
						SET prevKeyStr = CONCAT('0',prevKey);
					  ELSE SET prevKeyStr = '0001';
					  END IF;

					  RETURN CONCAT( mainKey, prevKeyStr );
				END;
				$$
				DELIMITER ;";

		}catch(PDOException $e){
			die($e->getMessage());
		}
	}

	public  function insert($table, $fields, $values){
		// Kivételkezelés használata
		$this->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

		$q = $this->prepare("INSERT INTO $table(".implode($fields,', ').") VALUES(:".implode($fields,', :').")");

		$binds = array();
		foreach($values as $vk => $v){
			$binds[':'.$fields[$vk]] = (is_null($v)) ? null : stripslashes($v);
		}
		// Execute
		try{
			$q->execute($binds);
			return true;
		}catch(PDOException $e){
			throw new Exception($e->getMessage());
		}
	}

	public function select($table, $select = false, $where = '', $fetchAll = false){
		$ret = array();
		$q  = "SELECT ";
		if(!$select){
			$q .= " * ";
		}else{
			$q .= rtrim(implode(',', $select),',');
		}
		$q .= " FROM $table ";
		if($where != ''){
			$where = stripslashes($where);
			$q .= ' WHERE '.$where;
		}
		$d = $this->query($q);

		if($fetchAll){
			$ret = $d->fetchAll(PDO::FETCH_ASSOC);
		}else if(is_array($select) && count($select) == 1){
			$ret = $d->fetchColumn();
		}else{
			$ret = $d->fetch(PDO::FETCH_ASSOC);
		}

		return $ret;
	}

	public function update($table, $arg, $whr = ''){
		$q = "UPDATE $table SET ";
		$sm = '';

		foreach($arg as $ak => $av){
			$val = (is_null($av)) ? 'NULL' : (is_string($av)) ? "'".$av."'" : $av ;
			$sm .= '`'.$ak.'` = '.$val.', ';
		}
		$sm = rtrim($sm,', ');
		$q .= $sm;
		if($whr != ""){
			$q .= " WHERE ".stripslashes($whr);
		}
		$q .= ';';
		$this->query($q);
		return true;
	}

	public function delete($table, $whr){
		$this->db->query("DELETE FROM $table WHERE $whr;");
	}

	public function q($query, $arg = array()){
		$back 		= array();
		$pages 		= array();
		$total_num 	= 0;
		$return_str = ($arg[ret_str]) ? $arg[ret_str] : 'ret';
		$current_page = Helper::getLastParam();
		$get 		= count(Helper::GET());
		if($get <= 2) $current_page = 1;
			$pages[current] = (is_numeric($current_page) && $current_page > 0) ? $current_page : 1;
		$limit 		= 50;
		$data 		= array();
		//////////////////////
		$query = preg_replace('/^SELECT/i', 'SELECT SQL_CALC_FOUND_ROWS ', $query);


		// LIMIT
		if($arg[limit]){
			$query = rtrim($query,";");
			$limit = (is_numeric($arg[limit]) && $arg[limit] > 0 && $arg[limit] != '') ? $arg[limit] : $limit;
			$l_min = 0;
			$l_min = $pages[current] * $limit - $limit;
			$query .= " LIMIT $l_min, $limit";
			$query .= ";";
		}
        //echo $query;

		$q = $this->query($query);

		if(!$q){
			error_log($query);
			//$back[$return_str][info][query][error] = $q->errorInfo();
		}

		if($q->rowCount() == 1 && !$arg[multi]){
			$data = $q->fetch(PDO::FETCH_ASSOC);
		}else if($q->rowCount() > 1 || $arg[multi]){
			$data = $q->fetchAll(PDO::FETCH_ASSOC);
		}

		$total_num 	=  $this->query("SELECT FOUND_ROWS();")->fetchColumn();
		$return_num = $q->rowCount();

		///
			$pages[max] 	= ($total_num == 0) ? 0 : ceil($total_num / $limit);
			$pages[limit] 	= ($arg[limit]) ? $limit : false;

		$back[$return_str][info][input][arg] 	= $arg;
		$back[$return_str][info][query][str] 	= $query;
		$back[$return_str][info][total_num] 	= (int)$total_num;
		$back[$return_str][info][return_num] 	= (int)$return_num;
		$pages[current] = 1;
		$back[$return_str][info][pages] 		= $pages;

		$back[$return_str][data] 	= $data;
		$back[data] 				= $data;
		return $back;
	}

	public function crud($type, $data = array()){
		$back = false;
		switch($type){
			default:
				throw new Exception(__('Nincs ilyen művelet végrehajtó').': '.$type);
			break;
			case 'insert':
				$rows 	= $data[rows];
				$rows 	= explode(',', $rows);
				$values = $data[values];
				$values = explode('::', $values);

				if(empty($data)) throw new Exception(__('Művelet nem hajtódott végre. Nincs elküldött feldolgozandó adat!'));
				if(empty($data[table])) throw new Exception(__('Művelet nem hajtódott végre. Nincs kiválasztva cél táblázat!'));
				if(empty($rows)) throw new Exception(__('Művelet nem hajtódott végre. Nincs elküldött rekordkulcs azonosító!'));

				$back = $this->insert($data[table], $rows, $values);
			break;

			case 'update':
				$udata 	= $data[data];
				$udata 	= explode('::', $udata);
				$xdata 	= array();
					foreach($udata as $ud){
						$cdt = explode('=',$ud);
						$xdata[trim($cdt[0])] = trim($cdt[1]);
					}
				if(empty($data)) throw new Exception(__('Művelet nem hajtódott végre. Nincs elküldött feldolgozandó adat!'));
				if(empty($data[table])) throw new Exception(__('Művelet nem hajtódott végre. Nincs kiválasztva cél táblázat!'));
				if(empty($xdata)) throw new Exception(__('Művelet nem hajtódott végre. Nincsennek megadva a cserélendő rekordok!'));

				$back = $this->update($data[table],$xdata,$data[where]);
			break;

			case 'delete':
			break;

			case 'select':
				if(empty($data[table])) throw new Exception(__('Művelet nem hajtódott végre. Nincs kiválasztva cél táblázat!'));
				if($data[rows] == '') throw new Exception(__('Művelet nem hajtódott végre. Nincs kiválasztva visszatérő rekord!'));
				$rows = explode(',',$data[rows]);

				$loop = false;
				if($data[loop] || count($rows) > 1) $loop = true;


				$back = $this->select($data[table],$rows,$data[where], $loop);
			break;
		}
		return $back;
	}

	function __destruct(){

	}
}
?>
