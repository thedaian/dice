<html>
<head>
<title>Dice Rolling Program</title>
<style type="text/css">
body {
	margin: 0 30%;
}
</style>
</head>
<body>
<form method="POST">
<input name="dice_string" type="text" value="2d6 + 3d8" />
<input type="submit" value="Roll!" />
</form>
<?php
if(isset($_POST['dice_string']))
{
	echo "<br>You rolled ".$_POST['dice_string'];
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