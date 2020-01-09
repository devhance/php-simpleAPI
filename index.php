<?php
	$con = mysqli_connect('localhost', 'root', '', 'api');
	error_reporting(0);
	$key = $_GET['api_key'];
	$user = $_GET['user'];
	$fields = $_GET['fields'];

	// if there is no api key
	if (!isset($key)) {
		
		echo json_encode(
			array(
				'code' => 1,
				'message' => 'API key is required'
			)
		);
		die();
	}

	// if api key length is correct
	if (strlen($key < 50)) {
		echo json_encode(array(
			'code' => 2,
				'message' => 'API key is invalid'
		));
		die();
	}

	// if api matches db
	$query = $con->query("SELECT * FROM apikey WHERE api_key = '$key' AND is_valid = 1");
	if ($query->num_rows == 0) {
		echo json_encode(array(
			'code' => 3,
				'message' => 'API key is invalid or expired.'
		));
		die(); 
	}

	// if user id exists ( /?apiname=&user= )
	if (!isset($user)) {
		echo json_encode(
			array(
				'code' => 4,
				'message' => 'User ID is required'
			)
		);
		die();
	}
	
	// if user matches db
	$query = $con->query("SELECT * FROM users WHERE id = '$user'");
	if ($query->num_rows == 0) {
		echo json_encode(array(
			'code' => 3,
				'message' => 'User ID is invalid.'
		));
		die();
	}

	// get fields
	if (!isset($fields)) {
		echo json_encode($query->fetch_assoc());
		die();
	}

	$fields = explode(',', $fields);

	$allowedFields = array(
		'name', 
		'firstname', 
		'lastname', 
		'email'
	);

	$user = array();

	$data = $query->fetch_assoc();

	foreach($fields as $field) {
		if(!in_array($field, $allowedFields)) {
			echo json_encode(array(
				'code' => 4,
				'message' => 'Field '. $field .' does not exist'
			));

			die();
		}
		if ($field == 'name') {
			$user[$field] = $query['firstname'].' '. $query['lastname'];
			continue;
		}

		$user[$field] = $data[$field];
	}


	print_r(json_encode($user));
?>