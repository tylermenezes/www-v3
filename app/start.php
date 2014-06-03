<?php

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
    $data['music']['nowplaying'] = [
        'image' => $nowplaying->image[count($nowplaying->image) - 1]->{'#text'},
        'title' => $nowplaying->name,
        'artist' => $nowplaying->artist->{'#text'},
        'album' => $nowplaying->album->{'#text'}
    ];
}

$data['music']['topalbums'] = [];

foreach ($lastfm->top_albums('12month') as $album) {
    $data['music']['topalbums'][] = [
        'name' => $album->name,
        'artist' => $album->artist->name,
        'image' => $album->image[1]->{'#text'},
    ];
}


// # Location
include_once(__DIR__.DIRECTORY_SEPARATOR.'DataSources'.DIRECTORY_SEPARATOR.'Location.php');
$lastlocation = \DataSources\Location::last_location();
$data['lastlocation'] = $lastlocation;

echo $twig->render('index.twig', ['data' => $data]);