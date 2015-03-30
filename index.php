<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Demo</title>
<link rel="stylesheet" href="css/ex.css" type="text/css" />
</head>
<body>

<?php

$rets_login_url = "http://www.dis.com:6103/rets/login";
$rets_username = "Joe";
$rets_password = "Schmoe";

//////////////////////////////

//require_once("phrets.php");
require 'vendor/autoload.php';
require('includes/html_table.class.php');
// start rets connection
$rets = new phRETS;

// Uncomment and change the following if you're connecting
// to a server that supports a version other than RETS 1.5

//$rets->AddHeader("RETS-Version", "RETS/1.7.2");

echo "+ Connecting to {$rets_login_url} as {$rets_username}<br>\n";
$connect = $rets->Connect($rets_login_url, $rets_username, $rets_password);

// check for errors
if ($connect) {
        echo "  + Connected<br>\n";
}
else {
        echo "  + Not connected:<br>\n";
        print_r($rets->Error());
        exit;
}

echo '<br />';
$types = $rets->GetMetadataTypes();
echo 'Resources';
d($types);

/* Get table layout */

$class = $rets->GetMetadataTable("Property", "COM");
echo 'Class COM - '.$types[0]['Data'][0]['StandardName'];
d($class);
/*
$class = $rets->GetMetadataTable("Property", "RES");
echo 'Class RES - '.$types[0]['Data'][1]['StandardName'];
d($class);
*/

/* Search RETS server */
$rets_modtimestamp_field = "ModificationTimestamp";
$old_time = "1900-01-01T00:00:00";
$query =  "({$rets_modtimestamp_field}={$old_time}+)";

$search = $rets->SearchQuery(
	"Property", 
	"COM", 
	$query, 
	array('Limit' => 20, 
		'Format' => 'COMPACT-DECODED', 
		'Select'=> 'BuildingName,YearBuilt,SqFt,ListPrice,ListDate',
		'Count' => 1
		));	
	
if($rets->TotalRecordsFound() > 0) {

		$tbl = new HTML_Table('', 'demoTbl');
		$tbl->addCaption('All records in COM', 'cap', array('id'=> 'tblCap') );

		$tbl->addRow();

		$fields = $rets->SearchGetFields($search);
		// loop through each field in the response and pull it's value
		foreach ($fields as $field) {
			 $tbl->addCell($field, '', 'header');    
		}
		// loop through each record 
		while($data = $rets->FetchRow($search)) {
			$tbl->addRow();
			foreach($data as $x => $x_value) {
					$tbl->addCell($x_value);			
				}
		}		   
		        
		echo $tbl->display();
		
	} 
	else {
		echo '0 Records Found';
	}
	

$rets->FreeResult($search);
echo '<br />';
echo "+ Disconnecting<br>\n";
$rets->Disconnect();

?>

</body>
</html>