<?php

namespace App\Tests\Controllers\Admin;

use App\Entity\Category;
use App\Tests\RoleAdmin;
use App\Tests\RoleUser;
use App\Tests\Rollback;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerSubscriptionTest extends WebTestCase
{
    use RoleUser;
    public function testDeleteSubscription()
    {
        // $this->client->followRedirects();


        $crawler = $this->client->request('GET', '/admin/');

        $link = $crawler
            ->filter('a:contains("cancel plan")')
            ->link();

        $this->client->click($link);

        $this->client->request('GET', '/video-list/category/toys,2');

        $this->assertContains( 'Video for <b>MEMBERS</b> only.', $this->client->getResponse()->getContent() );

    }

}
