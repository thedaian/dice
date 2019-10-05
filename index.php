<html>
<head>
<title>Dice Rolling Program</title>
<style type="text/css">
body {
	margin: 20px 30%;
}
table {
	width: 100%;
}
td {
	text-align: center;
}
</style>
</head>
<body>
<h1><a href="index.php">Die Roll Example</a></h1>
<form method="POST">
<input name="dice_string" type="text" value="2d6 + 3d8" />
<input type="submit" value="Roll!" />
</form>
<!--<form method="POST">
<input name="tests" type="hidden" value="true" />
<input type="submit" value="Run Tests!" />
</form>-->
<a href="?list_rolls=true">List Rolls</a>
<hr>
<?php
/**
create_db()

establishes a database connection, using PDO. $pdo object is static, and should return the same connection if called again in the code
*/
function create_db()
{
	static $pdo = NULL;
	
	if($pdo == NULL)
	{
		$host = 'localhost';
		$db   = 'dice';
		$user = 'root';
		$pass = '';
		$charset = 'utf8mb4';
		
		$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
		try {
			$pdo = new PDO($dsn, $user, $pass, $options);
		} catch (\PDOException $e) {
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
	}
	
	return $pdo;
}

/**
die_to_array(string, sign)
$s: string input in the form of <X>d<Y>, where X is the number of die and Y is the number of sides
$sign: string or character input with the sign of the current math function.

Returns an array containing each individual die roll, as well as the math value to be used when calculating the final amount
*/
function die_to_array($s, $sign)
{
	$dices = array();
	$def = array();
	$def = explode("d", $s);
	if(empty($def[0]))
	{
		$def[0] = 1;
	}
	for($number_of_die = 0; $number_of_die < $def[0]; $number_of_die++)
	{
		//this rolls the value here, for testing purposes, it might end up being better to move this to the `process_roll` function
		array_push($dices, rand(0, $def[1]));
		if(!empty($sign) && $number_of_die < $def[0] - 1)
		{
			array_push($dices, $sign);
		}
	}
	return $dices;
}

/**
split_into_array(string)
$s: string containing the input of multiple die rolls and individual numbers: such as "2d6 + 3d8" or "d6 + 3"

Returns an array which can be iterated through to generate the final result, with number then math symbol then number (and so on until the last number)
*/
function split_into_array($s)
{
	$equation = array();
	//this is done to make sure that + and - signs are surrounded by a space, since there is a chance that the input could contain little or no spaces, such as "2d6+3d8"
	$s = str_replace(array("+", "-", "*", "/"), array(" + ", " - ", " * ", " / "), $s);
	$equation = explode(" ", $s);
	//since we added spaces to the initial string, we have to remove any empty array elements here. we also have to pass it through array_values to reindex the keys properly
	$equation = array_values(array_filter($equation, function($v) { return !empty($v); }));

	for($i = 0; $i < count($equation); $i++)
	{
		if(substr_count($equation[$i], "d") > 0)
		{
			$sign = "+";
			if($i > 0)
			{
				$sign = $equation[$i - 1];
			}
			array_splice( $equation, $i, 1, die_to_array($equation[$i], $sign) );
		}
	}
	
	return $equation;
}

/**
process_roll(string)
$input:  string containing the input of multiple die rolls and individual numbers: such as "2d6 + 3d8" or "d6 + 3"

Takes the input, and processes the final results. Prints the answer. Additionally, establishes a MySQL database connection and inserts the values into storage there.
*/
function process_roll($input)
{
	$s_result = "";
	$final_number = 0;
	
	$pdo = create_db();
	
	$die_list = split_into_array($input);
	$final_number = $die_list[0];
	$s_result = implode("", $die_list);
	
	for($i = 1; $i < count($die_list); $i++)
	{
		if($die_list[$i] === "+")
		{
			$i++;
			$final_number += intval($die_list[$i]);
		}
		if($die_list[$i] === "-")
		{
			$i++;
			$final_number += intval($die_list[$i]);
		}
		//it's a bit silly to include multiplcation and division as die rolls, but it was also really easy to include, so I added them anyway
		if($die_list[$i] === "*")
		{
			$i++;
			$final_number *= intval($die_list[$i]);
		}
		if($die_list[$i] === "/")
		{
			$i++;
			$final_number /= intval($die_list[$i]);
		}
	}
	$pdo->prepare('INSERT INTO rolls VALUES(NOW(), ?, ?, ?)')->execute([$input, $s_result, $final_number]);
	
	
	printf("Input: %s<br/>Result: %s = %s<hr/>", $input, $s_result, $final_number);
}
//processes the basic input
if(isset($_POST['dice_string']))
{
	process_roll($_POST['dice_string']);
}

//displays a table with all the rolls
if(isset($_GET['list_rolls']))
{
	$pdo = create_db();
	$stmt = $pdo->query('SELECT * FROM rolls');
	
	echo '<h2>Showing all rolls</h2>';
	echo '<table><tr><th>Time of roll</th><th>Roll Input</th><th>Roll Result</th></th>';
	
	foreach($stmt as $row)
	{
		printf('<tr><td>Roll at %s</td>
			<td><a title="Show in descensing order" href="?highest_lowest=%s">Input: %s</a></td>
			<td>Result: %s = <strong>%s</strong></td></tr>', $row['time'], urlencode($row['raw']), $row['raw'], $row['string_result'], $row['final_number']);
	}
	
	echo '</table>';
}

//displays a table of specific inputs, and sorts the final tally result from highest to lowest
if(isset($_GET['highest_lowest']))
{
	$pdo = create_db();
	$stmt = $pdo->prepare('SELECT * FROM rolls WHERE raw=? ORDER BY final_number DESC');
	$stmt->execute([$_GET['highest_lowest']]);
	
	echo '<h2>Showing all rolls of ' . $_GET['highest_lowest'] . ' in descensing result order</h2>';
	echo '<table><tr><th>Time of roll</th><th>Roll Input</th><th>Roll Result</th></th>';
	
	while ($row = $stmt->fetch())
	{
		printf('<tr><td>Roll at %s</td>
			<td>Input: %s</td>
			<td>Result: %s = <strong>%s</strong></td></tr>', $row['time'], $row['raw'], $row['string_result'], $row['final_number']);
	}
	
	echo '</table>';
}
/*
//this is no longer useful, as the input happens too fast, and MySQL will raise an error
if(isset($_POST['tests']))
{
	process_roll("2d6 + 3d8");
	process_roll("d6 + 3");
	process_roll("3d6+1d12+4d10");
	process_roll("d6 - 4d12 + 2d100");
	process_roll("d6 * 4d12 / 2d100");
	process_roll("6 - 4j12 + 2d100");
}*/
?>
</body>