<?php
$users = [];
// game: SC(22827)|FS(175155)|BW(38796);boardgame|boardgameexpansion

// ----------------

function call($type, $id, $page) {
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://www.boardgamegeek.com/xmlapi2/thing?type=' . $type . '&id=' . $id .'&ratingcomments=1&page=' . $page,
        CURLOPT_USERAGENT => 'Codular Sample cURL Request',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 60
    ));
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function extractData($data, $game) {
    $comments = $data->item->comments->comment;
    foreach ($comments as $key => $value) {
        $username = urlencode($value['username']);
        if (!isset($users[$username])) {
            $users[$username] = [];
        }
        $users[$username][$game] = $value['rating'];
    }
}

function process($gamename, $gameid, $gametype = 'boardgame') {
    $page = 1;
    $byPage = 100;
    $lastPage = 0;

    $data = simplexml_load_string(call($gametype, $gameid, $page));
    extractData($data, $gamename);

    $totalitems = $data->item->comments['totalitems'];
    $lastPage = ceil($totalitems / $byPage);

    $page++;
    for ($page; $page <= $lastPage; $page++) {
        $data = simplexml_load_string(call($gametype, $gameid, $page));
        extractData($data, $gamename);
    }
}

// ----------------

process('SC', 22827);
process('FS', 175155);
process('BW', 38796, 'boardgameexpansion');

// ----------------

$results = [];
$resultsBW = [];

foreach($users as $username => $user) {
    if (isset($user['SC']) && isset($user['FS'])) {
        $results['SC'] = $user['SC'] > $user['FS']
            ? $results['SC']++
            : $results['FS']++;
    }

    if (isset($user['BW']) && isset($user['FS'])) {
        $resultsBW['BW'] = $user['BW'] > $user['FS']
            ? $resultsBW['BW']++
            : $resultsBW['FS']++;
    }
}

echo "<p>SC vs FS: " . count($result['SC']) . " - " . count($result['FS']) . "</p>";
echo "<p>BW vs FS: " . count($resultsBW['BW']) . " - " . count($resultsBW['FS']) . "</p>";
