<?php

if (!file_exists(dirname(__FILE__) . '/digiview.db')) {
    $db = new PDO('sqlite:'. dirname(__FILE__) . '/digiview.db');
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $table = "CREATE TABLE digiview_videos (
        id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE,
        url TEXT NOT NULL,
        videoId TEXT NOT NULL,
        titre TEXT NOT NULL,
        description	TEXT NOT NULL,
        vignette TEXT NOT NULL,
        debut INTEGER NOT NULL,
        fin	INTEGER NOT NULL,
        largeur	INTEGER NOT NULL,
        hauteur	INTEGER NOT NULL,
        sousTitres TEXT NOT NULL,
        date TEXT NOT NULL
    )";
    $db->exec($table);
} else {
    $db = new PDO('sqlite:'. dirname(__FILE__) . '/digiview.db');
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

?>
