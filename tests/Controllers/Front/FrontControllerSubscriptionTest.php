<?php

namespace App\Tests\Controllers\Front;

use App\Entity\Subscription;
use App\Tests\RoleUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerSubscriptionTest extends WebTestCase
{
    use RoleUser;
    /**
     * @dataProvider urlsWithVideo
     */
    public function testLoggedInUserDoesNotSeeTextForNoMembers($url)
    {
        $this->client->request('GET', $url);

        $this->assertNotContains(
            'video for <b>MEMBERS</b> only.',
            $this->client->getResponse()->getContent()
        );
    }
    /**
     * @dataProvider urlsWithVideo
     */
    public function testNotLoggedInUserSeesTextForNoMembers($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertContains( 'Video for <b>MEMBERS</b> only.', $client->getResponse()->getContent() );

    }

    public function urlsWithVideo()
    {
        yield ['/video-list/category/movies,4'];
        yield ['/search-results?query=movies'];
    }


    public function testExpiredSubscription()
    {
        $subscription = $this->entityManager
            ->getRepository(Subscription::class)
            ->find(3);

        $invalid_date = new \Datetime();
        $invalid_date->modify('-1 day');
        $subscription->setValidTo($invalid_date);

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

        $this->client->request('GET', '/video-list/category/movies,4');

        $this->assertContains( 'Video for <b>MEMBERS</b> only.', $this->client->getResponse()->getContent() );

    }


    /**
     * @dataProvider urlsWithVideo2
     */
    public function testNotLoggedInUserSeesVideosForNoMembers($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertContains( 'https://player.vimeo.com/video/113716040', $client->getResponse()->getContent() );

    }

    public function urlsWithVideo2()
    {
        yield ['/video-list/category/toys,2/2'];
        yield ['/search-results?query=Movies+3'];
        yield ['/video-details/2#video_comments'];
    }
}
