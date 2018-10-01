<html>
<body>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	Get district with most crime
	<input type="submit" name="Submit1">
	</form>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	Get most efficient police station
	<input type="submit" name="Submit2">
	</form>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	Get crime laws which are most and least uniquely applied in FIRs
	<input type="submit" name="Submit3">
	</form>


	<?php	

	echo extension_loaded("mongodb") ? "Loaded connection to mongodb successfully<br>" : "Connection to mongodb could not be loaded<bt>";
	$mng = new MongoDB\Driver\Manager("mongodb://localhost:27017");
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
 	// collect value of input field
		$Submit = $_REQUEST['Submit1'];
	}

	if (isset($Submit))
	{
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
		$rows = $mng->executeQuery('cases.pc', $query);
		foreach ($rows as $row ){
			if (array_key_exists($row->DISTRICT, $district_count)){
	            $district_count[$row->DISTRICT]+=1;            
				continue;
	        }
			$district_count[$row->DISTRICT] = 1;
		}
		echo nl2br("\nMaximum crimes in district: \n");
		print_r(array_search(max($district_count),$district_count));		
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
 	// collect value of input field
		$Submit = $_REQUEST['Submit2'];
	}
	if (isset($Submit))
	{
		// Second Task
		$pending_count = array();
		$filter = ["Status" => "Pending"];	
		$options = [];

		$query = new MongoDB\Driver\Query($filter,$options);

		$rows = $mng->executeQuery('cases.pc', $query);
		foreach ($rows as $row ){
			if (array_key_exists($row->PS, $pending_count)){
	            $pending_count[$row->PS]+=1;            
				continue;
	        }
			$pending_count[$row->PS] = 1;
		}
		echo nl2br("\nMaximum Pending Cases in: \n");
		print_r(array_search(max($pending_count),$pending_count));
		
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
 	// collect value of input field
		$Submit = $_REQUEST['Submit3'];
	}
	if (isset($Submit))
	{
		// Third Task
		$section_count = array();
		$filter = [];	
		$options = [];

		$query = new MongoDB\Driver\Query($filter,$options);

		$rows = $mng->executeQuery('cases.pc', $query);
		foreach ($rows as $row ){
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

	}

?>
</body>
</html>
