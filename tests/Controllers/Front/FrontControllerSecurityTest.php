<?php

namespace App\Tests\Controllers\Front;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerSecurityTest extends WebTestCase
{
    /**
     * @dataProvider getSecureUrls
     */
    public function testSecureUrls(string $url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        // assert to tartget url
        $this->assertContains( '/login', $client->getResponse()->getTargetUrl() );
    }
    public function getSecureUrls()
    {
        yield ['/admin/videos'];
        yield ['/admin'];
        yield ['/admin/su/categories'];
        yield ['/admin/su/delete-category/1'];
    }


    public function testVideoForMembersOnly()
    {
        $client = static::createClient();
        $client->request('GET', '/video-list/category/movies,4');
        $this->assertContains('Video for <b>MEMBERS</b> only.', $client->getResponse()->getContent());
    }
}
