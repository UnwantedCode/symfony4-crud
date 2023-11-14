<?php

namespace App\Tests;

trait RoleAdmin
{
    public function setUp()
    {
        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'jw@symf4.loc',
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
