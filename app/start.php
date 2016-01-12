<?php

if ($_GET['proxy']) {
    if (!preg_match('/^([A-Za-z0-9\-]*\.)*(last|lst)\.fm\/[a-zA-Z0-9\-\.\/\_]*\/[A-Za-z0-9\-\_]+\.(jpg|png|gif)$/', $_GET['proxy'])) {
        exit;
    }

    $image_type = preg_replace('/^.*\.([a-z]{3})$/', '\1', $_GET['proxy']);

    if (!in_array($image_type, ['png', 'jpg', 'gif'])) {
        $image_type = 'png';
    }

    header('Content-type: image/'.$image_type);
    header("Cache-Control: max-age=2592000");
    echo file_get_contents('http://'.$_GET['proxy']);
    exit;
}

// Load Twig
require_once(__DIR__.DIRECTORY_SEPARATOR.'Twig'.DIRECTORY_SEPARATOR.'Autoloader.php');
\Twig_Autoloader::register(true);
$loader = new \Twig_Loader_Filesystem(__DIR__.DIRECTORY_SEPARATOR.'views');
$twig = new \Twig_Environment($loader);

// # Last.fm
include_once(__DIR__.DIRECTORY_SEPARATOR.'DataSources'.DIRECTORY_SEPARATOR.'LastFm.php');
$lastfm = new \DataSources\LastFm('42f14f464b78f5ec49ab55fea1e80bd3', 'tylermenezes');


$data = [];
$data['music'] = [
    'nowplaying' => null
];

$nowplaying = $lastfm->now_playing();
if ($nowplaying) {
    $image = $nowplaying->image[count($nowplaying->image) - 1]->{'#text'};
    $image = '/?proxy='. urlencode(preg_replace('/^https?:\/\//', '', $image));

    $data['music']['nowplaying'] = [
        'image' => $image,
        'title' => $nowplaying->name,
        'artist' => $nowplaying->artist->{'#text'},
        'album' => $nowplaying->album->{'#text'}
    ];
}

$data['music']['topalbums'] = [];

foreach ($lastfm->top_albums('12month') as $album) {
    $image = $album->image[1]->{'#text'};
    $image = '/?proxy='. urlencode(preg_replace('/^https?:\/\//', '', $image));

    $data['music']['topalbums'][] = [
        'name' => $album->name,
        'artist' => $album->artist->name,
        'image' => $image,
    ];
}


// # Location
include_once(__DIR__.DIRECTORY_SEPARATOR.'DataSources'.DIRECTORY_SEPARATOR.'Location.php');
$lastlocation = \DataSources\Location::last_location();
$data['lastlocation'] = $lastlocation;

echo $twig->render('index.twig', ['data' => $data]);
