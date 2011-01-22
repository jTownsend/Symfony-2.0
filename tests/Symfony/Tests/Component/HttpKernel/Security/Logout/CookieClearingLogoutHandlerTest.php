<?php

namespace Symfony\Tests\Component\HttpKernel\Security\Logout;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Security\Logout\CookieClearingLogoutHandler;

class CookieClearingLogoutHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $cookieNames = array('foo', 'foo2', 'foo3');

        $handler = new CookieClearingLogoutHandler($cookieNames);

        $this->assertEquals($cookieNames, $handler->getCookieNames());
    }

    public function testLogout()
    {
        $request = new Request();
        $response = new Response();
        $token = $this->getMock('Symfony\Component\Security\Authentication\Token\TokenInterface');

        $handler = new CookieClearingLogoutHandler(array('foo', 'foo2'));

        $this->assertFalse($response->headers->hasCookie('foo'));

        $handler->logout($request, $response, $token);

        $cookies = $response->headers->getCookies();
        $this->assertEquals(2, count($cookies));

        $cookie = $cookies['foo'];
        $this->assertEquals('foo', $cookie->getName());
        $this->assertTrue($cookie->isCleared());

        $cookie = $cookies['foo2'];
        $this->assertStringStartsWith('foo2', $cookie->getName());
        $this->assertTrue($cookie->isCleared());
    }
}