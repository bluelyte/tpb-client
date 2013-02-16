# bluelyte/tpb-client

A scraper for The Pirate Bay based on Symfony components.

DISCLAIMER: This project is not endorsed by, affiliated with, or intended to infringe upon The Pirate Bay and is meant for non-commercial purposes (i.e. personal use) only.

# Install

The recommended method of installation is [through composer](http://getcomposer.org/).

```JSON
{
    "require": {
        "bluelyte/tpb-client": "1.0.0"
    }
}
```

# Usage

```php
<?php

require __DIR__ . '/vendor/autoload.php';

$client = new \Bluelyte\TPB\Client\Client();

try {
    $results = $client->search('king of the nerds s01e03');
    var_dump($results);
} catch (\Exception $e) {
    echo ((string) $e), PHP_EOL;
}

/*
Output:
array(4) {
  'start' =>
  string(1) "1"
  'end' =>
  string(1) "5"
  'total' =>
  string(1) "5"
  'results' =>
  array(5) {
    [0] =>
    array(14) {
      'category' =>
      string(5) "Video"
      'categoryLink' =>
      string(33) "http://thepiratebay.se/browse/200"
      'subcategory' =>
      string(8) "TV shows"
      'subcategoryLink' =>
      string(33) "http://thepiratebay.se/browse/205"
      'name' =>
      string(41) "King.of.The.Nerds.S01E03.HDTV.x264-EVOLVE"
      'detailsLink' =>
      string(80) "http://thepiratebay.se/torrent/8090340/King.of.The.Nerds.S01E03.HDTV.x264-EVOLVE"
      'magnetLink' =>
      string(268) "magnet:?xt=urn:btih:07f896f5d08e1cdaebe34ffdf15cd556eee69c4b&dn=King.of.The.Nerds.S01E03.HDTV.x264-EVOLVE&tr=udp%3A%2F%2Ftracker.openbittorrent.com%3A80&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80&tr=udp%3A%2F%2Ftracker.istole.it%3A6969&tr=udp%3A%2F%2Ftracker.ccc.de%3A80"
      'userLink' =>
      string(34) "http://thepiratebay.se/user/TvTeam"
      'comments' =>
      string(1) "8"
      'uploaded' =>
      string(20) "2013-02-01 07:20:00"
      'size' =>
      string(11) "388.77 MiB"
      'user' =>
      string(6) "TvTeam"
      'seeders' =>
      string(3) "168"
      'leechers' =>
      string(1) "9"
    },
    ...
  }
}
*/
```

## License

Released under the BSD License. See `LICENSE`.
