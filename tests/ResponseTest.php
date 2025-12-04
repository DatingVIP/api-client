<?php

declare(strict_types=1);

namespace DatingVIP\API\Tests;

use DatingVIP\API\Response;

class ResponseTest extends TestCase
{
    public function testConstructorDefaultJson(): void
    {
        $data = json_encode(['result' => ['id' => 1]]);
        $response = new Response($data);

        $this->assertSame($data, $response->getRawResponse());
        $this->assertSame(['result' => ['id' => 1]], $response->getRawData());
    }

    public function testConstructorWithNvpFormat(): void
    {
        $data = 'key1=value1&key2=value2';
        $response = new Response($data, 'nvp');

        $this->assertSame($data, $response->getRawResponse());
        $this->assertSame(['key1' => 'value1', 'key2' => 'value2'], $response->getRawData());
    }

    public function testConstructorWithEmptyResponse(): void
    {
        $response = new Response('');

        $this->assertSame('', $response->getRawResponse());
        $this->assertSame([], $response->getRawData());
    }

    public function testConstructorWithInvalidJson(): void
    {
        $response = new Response('{invalid json}');

        $this->assertSame('{invalid json}', $response->getRawResponse());
        $error = $response->getRawError();
        $this->assertNotEmpty($error);
        $this->assertArrayHasKey('error', $error);
        $this->assertArrayHasKey('msg', $error);
    }

    public function testGetReturnsJsonString(): void
    {
        $data = json_encode(['test' => 'value']);
        $response = new Response($data);

        $result = $response->get();
        $this->assertIsString($result);
        $this->assertJson($result);
    }

    public function testGetReturnsNvpString(): void
    {
        $data = 'key=value&foo=bar';
        $response = new Response($data, 'nvp');

        $result = $response->get();
        $this->assertIsString($result);
        $this->assertStringContainsString('key=value', $result);
    }

    public function testSetWithValidJson(): void
    {
        $response = new Response();
        $json = json_encode(['result' => 'success']);
        $result = $response->set($json);

        $this->assertTrue($result);
        $this->assertSame(['result' => 'success'], $response->getRawData());
        $this->assertEmpty($response->getRawError());
    }

    public function testSetWithInvalidJson(): void
    {
        $response = new Response();
        $result = $response->set('{invalid}');

        $this->assertFalse($result);
        $error = $response->getRawError();
        $this->assertNotEmpty($error);
        $this->assertSame(JSON_ERROR_SYNTAX, $error['error']);
    }

    public function testSetWithNvp(): void
    {
        $response = new Response('', 'nvp');
        $result = $response->set('param1=val1&param2=val2');

        $this->assertTrue($result);
        $this->assertSame(['param1' => 'val1', 'param2' => 'val2'], $response->getRawData());
    }

    public function testSetResultAndGetResult(): void
    {
        $response = new Response();
        $data = ['id' => 1, 'name' => 'test'];
        $response->setResult($data);

        $this->assertTrue($response->hasResult());
        $this->assertSame($data, $response->getResult());
    }

    public function testGetResultReturnsNullWhenNotSet(): void
    {
        $response = new Response();
        $this->assertNull($response->getResult());
    }

    public function testSetTextsAndGetTexts(): void
    {
        $response = new Response();
        $texts = ['en' => 'Hello', 'es' => 'Hola'];
        $response->setTexts($texts);

        $this->assertTrue($response->hasTexts());
        $this->assertSame($texts, $response->getTexts());
    }

    public function testGetTextsReturnsNullWhenNotSet(): void
    {
        $response = new Response();
        $this->assertNull($response->getTexts());
    }

    public function testSetStatusAndGetStatus(): void
    {
        $response = new Response();
        $response->setStatus('success');

        $this->assertSame('success', $response->getStatus());
    }

    public function testSetErrorsAndGetErrors(): void
    {
        $response = new Response();
        $errors = ['error1', 'error2'];
        $response->setErrors($errors);

        $this->assertTrue($response->hasErrors());
        $this->assertSame($errors, $response->getErrors());
    }

    public function testHasErrorsReturnsFalseWhenNotSet(): void
    {
        $response = new Response();
        $this->assertFalse($response->hasErrors());
    }

    public function testSetMessagesAndGetMessages(): void
    {
        $response = new Response();
        $messages = ['msg1', 'msg2'];
        $response->setMessages($messages);

        $this->assertTrue($response->hasMessages());
        $this->assertSame($messages, $response->getMessages());
    }

    public function testSetWarningsAndGetWarnings(): void
    {
        $response = new Response();
        $warnings = ['warning1'];
        $response->setWarnings($warnings);

        $this->assertTrue($response->hasWarnings());
        $this->assertSame($warnings, $response->getWarnings());
    }

    public function testSetAnnouncementsAndGetAnnouncements(): void
    {
        $response = new Response();
        $announcements = ['announcement1'];
        $response->setAnnouncements($announcements);

        $this->assertTrue($response->hasAnnouncements());
        $this->assertSame($announcements, $response->getAnnouncements());
    }

    public function testSetAppErrorsAndGetAppErrors(): void
    {
        $response = new Response();
        $appErrors = ['app_error1'];
        $response->setAppErrors($appErrors);

        $this->assertTrue($response->hasAppErrors());
        $this->assertSame($appErrors, $response->getAppErrors());
    }

    public function testHasMessagesReturnsFalseWhenNotSet(): void
    {
        $response = new Response();
        $this->assertFalse($response->hasMessages());
    }

    public function testHasWarningsReturnsFalseWhenNotSet(): void
    {
        $response = new Response();
        $this->assertFalse($response->hasWarnings());
    }

    public function testHasAnnouncementsReturnsFalseWhenNotSet(): void
    {
        $response = new Response();
        $this->assertFalse($response->hasAnnouncements());
    }

    public function testHasAppErrorsReturnsFalseWhenNotSet(): void
    {
        $response = new Response();
        $this->assertFalse($response->hasAppErrors());
    }

    public function testGetRawResponse(): void
    {
        $rawData = '{"test":"value"}';
        $response = new Response($rawData);
        $this->assertSame($rawData, $response->getRawResponse());
    }

    public function testGetRawData(): void
    {
        $response = new Response('{"key":"value"}');
        $this->assertSame(['key' => 'value'], $response->getRawData());
    }

    public function testGetRawErrorWhenNoError(): void
    {
        $response = new Response('{"valid":"json"}');
        $this->assertEmpty($response->getRawError());
    }

    public function testNestedArraysInResult(): void
    {
        $data = ['result' => ['nested' => ['deep' => 'value']]];
        $response = new Response(json_encode($data));

        $this->assertSame($data['result'], $response->getResult());
    }

    public function testSpecialCharactersInData(): void
    {
        $data = ['text' => 'Hello "World" & Co.'];
        $response = new Response(json_encode($data));

        $this->assertSame($data, $response->getRawData());
    }

    public function testSetEmptyJsonString(): void
    {
        $response = new Response();
        $result = $response->set('');

        $this->assertTrue($result);
        $this->assertSame([], $response->getRawData());
    }

    public function testMultipleMetaFields(): void
    {
        $response = new Response();
        $response->setStatus('ok');
        $response->setErrors(['err1']);
        $response->setMessages(['msg1']);
        $response->setWarnings(['warn1']);

        $this->assertSame('ok', $response->getStatus());
        $this->assertSame(['err1'], $response->getErrors());
        $this->assertSame(['msg1'], $response->getMessages());
        $this->assertSame(['warn1'], $response->getWarnings());
    }

    public function testCompleteResponseStructure(): void
    {
        $data = [
            'result' => ['id' => 1],
            'texts' => ['en' => 'Hello'],
            'meta' => [
                'status' => 'success',
                'errors' => [],
                'messages' => ['Operation completed'],
                'warnings' => [],
                'announcements' => ['New feature!'],
                'app' => ['debug' => 'info']
            ]
        ];

        $response = new Response(json_encode($data));

        $this->assertTrue($response->hasResult());
        $this->assertTrue($response->hasTexts());
        $this->assertSame(['id' => 1], $response->getResult());
        $this->assertSame(['en' => 'Hello'], $response->getTexts());
        $this->assertSame('success', $response->getStatus());
        $this->assertSame(['Operation completed'], $response->getMessages());
        $this->assertSame(['New feature!'], $response->getAnnouncements());
    }
}
