<?php

/*
Hi.

Use this link to make an array with the country prefix and name https://en.wikipedia.org/wiki/List_of_country_calling_codes
If you can't validate a prefix, then: 07, 02, 03 are Romanian, else german

00 will be converted to +
- and space will be repalced with NULL


Test with the following numbers the operations: Insert, attempt to duplicate insert, search, delete.
Insert should return the last insert id, and if duplicate found, it should return the original row id


RO:
+40-368-401454
0742601660
+40742601660
+401234

Rusia:
+79064313004

UA:
+380937687938
00380937687938

DE:
+495620233
004914281

04271952763
01725667130

+49-30-6789-4900
+49-30-6789-3210
+493374460196
+4933744707611
+493371621233

CN:
+86-28-85056019




*/

require_once("mysql_compatibility_php7.php");

class Phonebook {
	
	private static $prefix;
	private static $number;
	private static $name;
	private static $data=[];
	private static $phone_codes = [];
	public static $connect;  
	private static $host = "localhost";  
	private static $username = 'root';  
	private static $password = '123456';  
	private static $database='admin_new';
	
	public function __construct($phonenumber='',$name='') {
		//$this->database_connect();  
		if($phonenumber) {
			self::processPhone($phonenumber);
		}
		if($name) { 
			self::$name = $name;
		}
	}
	
	public static function database_connect()  
	{  
		 self::$connect = mysql_connect(self::$host, self::$username, self::$password, self::$database);  
		
		 
		 
	}  

	public static function checkDublicate($name,$prefix,$number) {
		  self::database_connect();
		  $sql = "SELECT * FROM all_phone_book WHERE name='".$name."' AND number='".$number."' AND prefix='".$prefix."' AND deleted=0 LIMIT 1";
		  $result = mysql_query($sql);
		  $found=mysql_num_rows( $result);
		if($found>0){
		  while($row = mysql_fetch_assoc($result)){

			  $output = $row['id'];
		  }
		}else{
			$output = 0;
		}
	  
		return $output;
	
	}

	/**
	* adding new phone numbers into db, table all_phone_book
	*/
	public static function addPhone($name,$number) {
		self::database_connect();
		$numb=self::processPhone($number);
		$dublicate=self::checkDublicate($name,$numb[0],$numb[1]);
		if($dublicate==0){
			$numb=self::processPhone($number);
			$name=mysql_real_escape_string($name);
			$sql = "INSERT INTO all_phone_book 
			(prefix, number, name) VALUES ('".$numb[0]."', 
				'".$numb[1]."',
				'".$name."')";   
		  $result = mysql_query($sql);
		$id=mysql_insert_id();
		return mysql_insert_id();
		}else{
			return $dublicate;
		}
        return "ok";
		
		//should return last insert id, or in case of dupplicate entry, it should return the id id the line
	}
	
	/** 
	* get phone number from db, tables all_phone_book
	*/	
	public static function getPhones($limit=50) { 
		self::database_connect();
		$output = ''; 
		$sql = 'SELECT * FROM all_phone_book WHERE deleted=0 ORDER BY id ASC LIMIT '.floor($limit);
		$result =mysql_query($sql);
		$output .= '<table class="table">
				<thead> <tr> <th>#</th> <th>Name</th> <th>Prefix</th> <th>Phone</th><th>command</th> </tr> </thead>';
		while($row =mysql_fetch_assoc($result ))  
		{ 
		$output .= '<tbody> 
		<tr> <th scope="row">'.$row['id'].'</th> <td>'.$row['name'].'</td> <td>'.$row['prefix'].'</td> <td>'.$row['number'].'</td> <td><div class="delete" data-id='.$row['id'].'>Delete</div></td> </tr>
		</tbody>';
	   }  
	    $output .= '</table>';  
	    return $output;  
	}
	
	/** 
	* check if phone number exists in db, table all_phone_book
	*/	
	public static function searchPhone($number) { //it should check the concatenated prefix and phone number
		self::database_connect();
		self::$number=$number;
		$prefix=substr(self::$number, 0, 2);
		if(!self::$number) {
			return false;
		}
		$sql = "SELECT * FROM all_phone_book WHERE number like '%".$number."%' OR prefix like '%".$prefix."%' AND deleted=0";
		
		$result = mysql_query($sql);
		$output .= '<table class="table">
				<thead> <tr> <th>#</th> <th>Name</th> <th>Prefix</th> <th>Phone</th><th>command</th> </tr> </thead>';
		while($row = mysql_fetch_assoc($result)){
			self::$data[] = $row;
			$output .= '<tbody> 
			<tr> <th scope="row">'.$row['id'].'</th> <td>'.$row['name'].'</td> <td>'.$row['prefix'].'</td> <td>'.$row['number'].'</td> <td><div class="delete" data-id='.$row['id'].'>Delete</div></td> </tr>
			</tbody>';
		}
		$output .= '</table>';  
		return $output;
	}
	
	/** 
	* make a relation to other modules, table all_phone_book_links
	*/
	public static function linkPhone($id,$table_id,$table_name) {
		if(!$id || !$table_id) {
			return false;
		}
		
		$sql = "INSERT INTO all_phone_book_links
		(phone_book_id, table_id, table_name) VALUES
				('".floor($id)."', 
				'".floor($table_id)."', 
				'".mysql_real_escape_string($table_name)."')";         ///this query is poorly written. the deleted column does not exist into all_phone_book_links 
		$result = mysql_query($sql);
		return $result;
	}
	
	/** 
	* remove a relation to other modules and mark number as deleted, in case when phone number need to delete from db, 
	* table all_phone_book_links
	*/
	public static function unlinkPhone($link_id) {
		self::database_connect();
		if (!$link_id) {
			return false;
		}
		$sql = "DELETE FROM all_phone_book_links WHERE phone_book_id='".floor($link_id)."'";
		$result = mysql_query($sql);
		if($result) {
			$query = "UPDATE all_phone_book SET deleted=1 WHERE id=".floor($link_id);
			$update_result = mysql_query($query);			
		}	
		return $update_result;
	}
	
	/** 
	* get all related to other modules phone numbers, tables all_phone_book, all_phone_book_links
	* Alex comment: should retrieve all the phones linked
	*/
	public static function getLinkedPhones($table_id,$table_name) {
		$sql = "SELECT * FROM all_phone_book as apb 
				LEFT JOIN all_phone_book_links as apbl 
				ON (apbl.phone_book_id=apb.id) 
				WHERE table_id = '".floor($table_id)."' 
				AND table_name = '".mysql_real_escape_string($table_name)."' ORDER BY apb.id ASC";
		$result = mysql_query($sql);
		while($row = mysql_fetch_assoc($result)){
			self::$data[] = $row;
        }
		return self::$data;		
	}
	
	/** 
	* all the rows from "all_phone_book_links" where is the all_phone_book.id present
	*/
	public static function getPhoneRefferencesId($id) {
		$sql = "SELECT * FROM all_phone_book_links as apbl 
				WHERE apbl.phone_book_id = '".floor($id)."'
				ORDER BY apbl.phone_book_id ASC";
		$result = mysql_query($sql);
		while($row = mysql_fetch_assoc($result)){
            self::$data[] = $row;
        }
		return self::$data;
	}
	
	/** 
	* all the rows from "all_phone_book_links" where is the all_phone_book.phone present 
	*/
	public static function getPhoneRefferencesByPhone($phone) {
		if(!$phone) {
			return false;
		}
		self::processPhone($phone);
		$sql = "SELECT *  
					FROM all_phone_book_links as apbl
					LEFT JOIN all_phone_book as apbb on apbb.id=apbl.phone_book_id
					WHERE apbl.phone_book_id IN( 
						SELECT apb.id  FROM all_phone_book as apb 
						WHERE apb.deleted = 0 
							AND (apb.number LIKE '".self::$number."%' OR apb.name LIKE '%".self::$name."%' )
					)";
		
		$result = mysql_query($sql);
		while($row = mysql_fetch_assoc($result)){
			self::$data[] = $row;
		}
		return self::$data;
	}
	
	/** 
	this should be the main parse function. It was not design correctly. This is where you shouls prove your selv!
	*/
	public static function processPhone($phone_str) {
		$ptn = "/^00/"; 
		$rpltxt = "+";
		
		$phone_str=preg_replace($ptn,$rpltxt ,$phone_str); /// this should be the other way arround! the leading 00 becomes + and the + will be later trimmed after the country prefix validation
		$phone_str=str_replace(array(' ','	','-'),array('','',''),trim($phone_str));
	
		
		
		 $check_number = preg_match('/\+|(00)/', $phone_str, $match);
		 $prefix;
		if($check_number == 1) { 
			$phone_str = preg_replace('/[^0-9]|(00)/', '', $phone_str);
			$nums = [];
		
			//comparing number with phone codes array 
			foreach (self::phoneCodes() as $val) {
				$test = preg_match_all('/(^'.$val['code'].')/', $phone_str, $matches);
				if($matches[0]) {
					$nums = $matches[0];
				}
			}
			
			if(count($nums) > 1) {
				$long_code = substr($phone_str, 0, strlen(max($nums)));
				if($check_number_length  < 5) {
					foreach ($nums as $val) {
						$prefix = $val;
					}
				} else {
					$prefix = $long_code;
				}
			} else {
				$prefix = $nums[0];
			}
		} else {
			
		 $local_code = substr($phone_str, 0, 2);
		 if($local_code == '02' || $local_code == '03' || $local_code == '07') {
		$prefix = 40;
		 } else {
			$prefix = 49;
			}
           $phone_str = $prefix.substr($phone_str, 2);
		}
		/**** if invalid number, then prefix is not defined *****/
		self::$number = substr($phone_str, strlen($prefix), strlen($phone_str));
		self::$prefix = '+'.$prefix;
		$phone = array(self::$prefix,self::$number);
		
		return $phone;
	}

	
	
	public static function getPrefix() {
		return self::$prefix;
	}
	
	public static function getNumber() {
		return self::$number;
	}
	
	public static function getName() {
		return self::$name;
	}
	
	public static function getData() {
		return self::$data;
	}
	
	public static function getPhoneCodes() {
		return self::phoneCodes();
	}
	
	public function phoneCodes () {
		return self::$phone_codes = [
			["code" => 1340, "country" =>  "United States Virgin Islands"],
			["code" => 1670, "country" =>  "Northern Mariana Islands"],
			["code" => 1671, "country" =>  "Guam"],
			["code" => 1684, "country" =>  "American Samoa"],
			["code" => 1787, "country" =>  "Puerto Rico"],
			["code" => 1242, "country" =>  "Bahamas"],
			["code" => 1246, "country" =>  "Barbados"],
			["code" => 1264, "country" =>  "Anguilla"],
			["code" => 1268, "country" =>  "Antigua and Barbuda"],
			["code" => 1284, "country" =>  "British Virgin Islands"],
			["code" => 1345, "country" =>  "Cayman Islands"],
			["code" => 1441, "country" =>  "Bermuda"],
			["code" => 1473, "country" =>  "Grenada"],
			["code" => 1649, "country" =>  "Turks and Caicos Islands"],
			["code" => 1658, "country" =>  "Jamaica"],
			["code" => 1664, "country" =>  "Montserrat"],
			["code" => 1721, "country" =>  "Sint Maarten"],
			["code" => 1758, "country" =>  "Saint Lucia"],
			["code" => 1767, "country" =>  "Dominica"],
			["code" => 1784, "country" =>  "Saint Vincent and the Grenadines"],
			["code" => 1809, "country" =>  "Dominican Republic"],
			["code" => 1829, "country" =>  "Dominican Republic"],
			["code" => 1849, "country" =>  "Dominican Republic"],
			["code" => 1868, "country" =>  "Trinidad and Tobago"],
			["code" => 1869, "country" =>  "Saint Kitts and Nevis"],
			["code" => 1876, "country" =>  "Jamaica"],
			["code" => 1939, "country" =>  "Puerto Rico"],
			["code" => 20, "country" =>  "Egypt"],
			["code" => 211, "country" =>  "South Sudan"],
			["code" => 212, "country" =>  "Morocco"],
			["code" => 213, "country" =>  "Algeria"],
			["code" => 216, "country" =>  "Tunisia"],
			["code" => 218, "country" =>  "Libya"],
			["code" => 220, "country" =>  "Gambia"],
			["code" => 221, "country" =>  "Senegal"],
			["code" => 222, "country" =>  "Mauritania"],
			["code" => 223, "country" =>  "Mali"],
			["code" => 224, "country" =>  "Guinea"],
			["code" => 225, "country" =>  "Ivory Coast"],
			["code" => 226, "country" =>  "Burkina Faso"],
			["code" => 227, "country" =>  "Niger"],
			["code" => 228, "country" =>  "Togo"],
			["code" => 229, "country" =>  "Benin"],
			["code" => 230, "country" =>  "Mauritius"],
			["code" => 231, "country" =>  "Liberia"],
			["code" => 232, "country" =>  "Sierra Leone"],
			["code" => 233, "country" =>  "Ghana"],
			["code" => 234, "country" =>  "Nigeria"],
			["code" => 235, "country" =>  "Chad"],
			["code" => 236, "country" =>  "Central African Republic"],
			["code" => 237, "country" =>  "Cameroon"],
			["code" => 238, "country" =>  "Cape Verde"],
			["code" => 239, "country" =>  "São Tomé and Príncipe"],
			["code" => 240, "country" =>  "Equatorial Guinea"],
			["code" => 241, "country" =>  "Gabon"],
			["code" => 242, "country" =>  "Republic of the Congo"],
			["code" => 243, "country" =>  "Democratic Republic of the Congo"],
			["code" => 244, "country" =>  "Angola"],
			["code" => 245, "country" =>  "Guinea-Bissau"],
			["code" => 246, "country" =>  "British Indian Ocean Territory"],
			["code" => 247, "country" =>  "Ascension Island"],
			["code" => 248, "country" =>  "Seychelles"],
			["code" => 249, "country" =>  "Sudan"],
			["code" => 250, "country" =>  "Rwanda"],
			["code" => 251, "country" =>  "Ethiopia"],
			["code" => 252, "country" =>  "Somalia"],
			["code" => 253, "country" =>  "Djibouti"],
			["code" => 254, "country" =>  "Kenya"],
			["code" => 255, "country" =>  "Tanzania"],
			["code" => 25524, "country" =>  "Zanzibar"],
			["code" => 256, "country" =>  "Uganda"],
			["code" => 257, "country" =>  "Burundi"],
			["code" => 258, "country" =>  "Mozambique"],
			["code" => 260, "country" =>  "Zambia"],
			["code" => 261, "country" =>  "Madagascar"],
			["code" => 262, "country" =>  "Réunion"],
			["code" => 262269, "country" =>  "Mayotte"], 
			["code" => 262639, "country" =>  "Mayotte"],
			["code" => 263, "country" =>  "Zimbabwe"],
			["code" => 264, "country" =>  "Namibia"],
			["code" => 265, "country" =>  "Malawi"],
			["code" => 266, "country" =>  "Lesotho"],
			["code" => 267, "country" =>  "Botswana"],
			["code" => 268, "country" =>  "Eswatini"],
			["code" => 269, "country" =>  "Comoros"], 
			["code" => 27, "country" =>  "South Africa"],
			["code" => 290, "country" =>  "Saint Helena"],
			["code" => 2908, "country" =>  "Tristan da Cunha"],
			["code" => 291, "country" =>  "Eritrea"],
			["code" => 297, "country" =>  "Aruba"],
			["code" => 298, "country" =>  "Faroe Islands"],
			["code" => 299, "country" =>  "Greenland"],
			["code" => 30, "country" =>  "Greece"],
			["code" => 31, "country" =>  "Netherlands"],
			["code" => 32, "country" =>  "Belgium"],
			["code" => 33, "country" =>  "France"],
			["code" => 34, "country" =>  "Spain"],
			["code" => 350, "country" =>  "Gibraltar"],
			["code" => 351, "country" =>  "Portugal"],
			["code" => 352, "country" =>  "Luxembourg"],
			["code" => 353, "country" =>  "Ireland"],
			["code" => 354, "country" =>  "Iceland"],
			["code" => 355, "country" =>  "Albania"],
			["code" => 356, "country" =>  "Malta"],
			["code" => 357, "country" =>  "Cyprus"],
			["code" => 358, "country" =>  "Finland"],
			["code" => 35818, "country" =>  "Åland Islands"],
			["code" => 359, "country" =>  "Bulgaria"],
			["code" => 36, "country" =>  "Hungary"], 
			["code" => 370, "country" =>  "Lithuania"], 
			["code" => 371, "country" =>  "Latvia"], 
			["code" => 372, "country" =>  "Estonia"], 
			["code" => 373, "country" =>  "Moldova"], 
			["code" => 374, "country" =>  "Armenia"], 
			["code" => 37447, "country" =>  "Artsakh"], 
			["code" => 37497, "country" =>  "Artsakh"], 
			["code" => 375, "country" =>  "Belarus"],
			["code" => 376, "country" =>  "Andorra"], 
			["code" => 377, "country" =>  "Monaco"], 
			["code" => 378, "country" =>  "San Marino"], 
			["code" => 379, "country" =>  "Vatican City"], 
			["code" => 380, "country" =>  "Ukraine"],
			["code" => 381, "country" =>  "Serbia"],
			["code" => 382, "country" =>  "Montenegro"],
			["code" => 383, "country" =>  "Kosovo"],
			["code" => 385, "country" =>  "Croatia"],
			["code" => 386, "country" =>  "Slovenia"],
			["code" => 387, "country" =>  "Bosnia and Herzegovina"],
			["code" => 389, "country" =>  "North Macedonia"],
			["code" => 39, "country" =>  "Italy"],
			["code" => 3906698, "country" => "Vatican City"], 
			["code" => 390549, "country" =>  "San Marino"],
			["code" => 40, "country" =>  "Romania"],
			["code" => 41, "country" =>   "Switzerland"],
			["code" => 4191, "country" =>  "Italy"], 
			["code" => 420, "country" =>  "Czech Republic"],
			["code" => 421, "country" =>  "Slovakia"],
			["code" => 423, "country" =>  "Liechtenstein"],
			["code" => 43, "country" =>  "Austria"],
			["code" => 44, "country" =>  "United Kingdom"],
			["code" => 441481, "country" =>  "Guernsey"],
			["code" => 441534, "country" =>  "Jersey"],
			["code" => 441624, "country" =>  "Isle of Man"],
			["code" => 45, "country" =>  "Denmark"],
			["code" => 46, "country" =>  "Sweden"],
			["code" => 47, "country" =>  "Norway"],
			["code" => 4779, "country" =>  "Svalbard"],
			["code" => 48, "country" =>  "Poland"],
			["code" => 49, "country" =>  "Germany"],
			["code" => 500, "country" =>  "Falkland Islands"],
			["code" => 500, "country"  =>  "South Georgia and the South Sandwich Islands"],
			["code" => 501, "country" =>  "Belize"],
			["code" => 502, "country" =>  "Guatemala"],
			["code" => 503, "country" =>  "El Salvador"],
			["code" => 504, "country" =>  "Honduras"],
			["code" => 505, "country" =>  "Nicaragua"],
			["code" => 506, "country" =>  "Costa Rica"],
			["code" => 507, "country" =>  "Panama"],
			["code" => 508, "country" =>  "Saint-Pierre and Miquelon"],
			["code" => 509, "country" =>  "Haiti"],
			["code" => 51, "country" =>  "Peru"],
			["code" => 52, "country" =>  "Mexico"],
			["code" => 53, "country" =>  "Cuba"],
			["code" => 54, "country" =>  "Argentina"],
			["code" => 55, "country" =>  "Brazil"],
			["code" => 56, "country" =>  "Chile"],
			["code" => 57, "country" =>  "Colombia"],
			["code" => 58, "country" =>  "Venezuela"],
			["code" => 590, "country" =>  "Guadeloupe"],
			["code" => 591, "country" =>  "Bolivia"],
			["code" => 592, "country" =>  "Guyana"],
			["code" => 593, "country" =>  "Ecuador"],
			["code" => 594, "country" =>  "French Guiana"],
			["code" => 595, "country" =>  "Paraguay"],
			["code" => 596, "country" =>  "Martinique"],
			["code" => 597, "country" =>  "Suriname"],
			["code" => 598, "country" =>  "Uruguay"],
			["code" => 5993, "country" =>  "Sint Eustatius"],
			["code" => 5994, "country" =>  "Saba"],
			["code" => 5997, "country" =>  "Bonaire"],
			["code" => 5999, "country" =>  "Curaçao"],
			["code" => 60, "country" =>  "Malaysia"],
			["code" => 61, "country" =>  "Australia"],
			["code" => 6189162, "country" =>  "Cocos Islands"],
			["code" => 6189164, "country" =>  "Christmas Island"],
			["code" => 62, "country" =>  "Indonesia"],
			["code" => 63, "country" =>  "Philippines"],
			["code" => 64, "country" =>  "New Zealand"],
			["code" => 64, "country" =>  "Pitcairn Islands"],
			["code" => 66, "country" =>  "Thailand"],
			["code" => 670, "country" =>  "East Timor"],
			["code" => 672, "country" => "Australian External Territories"],
			["code" => 6721, "country" => "Australia Australian Antarctic Territory"],
			["code" => 6723, "country" =>  "Norfolk Island"],
			["code" => 673, "country" =>  "Brunei"],
			["code" => 674, "country" =>  "Nauru"],
			["code" => 675, "country" =>  "Papua New Guinea"],
			["code" => 676, "country" =>  "Tonga"],
			["code" => 677, "country" =>  "Solomon Islands"],
			["code" => 678, "country" =>  "Vanuatu"],
			["code" => 679, "country" =>  "Fiji"],
			["code" => 680, "country" =>  "Palau"],
			["code" => 681, "country" =>  "Wallis and Futuna"],
			["code" => 682, "country" =>  "Cook Islands"],
			["code" => 683, "country" =>  "Niue"],
			["code" => 685, "country" =>  "Samoa"],
			["code" => 686, "country" =>  "Kiribati"],
			["code" => 687, "country" =>  "New Caledonia"],
			["code" => 688, "country" =>  "Tuvalu"],
			["code" => 689, "country" =>  "French Polynesia"],
			["code" => 690, "country" =>  "Tokelau"],
			["code" => 691, "country" =>  "Federated States of Micronesia"],
			["code" => 692, "country" =>  "Marshall Islands"],
			["code" => 7, "country" =>  "Russia"],
			["code" => 7, "country" =>  "Kazakhstan"],
			["code" => 7840, "country" =>  "Abkhazia"],
			["code" => 7940, "country" =>  "Abkhazia"],
			["code" => 81, "country" =>  "Japan"],
			["code" => 82, "country" =>  "South Korea"],
			["code" => 84, "country" =>  "Vietnam"],
			["code" => 850, "country" =>  "North Korea"],
			["code" => 852, "country" =>  "Hong Kong"],
			["code" => 853, "country" =>  "Macau"],
			["code" => 855, "country" =>  "Cambodia"],
			["code" => 856, "country" =>  "Laos"],
			["code" => 86, "country" =>  "China"],
			["code" => 880, "country" =>  "Bangladesh"],
			["code" => 886, "country" =>  "Taiwan"],
			["code" => 90, "country" =>  "Turkey"],
			["code" => 90392, "country" =>  "Northern Cyprus"],
			["code" => 91, "country" =>  "India"],
			["code" => 92, "country" =>  "Pakistan"],
			["code" => 92582, "country" =>  "Azad Kashmir"],
			["code" => 92581, "country" =>  "Gilgit Baltistan"],
			["code" => 93, "country" =>  "Afghanistan"],
			["code" => 94, "country" =>  "Sri Lanka"],
			["code" => 95, "country" =>  "Myanmar"],
			["code" => 960, "country" =>  "Maldives"],
			["code" => 961, "country" =>  "Lebanon"],
			["code" => 962, "country" =>  "Jordan"],
			["code" => 963, "country" =>  "Syria"],
			["code" => 964, "country" =>  "Iraq"],
			["code" => 965, "country" =>  "Kuwait"],
			["code" => 966, "country" =>  "Saudi Arabia"],
			["code" => 967, "country" =>  "Yemen"],
			["code" => 968, "country" =>  "Oman"],
			["code" => 970, "country" =>  "Palestine"],
			["code" => 971, "country" =>  "United Arab Emirates"],
			["code" => 972, "country" =>  "Israel"],
			["code" => 973, "country" =>  "Bahrain"],
			["code" => 974, "country" =>  "Qatar"],
			["code" => 975, "country" =>  "Bhutan"],
			["code" => 976, "country" =>  "Mongolia"],
			["code" => 977, "country" =>  "Nepal"],
			["code" => 98, "country" =>  "Iran"],
			["code" => 992, "country" =>  "Tajikistan"],
			["code" => 993, "country" =>  "Turkmenistan"],
			["code" => 994, "country" =>  "Azerbaijan"],
			["code" => 995, "country" =>  "Georgia"],
			["code" => 99534, "country" =>  "South Ossetia"],
			["code" => 99544, "country" =>  "Abkhazia"],
			["code" => 996, "country" =>  "Kyrgyzstan"],
			["code" => 997, "country" =>  "Kazakhstan"],
			["code" => 998, "country" =>  "Uzbekistan"]];
		}
}