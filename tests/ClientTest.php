<?php

declare(strict_types=1);

namespace DatingVIP\API\Tests;

use DatingVIP\API\Client;
use DatingVIP\API\Command;
use Exception;
use ReflectionClass;
use RuntimeException;

class ClientTest extends TestCase
{
    public function testSetUrl(): void
    {
        $client = new Client();
        $result = $client->setUrl('https://api.example.com/endpoint');

        $this->assertTrue($result);
    }

    public function testSetUrlReturnsFalseForEmptyString(): void
    {
        $client = new Client();
        $result = $client->setUrl('');

        $this->assertFalse($result);
    }

    public function testSetAuth(): void
    {
        $client = new Client();
        $result = $client->setAuth('username', 'password');

        $this->assertTrue($result);
    }

    public function testSetAuthReturnsFalseWhenIncomplete(): void
    {
        $client = new Client();

        $result1 = $client->setAuth('', 'password');
        $this->assertFalse($result1);

        $result2 = $client->setAuth('username', '');
        $this->assertFalse($result2);

        $result3 = $client->setAuth('', '');
        $this->assertFalse($result3);
    }

    public function testSetTimeout(): void
    {
        $client = new Client();
        $result = $client->setTimeout(30);

        $this->assertSame(30, $result);
    }

    public function testSetTimeoutReturnsDefaultForZero(): void
    {
        $client = new Client();
        $result = $client->setTimeout(0);

        $this->assertSame(5, $result);
    }

    public function testSetTimeoutReturnsDefaultForNegative(): void
    {
        $client = new Client();
        $result = $client->setTimeout(-10);

        $this->assertSame(5, $result);
    }

    public function testSetCookieStorage(): void
    {
        $client = new Client();
        $result = $client->setCookieStorage('/tmp/cookies.txt');

        $this->assertTrue($result);
    }

    public function testSetCookieStorageReturnsFalseForEmpty(): void
    {
        $client = new Client();
        $result = $client->setCookieStorage('');

        $this->assertFalse($result);
    }

    public function testExecuteThrowsExceptionForInvalidCommand(): void
    {
        $client = new Client();
        $client->setUrl('https://api.example.com');

        $invalidCommand = new Command('invalid');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid API command supplied');

        $client->execute($invalidCommand);
    }

    public function testExecuteWithValidCommand(): void
    {
        $client = new Client();
        $client->setUrl('https://api.example.com/api.json');
        $command = new Command('test.method', ['param' => 'value']);

        $this->expectException(RuntimeException::class);
        $client->execute($command);
    }

    public function testGetUrlWithCommand(): void
    {
        $client = new class extends Client {
            public function testGetUrl(Command $command): string
            {
                return $this->getUrl($command);
            }
        };

        $client->setUrl('https://api.example.com/endpoint');
        $command = new Command('controller.action');

        $url = $client->testGetUrl($command);

        $this->assertStringContainsString('https://api.example.com/endpoint', $url);
        $this->assertStringContainsString('cmd=controller.action', $url);
    }

    public function testGetUrlAddsQuestionMarkWhenNoQueryString(): void
    {
        $client = new class extends Client {
            public function testGetUrl(Command $command): string
            {
                return $this->getUrl($command);
            }
        };

        $client->setUrl('https://api.example.com/endpoint');
        $command = new Command('test.method');

        $url = $client->testGetUrl($command);

        $this->assertStringContainsString('?cmd=', $url);
    }

    public function testGetUrlAddsAmpersandWhenQueryStringExists(): void
    {
        $client = new class extends Client {
            public function testGetUrl(Command $command): string
            {
                return $this->getUrl($command);
            }
        };

        $client->setUrl('https://api.example.com/endpoint?param=value');
        $command = new Command('test.method');

        $url = $client->testGetUrl($command);

        $this->assertStringContainsString('&cmd=', $url);
    }

    public function testMultipleSettersChaining(): void
    {
        $client = new Client();

        $client->setUrl('https://api.example.com');
        $client->setAuth('user', 'pass');
        $client->setTimeout(60);
        $client->setCookieStorage('/tmp/cookies.txt');

        $this->assertTrue(true);
    }

    public function testUserAgentVersion(): void
    {
        $reflection = new ReflectionClass(Client::class);
        $property = $reflection->getProperty('user_agent');

        $client = new Client();
        $userAgent = $property->getValue($client);

        $this->assertStringContainsString('2.0.0', $userAgent);
        $this->assertStringContainsString('DatingVIP-API', $userAgent);
    }
}
