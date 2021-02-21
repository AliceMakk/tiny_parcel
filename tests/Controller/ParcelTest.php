<?php
namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ParcelTest extends WebTestCase
{
    private $client = null;
    private $parcel_id = null;
    private $parcel = [
        'name' => 'Tiny box',
        'weight' => '0.4',
        'volume' => '0.00079',
        'declared_value' => '1300'
    ];

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->logIn();
        $this->client->request(
            'POST',
            '/parcels/',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($this->parcel));
        $result = json_decode($this->client->getResponse()->getContent());
        $this->parcel_id = $result->id;
    }

    /**
     * Tests the creation endpoint and asserts error is given when no of the units are provided
     */
    public function testCreate()
    {
        $this->assertStringContainsString('Parcel saved!', $this->client->getResponse()->getContent());

        $this->client->request(
            'POST',
            '/parcels/',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name":"Tiny box", "weight":"", "volume":"", "declared_value":""}');
        $this->assertEquals('{"error":"Measurements of the parcel must be provided either weight, volume or parcel declared value."}', $this->client->getResponse()->getContent());
    }

    /**
     * Test retreiving one parcel by its id
     */
    public function testGetOne()
    {
        $this->client->request(
            'GET',
            "/parcels/{$this->parcel_id}",
            );
        $this->assertStringContainsString($this->parcel['name'], $this->client->getResponse()->getContent());
    }

    /**
     * Test the price is calculated at the maximum rate which is value
     */
    public function testMaxPriceByValue()
    {
        $this->client->request(
            'GET',
            "/parcels/{$this->parcel_id}",
            );
        $this->assertStringContainsString('"quote":"39.00"', $this->client->getResponse());
    }

    /**
     * Test the price is calculated at the maximum rate which is volume
     */
    public function testMaxPriceByVolume()
    {
        $this->client->request(
            'POST',
            '/parcels/',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Tiny box',
                'weight' => '2',
                'volume' => '0.1',
                'declared_value' => '5'
            ]));
        $result = json_decode($this->client->getResponse()->getContent());
        $this->client->request(
            'GET',
            "/parcels/{$result->id}",
            );
        $this->assertStringContainsString('"quote":"100.00"', $this->client->getResponse());
    }

    /**
     * Test the price is calculated at the maximum rate which is weight
     */
    public function testMaxPriceByWeight()
    {
        $this->client->request(
            'POST',
            '/parcels/',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Tiny box',
                'weight' => '7.86',
                'volume' => '0.001',
                'declared_value' => '4'
            ]));
        $result = json_decode($this->client->getResponse()->getContent());
        $this->client->request(
            'GET',
            "/parcels/{$result->id}",
            );
        $this->assertStringContainsString('"quote":"39.30"', $this->client->getResponse());
    }

    /**
     * Test the update endpoint
     */
    public function testUpdate()
    {
        $newValues = [
            'name' => 'Huge box',
            'weight' => '120',
            'declared_value' => ''
        ];

        $this->client->request(
            'PUT',
            "/parcels/{$this->parcel_id}",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($newValues));

        $this->client->request(
            'GET',
            "/parcels/{$this->parcel_id}",
            );
        $this->assertStringContainsString($newValues['name'], $this->client->getResponse());
    }

    /**
     * Test the delete endpoint
     */
    public function testDelete()
    {
        $this->client->request(
            'DELETE',
            "/parcels/{$this->parcel_id}",
            );
        $this->assertStringContainsString('Parcel deleted!', $this->client->getResponse()->getContent());
    }

    /**
     * Login user as the API is accessable only for user granted with ROLE_USER role
     */
    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewall = 'main';

        $token = serialize(new UsernamePasswordToken('user', null, $firewall, array('ROLE_USER')));
        $session->set('_security_'.$firewall, $token);
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
