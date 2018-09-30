<html>
<body>
<?php

	echo extension_loaded("mongodb") ? "loaded\n" : "not loaded\n";
	$mng = new MongoDB\Driver\Manager("mongodb://localhost:27017");

	echo "Connection to database successfully\n";

	// First Task 
	// Find all district names and corresponding case count

	$filter = [];
	// To limit to 10 queries use following 
	$options = [];	
	// $options = [];	
	$query = new MongoDB\Driver\Query($filter,$options);
	//empty query implies whole data set
	//array for district, count
	$district_count = array();
	//echo nl2br("\nThe query was created\n");

	$rows = $mng->executeQuery('cases.pc', $query);
	foreach ($rows as $row ){
		// print_r($row->DISTRICT);
		if (array_key_exists($row->DISTRICT, $district_count)){
            $district_count[$row->DISTRICT]+=1;            
			continue;
        }
		$district_count[$row->DISTRICT] = 1;
	}
	echo nl2br("\nMaximum crimes in district: \n");
	print_r(array_search(max($district_count),$district_count));

	// Second Task
	$pending_count = array();
	$filter = ["Status" => "Pending"];	
	$options = [];

	$query = new MongoDB\Driver\Query($filter,$options);

	$rows = $mng->executeQuery('cases.pc', $query);
	foreach ($rows as $row ){
		// print_r($row->PS);
		// echo "";
		if (array_key_exists($row->PS, $pending_count)){
            $pending_count[$row->PS]+=1;            
			continue;
        }
		$pending_count[$row->PS] = 1;
	}
	echo nl2br("\nMaximum Pending Cases in: \n");
	print_r(array_search(max($pending_count),$pending_count));


	// Third Task
	$section_count = array();
	$filter = [];	
	$options = [];

	$query = new MongoDB\Driver\Query($filter,$options);

	$rows = $mng->executeQuery('cases.pc', $query);
	foreach ($rows as $row ){
		// print_r($row->PS);
		// echo "";
		// print_r($row->Act_Section[0]);
		foreach ($row->Act_Section as $i)
		{
			if (array_key_exists($i, $section_count)){
            	$section_count[$i]+=1;            
				continue;
        	}
			$section_count[$i] = 1;
		}
	}
	echo nl2br("\nMaximum Cases under Section: \n");
	$x = array_search(max($section_count),$section_count);
	print_r($x);
	echo nl2br("\nNumber of cases in section $x: ");
	print_r($section_count[array_search(max($section_count),$section_count)]);
	// print_r($pending_count)
	// $query = new MongoDB\Driver\Query($filter,$options); 
	// $rows = $mng->executeQuery('cases.pc', $query);

	// echo '<pre>' . count($rows->toArray()) . '</pre>';

?>
</body>
</html>