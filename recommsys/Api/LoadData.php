<?php

$row = 1;
if (($handle = fopen("test.csv", "r")) != FALSE) {
	while (($data = fgetcsv($handle, 100, ";")) != FALSE) {
		$num = count($data);
		//echo "<p> $num fields in line $row: <br /></p>\n";
		$row++;
		for ($c = 0; $c < $num; $c++) {
			echo $data[$c] . "<br />\n";
		}
		echo "<br />\n";
	}
	fclose($handle);
}