<?php

namespace Seafile\Client\Tests\Resource;

use GuzzleHttp\Psr7\Response;
use Seafile\Client\Http\Client;
use Seafile\Client\Type\Avatar as AvatarType;
use Seafile\Client\Type\Group as GroupType;
use Seafile\Client\Resource\Avatar as AvatarResource;
use Seafile\Client\Tests\TestCase;

/**
 * Avatar resource test
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
class AvatarTest extends TestCase
{
    /**
     * Test getUserAvatarByEmail()
     *
     * @return void
     */
    public function testGetUserAvatarByEmail()
    {
        $baseUri = 'https://example.com/';
        $resource = 'user';
        $email = 'someone@example.com';
        $size = 80;

        $this->doGetAvatar('getUserAvatarByEmail', $baseUri, $resource, $email, $size);
    }

    /**
     * Test getGroupAvatarByEmail()
     *
     * @return void
     */
    public function testGetGroupAvatarByEmail()
    {
        $baseUri = 'https://example.com/';
        $resource = 'group';
        $id = '1';
        $size = 80;

        $this->doGetAvatar('getGroupAvatar', $baseUri, $resource, (new GroupType)->fromArray(['id' => $id]), $size);
    }

    /**
     * Do actual "get avatar" request
     *
     * @param string           $method   Method name
     * @param string           $baseUri  Base URI
     * @param string           $resource Resource string
     * @param string|GroupType $entity   Resource entity
     * @param int              $size     Avatar size in pixels
     *
     * @return void
     */
    protected function doGetAvatar($method, $baseUri, $resource, $entity, $size)
    {
        $mockedClient = $this->getMock('\Seafile\Client\Http\Client', ['get', 'getConfig']);

        $id = ($entity instanceof GroupType ? $entity->id : $entity);

        $mockedClient->expects($this->any())
            ->method('get')
            ->with($baseUri . '/avatars/' . $resource . '/' . $id . '/resized/' . $size . '/')
            ->willReturn(
                new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    file_get_contents(__DIR__ . '/../../assets/Avatar_get.json')
                )
            );

        $mockedClient->expects($this->any())
            ->method('getConfig')
            ->with('base_uri')
            ->willReturn($baseUri);

        $avatarResource = new AvatarResource($mockedClient);

        $avatarType = $avatarResource->{$method}($entity);

        $this->assertInstanceOf('Seafile\Client\Type\Avatar', $avatarType);
        $this->assertInstanceOf('DateTime', $avatarType->mtime);
        $this->assertSame('1970-01-01T00:00:00+0000', $avatarType->mtime->format(DATE_ISO8601));
    }
}
