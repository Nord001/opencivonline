<html>
	<head>
		<title>Open Civilization Online</title>
		<link href="styles.css" rel="stylesheet" />
	</head>
	<body>
<?php
	require 'credis/Client.php';
	$redis = new Credis_Client('localhost');

	$user = $_REQUEST["user"];
	$building = $_REQUEST["building"];

	echo "<a href='base.php?user=$user&base=$user'>&lt; back</a>";
	include "buildings/$building.php";
