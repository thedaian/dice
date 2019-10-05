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
//turns a "xdy" text string into an array of die values
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
		array_push($dices, rand(0, $def[1]));
		if(!empty($sign) && $number_of_die < $def[0] - 1)
		{
			array_push($dices, $sign);
		}
	}
	return $dices;
}

function split_into_array($s)
{
	$equation = array();
	//this is done to make sure that + and - signs are surrounded by a space, since there is a chance that the input could contain little or no spaces, such as "2d6+3d8"
	$s = str_replace(array("+", "-"), array(" + ", " - "), $s);
	$equation = explode(" ", $s);
	$equation = array_filter($equation, function($v) { return !empty($v); }); //since we added spaces to the initial string, we have to remove any empty array elements here
	
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

function process_roll($input)
{
	echo $input . '<br>';
	
	$die_list = split_into_array($input);
	$result = $die_list[0];
	$s_result = implode("", $die_list);
	
	for($i = 1; $i < count($die_list); $i++)
	{
		if($die_list[$i] === "+")
		{
			$i++;
			$result += intval($die_list[$i]);
		}
		if($die_list[$i] === "-")
		{
			$i++;
			$result += intval($die_list[$i]);
		}
	}
	var_dump($die_list);
	
	echo $s_result . '<br>';
	echo $result . '<br>';
}

if(isset($_POST['dice_string']))
{
	echo "<br>You rolled ".$_POST['dice_string'];
}

//if(isset($_POST['tests']))
//{
	process_roll("2d6 + 3d8");
	process_roll("d6 + 3");
	process_roll("d6 + 1d12 + 2d100");
	process_roll("3d6+1d12+4d10");
	process_roll("d6 - 4d12 + 2d100");
//}
/*Write a program that calculates a dice addition equation. (done)

The program will need to store the equation and result so it can be recalled in the future (in case the Game Master didn't see you roll).

The program should also be able to sort the highest and lowest rolls based on all stored values.

Some Examples:

2d6 + 3d8 => d6 + d6 + d8 + d8 + d8 => 5...36
d6 + 3 => d6 + 3 => 4...9
d6 + 1d12 + 2d100 => d6 + d12 + d100 + d100 => 4...218

Extra Credit
Testing. Build some test cases around the problem (done-ish)
Subtraction. Build some equations that work with subtracting values. (done)
Verbosity. Have the output print every die roll (done)
d6 + 1d12 + 2d100
1 + 5 + 83 + 47
136*/


?>
</body>