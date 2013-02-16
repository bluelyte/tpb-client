<?php

namespace Bluelyte\TPB\Client;

use Symfony\Component\BrowserKit\Request;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler;

class Client extends \Goutte\Client
{
    protected $baseUrl = 'http://thepiratebay.se';

    protected $categories = array(
        'audio',
        'video',
        'apps',
        'games',
        'other',
    );

    protected function filterRequest(Request $request)
    {
        $request = new Request(
            str_replace(' ', '%20', $request->getUri()),
            $request->getMethod(),
            $request->getParameters(),
            $request->getFiles(),
            $request->getCookies(),
            $request->getServer(),
            $request->getContent()
        );
        //var_dump($request);
        return $request;
    }

    protected function filterResponse(Response $response)
    {
        //var_dump($response);
        return $response;
    }

    public function search($term, $page = 1, $category = 'all', $sort = true)
    {
        $categories = array_filter($this->categories, function($value) use ($category) {
            return ($category === 'all' || $value === $category);
        });
        $params = array('q' => $term, 'page' => $page - 1);
        foreach ($categories as $param) {
            $params[$param] = 1;
        }

        // Get the search form
        $crawler = $this->request('GET', $this->baseUrl);

        // Submit the search form
        $form = $crawler->filterXPath('//form')->form();
        $crawler = $this->submit($form, $params);

        // Sort the search results to get the one with the most seeders
        if ($sort) {
            $link = $crawler->filterXPath('//table[@id="searchResult"]//a[text()="SE"]')->link();
            $crawler = $this->click($link);
        }

        // Get position within the entire result set
        // Displaying hits from 30 to 60 (approx 1000 found)
        $h2 = $crawler->filterXPath('//h2[contains(., "Displaying hits")]');
        $position = array();
        preg_match('/Displaying hits from (?P<start>[0-9,]+) to (?P<end>[0-9,]+) [^0-9]+(?P<total>[0-9,]+)/', $h2->text(), $position);

        // Parse the data from the table
        $rows = $crawler->filterXPath('//table[@id="searchResult"]/tr');
        $results = array();
        foreach ($rows as $row) {
            $rowData = array();
            $row = new Crawler($row);

            $links = $row->filterXPath('//td[1]//a');
            $category = $this->getCategoryLink($links->eq(0));
            $rowData['category'] = $category['name'];
            $rowData['categoryLink'] = $category['href'];
            $subcategory = $this->getCategoryLink($links->eq(1));
            $rowData['subcategory'] = $subcategory['name'];
            $rowData['subcategoryLink'] = $subcategory['href'];

            $cell = $row->filterXPath('//td[2]');
            $link = $cell->filterXPath('//a[@class="detLink"]');
            $rowData['name'] = $link->text();
            $rowData['detailsLink'] = $this->baseUrl . $link->attr('href');
            $rowData['magnetLink'] = $cell->filterXPath('//a[@title="Download this torrent using magnet"]')->attr('href');

            $link = $cell->filterXPath('//a[@title="Download this torrent"]');
            if (count($link)) {
                $rowData['torrentLink'] = 'http:' . $link->attr('href');
            }

            $link = $cell->filterXPath('//a[contains(@href, "/user/")]');
            if (count($link)) {
                $rowData['userLink'] = $this->baseUrl . $link->attr('href');
            }

            $img = $cell->filterXPath('//img[contains(@alt, "comments")]');
            if (count($img)) {
                $rowData['comments'] = preg_replace('/[^0-9]/', '', $img->attr('alt'));
            }

            $desc = $cell->filterXPath('//font[@class="detDesc"]')->text();
            $descParsed = array();
            preg_match('#Uploaded\s+(?P<time>[^,]+),\s+Size\s+(?P<size>[^,]+),\s+ULed\s+by\s+(?P<user>[^\s]+)#S', $desc, $descParsed);
            $rowData['uploaded'] = date('Y') . '-' . $descParsed['time'] . ':00';
            $rowData['size'] = $descParsed['size'];
            $rowData['user'] = $descParsed['user'];

            $rowData['seeders'] = $row->filterXPath('//td[3]')->text();
            $rowData['leechers'] = $row->filterXPath('//td[4]')->text();

            $results[] = $rowData;
        }

        $return = array(
            'start' => $position['start'],
            'end' => $position['end'],
            'total' => $position['total'],
            'results' => $results
        );

        return $return;
    }

    protected function getCategoryLink(Crawler $crawler)
    {
        return array(
            'name' => $crawler->text(),
            'href' => $this->baseUrl . $crawler->attr('href'),
        );
    }
}
