<?php
/* 
mySQL legacy library in PHP7 by inovativeweb.ro
Version :		0.1.1
Build time: 	18.05.2017

SCOPE:
Redefine the legacy depracaded mySQL functions

COMPATIBILITY: >= PHP5

USAGE:
require_once($_SERVER['DOCUMENT_ROOT']."mysql_replace.php");
OR require_once("/full/server/path/mysql_replace.php");

SETTINGS: NONE

KNOWN ISSUES:
mysql_list_processes() reurns incomplete data (precess id is returned but no database,table,time or other data )
mysql_field_type() can return some bad filed type descriptions, depending on the mysql server version
mysql_fetch_field() can return some types, depending on the mysql server version

TO IMPROVE:
sqli_h_type2txt() acording to mysql server version => it may fix the filed type issues

COMPLETE FUNCTION LIST:

#    mysql_affected_rows — Get number of affected rows in previous MySQL operation
#    mysql_client_encoding — Returns the name of the character set
#    mysql_close — Close MySQL connection
#    mysql_connect — Open a connection to a MySQL Server
#    mysql_create_db — Create a MySQL database
#    mysql_data_seek — Move internal result pointer
#    mysql_db_name — Retrieves database name from the call to mysql_list_dbs
#    mysql_db_query — Selects a database and executes a query on it
#    mysql_drop_db — Drop (delete) a MySQL database
#    mysql_errno — Returns the numerical value of the error message from previous MySQL operation
#    mysql_error — Returns the text of the error message from previous MySQL operation
#    mysql_escape_string — Escapes a string for use in a mysql_query
#    mysql_fetch_array — Fetch a result row as an associative array, a numeric array, or both
#    mysql_fetch_assoc — Fetch a result row as an associative array
#    mysql_fetch_field — Get column information from a result and return as an object
#    mysql_fetch_lengths — Get the length of each output in a result
#    mysql_fetch_object — Fetch a result row as an object
#    mysql_fetch_row — Get a result row as an enumerated array
#    mysql_field_flags — Get the flags associated with the specified field in a result
#    mysql_field_len — Returns the length of the specified field
#    mysql_field_name — Get the name of the specified field in a result
#    mysql_field_seek — Set result pointer to a specified field offset
#    mysql_field_table — Get name of the table the specified field is in
#    mysql_field_type — Get the type of the specified field in a result
#    mysql_free_result — Free result memory
#    mysql_get_client_info — Get MySQL client info
#    mysql_get_host_info — Get MySQL host info
#    mysql_get_proto_info — Get MySQL protocol info
#    mysql_get_server_info — Get MySQL server info
#    mysql_info — Get information about the most recent query
#    mysql_insert_id — Get the ID generated in the last query
#    mysql_list_dbs — List databases available on a MySQL server
#    mysql_list_fields — List MySQL table fields
#    mysql_listfields — Alias for mysql_list_fields; List MySQL table fields
#    mysql_list_processes — List MySQL processes
#    mysql_list_tables — List tables in a MySQL database
#    mysql_listtables — Alias for mysql_list_tables; List tables in a MySQL database
#    mysql_num_fields — Get number of fields in result
#    mysql_num_rows — Get number of rows in result
#    mysql_pconnect — Open a persistent connection to a MySQL server
#    mysql_ping — Ping a server connection or reconnect if there is no connection
#    mysql_query — Send a MySQL query
#    mysql_real_escape_string — Escapes special characters in a string for use in an SQL statement
#    mysql_result — Get result data
#    mysql_select_db — Select a MySQL database
#    mysql_set_charset — Sets the client character set
#    mysql_stat — Get current system status
#    mysql_tablename — Get table name of field
#    mysql_thread_id — Return the current thread ID
#    mysql_unbuffered_query — Send an SQL query to MySQL without fetching and buffering the result rows.
#    sqli_h_type2txt - parses the mysqli types depending on mysql server version
#    sqli_h_flags2txt - parses the mysqli flags
*/
if(!function_exists('mysql_connect') && function_exists('mysqli_connect')){

	define('MYSQL_ASSOC',MYSQLI_ASSOC);
	define('MYSQL_NUM',MYSQLI_NUM);
	define('MYSQL_BOTH',MYSQLI_BOTH);
	define('MYSQL_CLIENT_COMPRESS',MYSQLI_CLIENT_COMPRESS);
	define('MYSQL_CLIENT_SSL',MYSQLI_CLIENT_SSL);
	define('MYSQL_CLIENT_INTERACTIVE',MYSQLI_CLIENT_INTERACTIVE);
	define('MYSQL_CLIENT_IGNORE_SPACE',MYSQLI_CLIENT_IGNORE_SPACE);

	function mysql_connect($host,$user,$pass,$database,$new_link=false,$clFlags=0){
		$res=mysqli_connect($host,$user,$pass,$database);
		$GLOBALS['_MysqliSvRes']= $res;
		return $res;
	}
	function mysql_pconnect($host,$user,$pass,$new_link=false,$clFlags=0){return mysql_connect('p:'.$host,$user,$pass,$new_link=false,$clFlags);}
	function mysql_close($res=''){ return mysqli_close($res?$res:$GLOBALS['_MysqliSvRes']);}
	function mysql_query($query,$res=NULL){	 if(!$res){$res=$GLOBALS['_MysqliSvRes'];}else{$GLOBALS['_MysqliSvRes']=$res;}	return mysqli_query($res,$query,MYSQLI_STORE_RESULT);}
	function mysql_fetch_array($res,$result_type = MYSQLI_BOTH){ return mysqli_fetch_array ( $res, $result_type );}
	function mysql_fetch_assoc($res){ return mysqli_fetch_assoc ( $res);}
	function mysql_fetch_row($res){ return mysqli_fetch_row ( $res);}
	function mysql_insert_id($res=NULL){	if(!$res){$res=$GLOBALS['_MysqliSvRes'];}		return mysqli_insert_id($res);}
	function mysql_affected_rows($res=NULL){	if(!$res){$res=$GLOBALS['_MysqliSvRes'];}		return mysqli_affected_rows($res);}
	function mysql_error($res=NULL){ if(!$res && isset($GLOBALS['_MysqliSvRes'])){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_error($res);}
	function mysql_errno($res=NULL){ if(!$res && isset($GLOBALS['_MysqliSvRes'])){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_errno($res);}
	function mysql_set_charset($query,$res=NULL){		if(!$res){$res=$GLOBALS['_MysqliSvRes'];}		return mysqli_set_charset($res,$query);	}
	function mysql_select_db($database_name,$res=NULL){	if(!$res){$res=$GLOBALS['_MysqliSvRes'];}		return mysqli_select_db($res,$database_name);}
	function mysql_real_escape_string($str,$res=NULL){ if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_real_escape_string($res,$str);}
	function mysql_escape_string($str){return mysqli_real_escape_string($GLOBALS['_MysqliSvRes'],$str);}
	function mysql_num_rows($res){return mysqli_num_rows($res);}
	function mysql_client_encoding($res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_character_set_name($res);}
	function mysql_create_db($db,$res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_query($res,"CREATE DATABASE `{$db}`");}
	function mysql_list_dbs($res=NULL){	if(!$res){$res=$GLOBALS['_MysqliSvRes'];} return mysqli_query($res,"SHOW DATABASES"); }
	function mysql_db_name($array,$i=0){return $array[$i];} //http://php.net/manual/en/function.mysql-db-name.php
	function mysql_db_query($db,$sql,$res=NULL){
		if(!$res){$res=$GLOBALS['_MysqliSvRes'];}
		mysqli_select_db ($res,$db) or mysqli_error($res);
		return mysqli_query($res,$sql);
	}
	function mysql_drop_db($db,$res=NULL){	return mysqli_query($res,"DROP `{$db}`"); }
	function mysql_fetch_lengths($res){ return mysqli_fetch_lengths($res);}
	function mysql_fetch_object($res,$classname='stdClass',$params=array()){ return mysqli_fetch_object($res,$classname,$params);}
	function mysql_field_flags($res,$field_offset=0){$o=mysqli_fetch_field_direct($res,$field_offset);	return sqli_h_flags2txt($o->flags);	}
	function mysql_field_len($res,$field_offset=0){$o=mysqli_fetch_field_direct($res,$field_offset); return $o->length;}
	function mysql_fetch_field($res,$offset=0){
		if(!mysqli_field_seek($res,$offset)){return false;}
		$obj= mysqli_fetch_field($res);
		$obj->not_null=0; //ok
		$obj->unique_key=0; //ok
		$obj->unsigned=0; //ok
		$obj->zerofill=0; //ok
		$obj->blob=0; //ok
		$obj->primary_key=0; //ok
		$obj->numeric=0;//ok
		$obj->multiple_key=0; //ok
		$obj->type_int=$obj->type;
		$obj->type=sqli_h_type2txt($obj->type);
		$obj->flags_str=sqli_h_flags2txt($obj->flags);
		$tmp=explode(' ',$obj->flags_str);
		foreach ($tmp as $val){
			if($val=='pri_key'){$val='primary_key';}
			elseif($val=='num'){$val='numeric';}
			if($val){$obj->$val=1;}
		}
		return $obj;
	}
	function mysql_field_name($res,$field=0){$obj=mysqli_fetch_field_direct($res,$field); return $obj->name;}
	function mysql_data_seek($res,$row_number){return  mysqli_data_seek($res,$row_number);}
	function mysql_field_seek($res,$field=0){ return mysqli_field_seek($res,$field);}
	function mysql_field_table($res,$field=0){ $obj=mysqli_fetch_field_direct($res,$field); return $obj->table;}
	function mysql_field_type($res,$field=0){ $obj=mysqli_fetch_field_direct($res,$field); return sqli_h_type2txt($obj->type);}
	function mysql_free_result($res){return mysqli_free_result($res);}
	function mysql_get_client_info(){return mysqli_get_client_info($GLOBALS['_MysqliSvRes']);}
	function mysql_get_host_info($res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_get_host_info($res);}
	function mysql_get_proto_info($res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_get_proto_info($res);}
	function mysql_get_server_info($res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_get_server_info($res);}
	function mysql_info($res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}	return mysqli_info($res);}
	function mysql_listfields($db,$table,$res=NULL){return mysql_list_fields($db,$table,$res);} //alias
	function mysql_list_fields($db,$table,$res=NULL){
		if(!$res){$res=$GLOBALS['_MysqliSvRes'];}
		return mysqli_query($res,"SHOW COLUMNS FROM `{$db}`.`{$table}`");
	}
	function mysql_list_processes($res=NULL){
		if(!$res){$res=$GLOBALS['_MysqliSvRes'];}
		return array(
			'Id' => mysqli_thread_id($res),
			'User' => NULL,
			'Host' => NULL,
			'db' => mysqli_fetch_assoc(mysqli_query($res,"SELECT DATABASE() as current_database"))['current_database'],
			'Command' => 'Processlist',
			'Time' => -1,
			'State' => NULL,
			'Info' => NULL,
			);
	}
	function mysql_listtables($db,$res=NULL){return mysql_list_tables($db,$res);}
	function mysql_list_tables($db,$res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];}return mysqli_query($res,"SHOW TABLES FROM `{$db}` ");}
	function mysql_num_fields($res){return mysqli_num_fields($res);}
	function mysql_ping($res){return mysqli_ping($res);}
	function mysql_result($result,$row,$field_name_or_offset=0){
		mysqli_data_seek($result,$row);
		//mysqli_field_seek($result,$field_name_or_offset);
		$data = mysqli_fetch_array($result);
		return $data[$field_name_or_offset];
	}
	function mysql_stat($res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];} return mysqli_stat($res);}
	function mysql_tablename($res,$i=0){mysqli_data_seek($res,$i);	$t=mysqli_fetch_row($res);	return $t[0];}//input: mysql_list_tables($db)
	function mysql_thread_id($res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];} return mysqli_thread_id($res);}
	function mysql_unbuffered_query($query,$res=NULL){if(!$res){$res=$GLOBALS['_MysqliSvRes'];} return mysqli_query($res,$query,MYSQLI_USE_RESULT);}

	function sqli_h_type2txt($type_id){
		// TODO: IMPLEMENT LIST DEPENDING ON SERVER VERSION
		// $cl_info=mysqli_get_client_info($GLOBALS['_MysqliSvRes']);
		// this came from http://php.net/manual/en/mysqli-result.fetch-field-direct.php
		$mysql_data_type_hash = array(
			0=>'decimal',
			1=>'tinyint',
			2=>'smallint',
			3=>'int',
//			3=>'blob',
			4=>'float',
			5=>'double',
			6=>'null',
			7=>'timestamp',
			8=>'bigint',
			9=>'mediumint',
			10=>'date',
			11=>'time',
			12=>'datetime',
			13=>'year',
			14=>'newdate',
			16=>'bit',
			//252 is currently mapped to all text and blob types (MySQL 5.0.51a)
			246=>'decimal',
			247=>'enum',
			248=>'set',
			249=>'tiny_blob',
			250=>'medium_blob',
			251=>'long_blob',
			252=>'blob',
			253=>'varchar',
			// 253=>'var_string',
			254=>'char',
			//254=>'string',
			255=>'geomery',
		);
		if(isset($mysql_data_type_hash[$type_id])==3){return $mysql_data_type_hash[$type_id];}
		$types = array();
		$constants = get_defined_constants(true);
		foreach ($constants['mysqli'] as $c => $n) if (preg_match('/^MYSQLI_TYPE_(.*)/', $c, $m)) $types[$n] = $m[1];
		$type= array_key_exists($type_id, $types)? strtolower($types[$type_id]) : NULL;
		return $type;
	}
	function sqli_h_flags2txt($flags_num){
		$flags = array();
		$constants = get_defined_constants(true);
		foreach ($constants['mysqli'] as $c => $n) if (preg_match('/MYSQLI_(.*)_FLAG$/', $c, $m)) if (!array_key_exists($n, $flags)) $flags[$n] = $m[1];
		$result = array();
		foreach ($flags as $n => $t) if ($flags_num & $n) $result[] = strtolower($t);
		return implode(' ', $result);
	}
}


?>