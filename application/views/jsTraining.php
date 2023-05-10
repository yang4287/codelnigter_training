<?php
defined('BASEPATH') or exit('No direct script access allowed');

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Bootstrap 4 Example</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>



	<!-- Font Awesome Icon -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" />

	<script src="<?=JS_DIR;?>jsTraining/jsTraining.js"></script>
</head>

<body>

	<div class="container">
		<!-- Title -->
		<div class="row">
			<div class="col-sm-12">
				<h1>Javascript Training</h1>
				<hr>
			</div>
		</div>

		<!-- Controll Form -->
		<div class="row">
			<div class="col-sm-12"><button class="btn btn-warning float-right ctrl-btn">新增</button></div>
		</div>

		<!-- Table -->
		<div class="row">
			<table class="table table-striped table-bordered table-hover ctrl-table">
			</table>
		</div>

		<!-- Message box -->
		<div class="row">
			<div id="ctrl-message" class="text-danger ctrl-message"></div>
		</div>

	</div>

</body>

</html>