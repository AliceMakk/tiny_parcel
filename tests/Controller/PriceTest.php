<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class PriceTest extends WebTestCase
{
    private $client = null;
    private $parcel_ids = [];

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->logIn();
        $i = 0;
        while ($i < 10) {
            $this->client->request(
                'POST',
                '/parcels/',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode([
                    'name' => 'Tiny box #' . $i,
                    'weight' => '0.4',
                    'volume' => '0.00079',
                    'declared_value' => '1300'
                ]));
            $result = json_decode($this->client->getResponse()->getContent());
            $this->parcel_ids[] = $result->id;
            $i++;
        }
    }

    /**
     * Tests the prices are retrieved based on parcel ids
     */
    public function testGetAll()
    {
        $this->client->request(
            'GET',
            '/prices/?parcelIds=' . implode(',', $this->parcel_ids));
        $this->assertStringContainsString('"parcelId":' . $this->parcel_ids[0], $this->client->getResponse()->getContent());
    }

    /**
     * Login user as the API is accessable only for user granted with ROLE_USER role
     */
    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewall = 'main';

        $token = serialize(new UsernamePasswordToken('user', null, $firewall, array('ROLE_USER')));
        $session->set('_security_' . $firewall, $token);
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
