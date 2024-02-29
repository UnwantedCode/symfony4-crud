<?php

namespace App\Tests;

trait RoleUser
{
    public $client;
    public $entityManager;
    public function setUp()
    {
        parent::setUp();
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'jd@symf4.loc',
            'PHP_AUTH_PW' => 'passw',
        ]);

        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
