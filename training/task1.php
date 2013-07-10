<?php

//phpinfo();

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function SplitEmailAddress($email)
{
	return explode("@", $email);
}
echo "<h4>SplitEmailAddress</h4>";
$userdomain = SplitEmailAddress("zengjin@baixing.net");
echo $userdomain[0] . " @ " . $userdomain[1] . "<br />";
/*
function GeneratePassword($len, $str)
{
	if ($len < 0 or strlen($str) <= 0)
		return null;
	$max = strlen($str) - 1;
	$ret = "";
	for ($i = 0; $i < $len; $i++)
		$ret .= $str[rand(0, $max)];
	return $ret;
}
*/
function GeneratePassword($len, $str)
{
	/*if ($len < 0 or strlen($str) <= 0)
		return null;
	$ret = str_shuffle($str);
	while (strlen($ret) < $len)
	{
		$ret = $ret . str_shuffle($str);
	}
	return $ret;
	*/
	return substr(str_shuffle(str_pad($str, $len, $str)), 0, $len);
}
echo "<h4>GeneratePassword</h4>";
echo GeneratePassword(-5, "abc") . "<br />";
echo GeneratePassword(0, "abc") . "<br />";
echo GeneratePassword(2, "") . "<br />";
echo GeneratePassword(1, null) . "<br />";
echo GeneratePassword(3, "abac") . "<br />";
echo GeneratePassword(6, "abac") . "<br />";
echo GeneratePassword(4, "abac") . "<br />";

function GetLongestString()
{
	return max(array_map("strlen", func_get_args()));
}
echo "<h4>GetLongestString</h4>";
echo GetLongestString("abc", "a", "abcde", "abcd") . "<br />";
echo GetLongestString() . "<br />";
echo GetLongestString("") . "<br />";
$time_start = microtime_float();
for ($i = 0; $i < 100000; $i++)
	GetLongestString("abc", "a", "abcde", "abcd", "abc", "saea", "abcddfse", "absdcd", "abcasdfgasdgasasd", "sadfa", "abcdsdgdge", "abgsdfscd");
echo microtime_float() - $time_start;
