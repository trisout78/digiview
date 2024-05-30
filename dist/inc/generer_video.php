<?php

session_start();

$env = '../.env';
if (isset($_SESSION['domainesAutorises']) || file_exists($env)) {
	if (isset($_SESSION['domainesAutorises']) && $_SESSION['domainesAutorises'] !== '') {
		$domainesAutorises = $_SESSION['domainesAutorises'];
	} else if (file_exists($env)) {
		$donneesEnv = explode("\n", file_get_contents($env));
		foreach ($donneesEnv as $ligne) {
			preg_match('/([^#]+)\=(.*)/', $ligne, $matches);
			if (isset($matches[2])) {
				putenv(trim($ligne));
			}
		}
		$domainesAutorises = getenv('AUTHORIZED_DOMAINS');
		$_SESSION['domainesAutorises'] = $domainesAutorises;
	}
	if ($domainesAutorises === '*') {
		$origine = $domainesAutorises;
	} else {
		$domainesAutorises = explode(',', $domainesAutorises);
		$origine = $_SERVER['SERVER_NAME'];
	}
	if ($origine === '*' || in_array($origine, $domainesAutorises, true)) {
		header('Access-Control-Allow-Origin: $origine');
		header('Access-Control-Allow-Methods: POST');
		header('Access-Control-Max-Age: 1000');
		header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
	} else {
		header('Location: ../');
		exit();
	}
} else {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Max-Age: 1000');
	header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
}

$_POST = json_decode(file_get_contents('php://input'), true);

if (!empty($_POST['videoId']) && !empty($_POST['titre'])) {
	require 'db.php';
	$url = uniqid('', false);
	$videoId = $_POST['videoId'];
	$titre = $_POST['titre'];
	$description = $_POST['description'];
	$vignette = $_POST['vignette'];
	$debut = $_POST['debut'];
	$fin = $_POST['fin'];
	$largeur = $_POST['largeur'];
	$hauteur = $_POST['hauteur'];
	$sousTitres = $_POST['sousTitres'];
	$date = date('Y-m-d H:i:s');
	$stmt = $db->prepare('INSERT INTO digiview_videos (url, videoId, titre, description, vignette, debut, fin, largeur, hauteur, sousTitres, date) VALUES (:url, :videoId, :titre, :description, :vignette, :debut, :fin, :largeur, :hauteur, :sousTitres, :date)');
	if ($stmt->execute(array('url' => $url, 'videoId' => $videoId, 'titre' => $titre, 'description' => $description, 'vignette' => $vignette, 'debut' => $debut, 'fin' => $fin, 'largeur' => $largeur, 'hauteur' => $hauteur, 'sousTitres' => $sousTitres, 'date' => $date))) {
		echo $url;
	} else {
		echo 'erreur';
	}
	$db = null;
	exit();
} else {
	header('Location: ../');
	exit();
}

?>
