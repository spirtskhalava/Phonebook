<?php  
include 'phonebook.class.php';  

 if(isset($_POST["action"]))  
 {  
      if($_POST["action"] == "Load")  
      {  
           echo Phonebook::getPhones(10);  
      } 
      if($_POST["action"] == "Insert")  
      {  
           $first_name = mysqli_real_escape_string($object->connect, $_POST["first_name"]);  
           $last_name = mysqli_real_escape_string($object->connect, $_POST["last_name"]);  
           $image = $object->upload_file($_FILES["user_image"]);  
           $query = "  
           INSERT INTO users  
           (first_name, last_name, image)   
           VALUES ('".$first_name."', '".$last_name."', '".$image."')  
           ";  
           $object->execute_query($query);  
           echo 'Data Inserted';  
      }  
 }  

 if(isset($_POST["id"]))  {
     $deleteid=$_POST["id"];
     echo Phonebook::unlinkPhone($deleteid); 
     

 }
 if(isset($_POST["number"]))  {
    $number=$_POST["number"];
    echo Phonebook::searchPhone($number); 
    

}
if(isset($_POST["name"]) && isset($_POST["num"]))  {
     $name=$_POST["name"];
     $number=$_POST["num"];
     echo Phonebook::addPhone($name,$number); 

 }
 ?>  