<?php

namespace Seafile\Tests;

use GuzzleHttp\Psr7\Response;
use Seafile\Http\Client;
use Seafile\Resource\Directory;
use Seafile\Resource\Library;
use Seafile\Tests\TestCase;

/**
 * Directory resource test
 *
 * PHP version 5
 *
 * @category  API
 * @package   Seafile\Resource
 * @author    Rene Schmidt DevOps UG (haftungsbeschränkt) & Co. KG <rene@reneschmidt.de>
 * @copyright 2015 Rene Schmidt DevOps UG (haftungsbeschränkt) & Co. KG <rene@reneschmidt.de>
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/rene-s/seafile-php-sdk
 */
class DirectoryTest extends TestCase
{
    /**
     * getAll()
     *
     * @return void
     */
    public function testGetAll()
    {
        $directoryResource = new Directory($this->getMockedClient(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                file_get_contents(__DIR__ . '/../../assets/DirectoryTest_getAll.json')
            )
        ));

        $directoryItems = $directoryResource->getAll(new \Seafile\Type\Library());

        $this->assertInternalType('array', $directoryItems);

        foreach ($directoryItems as $directoryItem) {
            $this->assertInstanceOf('Seafile\Type\DirectoryItem', $directoryItem);
        }
    }

    /**
     * getAll() with directory path
     *
     * @return void
     */
    public function testGetAllWithDir()
    {
        $dir = '/' . uniqid('test_', true);

        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            file_get_contents(__DIR__ . '/../../assets/DirectoryTest_getAll.json')
        );

        $mockedClient = $this->getMockBuilder('\Seafile\Http\Client')->getMock();

        $mockedClient->method('getConfig')->willReturn('http://example.com/');

        $mockedClient->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('GET'),
                $this->equalTo('http://example.com/repos/some-crazy-id/dir/'),
                $this->equalTo(['query' => ['p' => $dir]])
            )->willReturn($response);

        /**
         * @var Client $mockedClient
         */
        $directoryResource = new Directory($mockedClient);
        $lib = new \Seafile\Type\Library();
        $lib->id = 'some-crazy-id';

        $directoryResource->getAll($lib, $dir);
    }
}