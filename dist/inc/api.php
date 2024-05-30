<?php

session_start();

if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Max-Age: 1000');
	header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
} else {
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
			echo 'erreur';
			exit();
		}
	} else {
		echo 'erreur';
		exit();
	}
}

$_POST = json_decode(file_get_contents('php://input'), true);

if (!empty($_POST['token']) && !empty($_POST['lien'])) {
	$token = $_POST['token'];
	$domaine = $_SERVER['SERVER_NAME'];
	$lien = $_POST['lien'];
	$donnees = array(
		'token' => $token,
		'domaine' => $domaine
	);
	$donnees = http_build_query($donnees);
	$ch = curl_init($lien);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $donnees);
	$resultat = curl_exec($ch);
	if ($resultat === 'non_autorise' || $resultat === 'erreur') {
		echo 'erreur_token';
	} else if ($resultat === 'token_autorise' && !empty($_POST['action'])) {
		$action = $_POST['action'];
		if ($action === 'creer' && !empty($_POST['videoId'])) {
			require 'db.php';
			$id = uniqid('', false);
			$videoId = $_POST['videoId'];
			$titre = $_POST['nom'];
			$description = $_POST['description'];
			$vignette = $_POST['vignette'];
			$debut = $_POST['debut'];
			$fin = $_POST['fin'];
			$largeur = $_POST['largeur'];
			$hauteur = $_POST['hauteur'];
			$sousTitres = $_POST['sousTitres'];
			$date = date('Y-m-d H:i:s');
			$stmt = $db->prepare('INSERT INTO digiview_videos (url, videoId, titre, description, vignette, debut, fin, largeur, hauteur, sousTitres, date) VALUES (:url, :videoId, :titre, :description, :vignette, :debut, :fin, :largeur, :hauteur, :sousTitres, :date)');
			if ($stmt->execute(array('url' => $id, 'videoId' => $videoId, 'titre' => $titre, 'description' => $description, 'vignette' => $vignette, 'debut' => $debut, 'fin' => $fin, 'largeur' => $largeur, 'hauteur' => $hauteur, 'sousTitres' => $sousTitres, 'date' => $date))) {
				echo $id;
			} else {
				echo 'erreur';
			}
			$db = null;
		} else if ($action === 'modifier' && !empty($_POST['id']) && !empty($_POST['titre'])) {
			require 'db.php';
			$id = $_POST['id'];
			$titre = $_POST['titre'];
			$description = $_POST['description'];
			$stmt = $db->prepare('UPDATE digiview_videos SET titre = :titre, description = :description WHERE url = :id');
			if ($stmt->execute(array('titre' => $titre, 'description' => $description, 'id' => $id))) {
				echo 'titre_modifie';
			} else {
				echo 'erreur';
			}
		} else if ($action === 'supprimer' && !empty($_POST['id'])) {
			require 'db.php';
			$id = $_POST['id'];
			$stmt = $db->prepare('DELETE FROM digiview_videos WHERE url = :url');
			if ($stmt->execute(array('url' => $id))) {
				echo 'contenu_supprime';
			} else {
				echo 'erreur';
			}
			$db = null;
		} else {
			echo 'erreur';
		}
	} else {
		echo 'erreur';
	}
	curl_close($ch);
	exit();
} else {
	echo 'erreur';
	exit();
}

?>
