<?php

namespace DataSources;

class LastFm {
    private $base = 'http://ws.audioscrobbler.com/2.0/';
    private $api_key = null;
    private $user = null;

    public function __construct($api_key, $user)
    {
        $this->api_key = $api_key;
        $this->user = $user;
    }

    public function get($endpoint, $params = [])
    {
        $params['api_key'] = $this->api_key;
        $params['method'] = $endpoint;
        $params['user'] = $this->user;
        $params['format'] = 'json';

        $query = http_build_query($params);
        $url = $this->base.'?'.$query;

        return json_decode(file_get_contents($url));
    }

    public function recent()
    {
        return $this->get('user.getrecenttracks')->recenttracks->track;
    }

    public function now_playing()
    {
        $track = $this->recent()[0];
        if ($track->{'@attr'} && $track->{'@attr'}->nowplaying) {
            return $track;
        } else {
            return null;
        }
    }

    public function top_artists($period = '1month')
    {
        return $this->get('user.gettopartists', ['period' => $period])->topartists->artist;
    }

    public function top_albums($period = '1month')
    {
        return $this->get('user.gettopalbums', ['period' => $period, 'limit' => 20])->topalbums->album;
    }
}