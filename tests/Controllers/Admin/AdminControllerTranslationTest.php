<?php

namespace App\Tests\Controllers\Admin;

use App\Tests\RoleUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerTranslationTest extends WebTestCase
{
    use RoleUser;
    public function testTranslations()
    {

        $this->client->request('GET', '/pl/admin/');

        $this->assertContains( 'Mój profil', $this->client->getResponse()->getContent() );
        $this->assertContains( 'lista-wideo', $this->client->getResponse()->getContent() );
    }
}