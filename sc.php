<?php
class scvsfs
{
    private $users = [];
    private $results = ['SC'=>0, 'FS'=>0];
    private $resultsBW = ['BW'=>0, 'FS'=>0];
    private $count = ['SC'=>0, 'FS'=>0, 'BW'=>0];
    private $total = ['SC'=>0, 'FS'=>0, 'BW'=>0];

    public function __contruct()
    {
        //$this->init();
    }

    public function init()
    {
        // ----------------
        $this->process('SC', 22827);
        $this->process('FS', 175155);
        $this->process('BW', 38796, 'boardgameexpansion');
        // ----------------

        foreach($this->users as $username => $user) {
            if (isset($user['SC']) && isset($user['FS'])) {
                if ($user['SC'] > $user['FS']) {
                    $this->results['SC']++;
                } else if ($user['SC'] < $user['FS']) {
                    $this->results['FS']++;
                }
            }
            if (isset($user['BW']) && isset($user['FS'])) {
                if ($user['BW'] > $user['FS']) {
                    $this->resultsBW['BW']++;
                } else if ($user['BW'] < $user['FS']) {
                    $this->resultsBW['FS']++;
                }
            }
        }
        echo "<p>SC vs FS: " . $this->results['SC'] . " - " . $this->results['FS'] . "</p>";
        echo "<p>BW vs FS: " . $this->resultsBW['BW'] . " - " . $this->resultsBW['FS'] . "</p>";
        echo "<p>cnt SC: " . $this->count['SC'] . "/" . $this->total['SC']
        . ", FS: " . $this->count['FS'] . "/" . $this->total['FS']
        . ", BW: " . $this->count['BW'] . "/" . $this->total['BW'] . "</p>";
    }

    private function process($gamename, $gameid, $gametype = 'boardgame') {
        $page = 1;
        $byPage = 100;
        $lastPage = 0;

        $data = simplexml_load_string($this->call($gametype, $gameid, $page));
        $this->extractData($data, $gamename);
        // echo "<pre>";print_r($this->users);echo"</pre>";

        $totalitems = $data->item->comments['totalitems'];
        $this->total[$gamename] = $totalitems;
        $lastPage = ceil($totalitems / $byPage);
        $page++;
        for (; $page <= $lastPage; $page++) {
            $data = simplexml_load_string($this->call($gametype, $gameid, $page));
            $this->extractData($data, $gamename);
        }
    }

    private function call($type, $id, $page) {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://www.boardgamegeek.com/xmlapi2/thing?type='
            . $type . '&id=' . $id .'&ratingcomments=1&page=' . $page,
            CURLOPT_USERAGENT => 'Codular Sample cURL Request',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 60
        ));
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    private function extractData($data, $game) {
        try {
            $comments = isset($data->item->comments->comment)
                ? $data->item->comments->comment
                : [];
            foreach ($comments as $key => $value) {
                $username = urlencode($value['username']);
                if (!isset($this->users[$username])) {
                    $this->users[$username] = [];
                }
                $this->users[$username][$game] = (int)$value['rating'][0];
                if ($this->users[$username][$game] >= 0) {
                    $this->count[$game]++;
                }
            }
        } catch(\Throwable $t) {
            echo "<p>".$t->getMessage()."</p>";
        }
    }
}

(new scvsfs())->init();
