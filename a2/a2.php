<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
Query employees by ID, Name or Department: <br>
ID: <input type="text" name="emp_no">
Last Name: <input type="text" name="last_name">
Department: <input type="text" name="dept_name">
<input type="submit" name="Submit0">
</form>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
Query departments by employee count:
<input type="submit" name="Submit1">
</form>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
Query employees in a department ordered by tenure: <br>
Department: <input type="text" name="dept_name">
<input type="submit" name="Submit2">
</form>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
Query gender ratio in a department: <br>
Department: <input type="text" name="dept_name">
<input type="submit" name="Submit3">
</form>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
Query gender pay ratio in a department: <br>
Department: <input type="text" name="dept_name">
<input type="submit" name="Submit4">
</form>

<?php
	function output($Submit, $result, $sql)
	{
		// echo $sql;
		// echo "<br>";
		if ($result->num_rows > 0) {
			?>
			<table border="1" cellspacing="1" cellpadding="2">
			<tr>
				<?php
				$row = $result->fetch_assoc();
				foreach ($row as $key=>$value)
					{ 
						?>
						<th><?php echo "$key";?></th>
					<?php
					}
				?>
			</tr>
			<?php do{ ?>
				<tr>
				<?php
				foreach ($row as $key=>$value)
					{ 
						?>
						<td><?php echo "$value";?></td>
					<?php
					}
				?>
				</tr>	
			<?php } 
			while ($row = $result->fetch_assoc()); ?>
			</table>
			<?php
		} else {
			echo "0 results";
		}
	}
?>

<?php 
// first
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 // collect value of input field
	$emp_no = $_REQUEST['emp_no'];
	$last_name = $_REQUEST['last_name'];
	$dept_name = $_REQUEST['dept_name'];
	$Submit = $_REQUEST['Submit0'];
}

$servername = "localhost";
$username = "root";
$password = "0";
$dbname = "employees";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 
// echo "Connected successfully <br>";

$sql = "select employees.emp_no as Emp_No, employees.first_name as First_Name, employees.last_name as Last_Name, departments.dept_name as Department_Name from 
	((departments inner join dept_emp on departments.dept_no = dept_emp.dept_no) 
	inner join employees on dept_emp.emp_no=employees.emp_no) where departments.dept_name";

if (empty($dept_name))
{
	$sql .= " like \"%\" ";
}
else
{
	$sql .= " = \"$dept_name\" ";
}
if (!empty($emp_no))
{
	$q1 = " and employees.emp_no = " . $emp_no;
	$sql .= $q1;
	$flag = 1;
}
if (!empty($last_name))
{
	$q2 = " and employees.last_name = \"" . $last_name . "\"";
	$sql .= $temp .  $q2;
	$flag = 1;
}
$sql = $sql . ";";

if (isset($Submit))
{
	$result = $conn->query($sql);
	output($Submit, $result, $sql);
}
mysqli_close($conn);
?>


<?php
// second

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 // collect value of input field
	$Submit = $_REQUEST['Submit1'];
}

$servername = "localhost";
$username = "root";
$password = "0";
$dbname = "employees";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 
// echo "Connected successfully <br>";

$sql = "select departments.dept_name as Department_Name, count(t.dept_no) as No_of_Employees from (select dept_no from dept_emp) as t inner join departments on departments.dept_no=t.dept_no group by t.dept_no order by count(t.dept_no) desc;";

if (isset($Submit))
{
	$result = $conn->query($sql);
	output($Submit, $result, $sql);
}
mysqli_close($conn);
?>


<?php
// third

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 // collect value of input field
	$dept_name = $_REQUEST['dept_name'];
	$Submit = $_REQUEST['Submit2'];
}

$servername = "localhost";
$username = "root";
$password = "0";
$dbname = "employees";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 
// echo "Connected successfully <br>";

$sql = "select employees.emp_no as Emp_No, first_name as First_Name, last_name as Last_Name, t.Tenure_in_days from employees inner join (select dept_emp.emp_no, (to_date-from_date) as Tenure_in_days from dept_emp where dept_no in (select dept_no from departments where dept_name = \"$dept_name\")) as t on employees.emp_no=t.emp_no order by Tenure_in_days desc;";

if (isset($Submit))
{
	$result = $conn->query($sql);
	output($Submit, $result, $sql);
}
mysqli_close($conn);
?>

<?php
// fourth

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 // collect value of input field
	$dept_name = $_REQUEST['dept_name'];
	$Submit = $_REQUEST['Submit3'];
}

$servername = "localhost";
$username = "root";
$password = "0";
$dbname = "employees";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 
// echo "Connected successfully <br>";

$sql = "select female/male*1000 as Gender_Ratio from (select sum(case when gender=\"M\" then 1 else 0 end) as male, sum(case when gender=\"F\" then 1 else 0 end) as female from employees inner join (select dept_emp.emp_no from dept_emp where dept_no in (select dept_no from departments where dept_name = \"$dept_name\")) as t on employees.emp_no=t.emp_no) as t2;";

if (isset($Submit))
{
	$result = $conn->query($sql);
	output($Submit, $result, $sql);
}
mysqli_close($conn);
?>

<?php
// fourth

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 // collect value of input field
	$dept_name = $_REQUEST['dept_name'];
	$Submit = $_REQUEST['Submit4'];
}

$servername = "localhost";
$username = "root";
$password = "0";
$dbname = "employees";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 
// echo "Connected successfully <br>";

$sql = "select title as Title, female/male as Gender_Pay_Ratio from (select t4.title, sum(case when gender=\"M\" then t4.salary else 0 end) as male, sum(case when gender=\"F\" then t4.salary else 0 end) as female from (select avg(t3.salary) as salary, t3.gender, titles.title from (select salaries.salary, t2.emp_no, t2.gender from (select employees.emp_no, employees.gender from employees inner join (select dept_emp.emp_no from dept_emp where dept_no in (select dept_no from departments where dept_name = \"$dept_name\")) as t on employees.emp_no=t.emp_no) as t2 inner join salaries on salaries.emp_no=t2.emp_no) as t3 inner join titles on t3.emp_no=titles.emp_no group by title, gender) as t4 group by t4.title) as t5;";

if (isset($Submit))
{
	$result = $conn->query($sql);
	output($Submit, $result, $sql);
}
mysqli_close($conn);
?>