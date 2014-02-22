<?php

require './Slim/Slim.php';

$app = new Slim();

// include('./projects.php');


// $app->get('/wines', 'getWines');
// $app->get('/wines/:id',	'getWine');
// $app->get('/wines/search/:query', 'findByName');
// $app->post('/wines', 'addWine');
// $app->put('/wines/:id', 'updateWine');
// $app->delete('/wines/:id', 'deleteWine');



$app->get('/projects', 'getProjects');
$app->get('/projects/:id',	'getProject');
$app->get('/projects/search/:query', 'findByName');
$app->post('/projects', 'addProject');
$app->put('/projects/:id', 'updateProject');
$app->delete('/projects/:id', 'deleteProject');


// $app->get('/res/inst', 'getInstitutions');
// $app->get('/res/inst/:id', 'getInstitution');
// $app->get('/res/proj', 'getProjects');
// $app->get('/res/proj/:id', 'getProject');
// $app->get('/res/proj/:pid/wp', 'getWorkPackages');
// $app->get('/res/proj/:pid/wp/:id', 'getWorkPackage');
// 
// $app->get('/res', 'getResInstitutions');
// $app->get('/res/:id',	'getWine');
// $app->get('/res/search/:query', 'findByName');
// $app->post('/res', 'addWine');
// $app->put('/res/:id', 'updateWine');
// $app->delete('/wines/:id', 'deleteWine');
// 
// 
// $app->post('/auth', 'authentication');
// 
// 
// $app->post('/res', 'getResearcher');
// $app->post('/res/clock/start', 'startClock');
// $app->post('/res/clock/stop', 'stopClock');
// $app->post('/res/clock/update', 'updateClock');
// $app->post('/res/clock/delete', 'deleteClock');


$app->run();

function getProjects() {
	$sql = "select * FROM project ORDER BY name";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$projects = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($projects);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getProject($id) {
	$sql = "SELECT * FROM project WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$project = $stmt->fetchObject();  
		$db = null;
		echo json_encode($project); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function addProject() {
	error_log('addProject\n', 3, '/var/tmp/php.log');
	$request = Slim::getInstance()->request();
	$project = json_decode($request->getBody());
	$sql = "INSERT INTO project (number, name, acronym, status, start, end, description) VALUES (:number, :name, :acronym, :status, :start, :end, :description)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("number", $project->number);
		$stmt->bindParam("name", $project->name);
		$stmt->bindParam("acronym", $project->acronym);
		$stmt->bindParam("status", $project->status);
		$stmt->bindParam("start", $project->start);
		$stmt->bindParam("end", $project->end);
		$stmt->bindParam("description", $project->description);
		$stmt->execute();
		$project->id = $db->lastInsertId();
		$db = null;
		echo json_encode($project); 
	} catch(PDOException $e) {
		error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function updateProject($id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$project = json_decode($body);
	$sql = "UPDATE project SET number=:number, name=:name, acronym=:acronym, status=:status, start=:start, end=:end, description=:description WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("number", $project->number);
		$stmt->bindParam("name", $project->name);
		$stmt->bindParam("acronym", $project->acronym);
		$stmt->bindParam("status", $project->status);
		$stmt->bindParam("start", $project->start);
		$stmt->bindParam("end", $project->end);
		$stmt->bindParam("description", $project->description);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($project); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function deleteProject($id) {
	$sql = "DELETE FROM project WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function findByName($query) {
	$sql = "SELECT * FROM project WHERE UPPER(name) LIKE :query ORDER BY name";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$query = "%".$query."%";  
		$stmt->bindParam("query", $query);
		$stmt->execute();
		$projects = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"project": ' . json_encode($projects) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getConnection() {
	$dbhost="127.0.0.1";
	$dbuser="root";
	$dbpass="";
	$dbname="cellar";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

?>