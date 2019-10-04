<html>
<head>
<title>Dice Rolling Program</title>
<style type="text/css">
body {
	margin: 20px 30%;
}
</style>
</head>
<body>
<form method="POST">
<input name="dice_string" type="text" value="2d6 + 3d8" />
<input type="submit" value="Roll!" />
</form>
<br><hr><br>
<form method="POST">
<input name="tests" type="hidden" value="true" />
<input type="submit" value="Run Tests!" />
</form>
<?php
function split_into_array($s)
{
	$array = [];
	$opers = [];
	/*$next = 0;
	$sign = "";
	do
	{
		$plus = stripos($s, "+");
		$neg = stripos($s, "-");
		if(($plus !== FALSE && $neg !== FALSE && $plus < $neg) || ($plus !== FALSE && $neg === FALSE))
		{
			$next = $plus - 1;
			$sign = "+";
		} else if(($neg !== FALSE && $plus !== FALSE && $neg < $plus) || ($neg !== FALSE && $plus === FALSE))
		{
			$next = $neg - 1;
			$sign = "-";
		} else {
			$next = strlen($s);
			$sign = "";
		}
		array_push($array, trim(substr($s, 0, $next)));
		if(!empty($sign))
		{
			array_push($array, $sign);
		}
		if($next > 0)
		{
			$s = substr($s, $next + 2);
		}
	} while(strlen($s) > 0);*/
	if(substr_count($s, "+"))
	{
		$array = explode("+", $s);
		array_fill($opers, count($array - 1), "+");
	}
	if(substr_count($s, "-"))
	{
		$array = explode("-", $s);
		array_fill($opers, count($array - 1), "-");
	}
	return $array;
}

function process_roll($input)
{
	$die_list = split_into_array($input);
	var_dump($die_list);
}

if(isset($_POST['dice_string']))
{
	echo "<br>You rolled ".$_POST['dice_string'];
}

if(isset($_POST['tests']))
{
	process_roll("2d6 + 3d8");
	process_roll("d6 + 3");
	process_roll("d6 + 1d12 + 2d100");
	process_roll("d6 - 1d12 + 2d100");
}
/*Write a program that calculates a dice addition equation.

The program will need to store the equation and result so it can be recalled in the future (in case the Game Master didn't see you roll).

The program should also be able to sort the highest and lowest rolls based on all stored values.

Some Examples:

2d6 + 3d8 => d6 + d6 + d8 + d8 + d8 => 5...36
d6 + 3 => d6 + 3 => 4...9
d6 + 1d12 + 2d100 => d6 + d12 + d100 + d100 => 4...218

Extra Credit
Testing. Build some test cases around the problem
Subtraction. Build some equations that work with subtracting values.
Verbosity. Have the output print every die roll
d6 + 1d12 + 2d100
1 + 5 + 83 + 47
136*/


?>
</body>