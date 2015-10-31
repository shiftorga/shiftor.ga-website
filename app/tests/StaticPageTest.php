<?php

namespace Cmf;

class StaticPageTest extends WebTestCase
{

    /**
     * @dataProvider contentDataProvider
     */
    public function testContent($url, $title)
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertCount(1, $crawler->filter(sprintf('h2:contains("%s")', $title)), 'Page does not contain an h2 tag with: '.$title);
    }

    public function contentDataProvider()
    {
        return array(
            array('/', 'ShiftOrga'),
            array('/news', 'News'),
            array('/get-involved', 'Hilf mit'),
            array('/about', 'About'),
        );
    }

    public function testContributorsShowsTableOfSponsors()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/contributers');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('table thead th:contains("Company")')->count());
        $this->assertEquals(1, $crawler->filter('table tbody tr:contains("Mayflower")')->count());
    }

    public function testGetInvolvedShowsALinkToGithubWiki()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/get-involved');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('a:contains("Github Wiki")')->count());
    }


    public function testOnlyCurrentNavItemIsCurrent()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/get-involved');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(1, $crawler->filter('#nav li.current a:contains("Hilf mit")')->count());
        $this->assertEquals(0, $crawler->filter('#nav li.current a:contains("Home")')->count());
        $this->assertEquals(0, $crawler->filter('#nav li.current a:contains("About")')->count());
    }

    public function testRssFeed()
    {
        $client = $this->createClient();
        $client->request('GET', '/news.rss');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains('ShiftOrga', $client->getResponse()->getContent());
    }
}
