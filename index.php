<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
	<title>Phonebook</title>
	
	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	
	
	
</head>
<body>
<div class="container">
	<div class="row">
	    <form method="post" id="user_form"><input type="hidden" name="action" id="action" /></form>
		<div class="col-xs-4"><div class="input-group"> <span class="input-group-addon" id="sizing-addon2">&#x1F50D;</span> <input class="form-control" placeholder="Search..." id="search"> </div></div>
		<form method="post" id="user_form">
		<div class="col-xs-2"><button id="button" type="button" class="btn btn-default">Insert phone</button></div>
		<div class="col-xs-2"><input type="text" class="form-control" id="name" placeholder="name" required></div>
		<div class="col-xs-2"><input type="text" class="form-control" id="number" placeholder="number" required></div>
		<div class="col-xs-2"><input type="text" class="form-control" id="prefix" placeholder="prefix" required></div>
		</form>
	</div>
	<div class="row">
		<div class="col-xs-12" id="user_table">
		
		
			<!-- <table class="table">
				<thead> <tr> <th>#</th> <th>Name</th> <th>Prefix</th> <th>Phone</th> </tr> </thead>
				<tbody> 
				  <tr> <th scope="row">1</th> <td>Vaduva Constantin</td> <td>+40</td> <td>745688175</td> </tr>
				  <tr> <th scope="row">2</th> <td>Rode Calitate Dienstleistungs GmbH 	</td> <td>+39</td> <td>3067893210</td> </tr>
				  <tr> <th scope="row">3</th> <td>Rode Calitate Dienstleistungs GmbH 	</td> <td>+39</td> <td>3067894900</td> </tr>
				  <tr> <th scope="row">4</th> <td>Oskar Sudermann Simon-von-Utrecht</td> <td>+39</td> <td>40555660300</td> </tr>
				</tbody>
			</table> -->
		
		
		</div>
	</div>
</div>




<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="script.js"></script>

</body>
</html>