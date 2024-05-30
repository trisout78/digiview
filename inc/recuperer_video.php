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

if (!empty($_POST['id'])) {
	require 'db.php';
	$id = $_POST['id'];
	$stmt = $db->prepare('SELECT videoId, titre, description, vignette, debut, fin, largeur, hauteur, sousTitres FROM digiview_videos WHERE url = :url');
	if ($stmt->execute(array('url' => $id))) {
		$donnees = $stmt->fetchAll();
		if (!$donnees) {
			echo 'contenu_inexistant';
		} else {
			echo json_encode($donnees[0]);
		}
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
