<?php

declare(strict_types=1);

namespace DatingVIP\API\Tests;

use DatingVIP\API\Command;

class CommandTest extends TestCase
{
    public function testConstructorEmpty(): void
    {
        $command = new Command();

        $this->assertSame('', $command->getName());
        $this->assertSame([], $command->getData());
        $this->assertFalse($command->isValid());
    }

    public function testConstructorWithName(): void
    {
        $command = new Command('controller.action');

        $this->assertSame('controller.action', $command->getName());
        $this->assertSame([], $command->getData());
        $this->assertTrue($command->isValid());
    }

    public function testConstructorWithNameAndData(): void
    {
        $data = ['key' => 'value', 'foo' => 'bar'];
        $command = new Command('controller.action', $data);

        $this->assertSame('controller.action', $command->getName());
        $this->assertSame($data, $command->getData());
        $this->assertTrue($command->isValid());
    }

    public function testSetWithValidCommand(): void
    {
        $command = new Command();
        $result = $command->set('test.method', ['param' => 'value']);

        $this->assertTrue($result);
        $this->assertSame('test.method', $command->getName());
        $this->assertSame(['param' => 'value'], $command->getData());
    }

    public function testSetWithInvalidCommand(): void
    {
        $command = new Command();
        $result = $command->set('invalid', []);

        $this->assertFalse($result);
        $this->assertFalse($command->isValid());
    }

    public function testIsValidWithDot(): void
    {
        $command = new Command('controller.action');
        $this->assertTrue($command->isValid());
    }

    public function testIsValidWithMultipleDots(): void
    {
        $command = new Command('module.controller.action');
        $this->assertTrue($command->isValid());
    }

    public function testIsValidWithoutDot(): void
    {
        $command = new Command('invalid');
        $this->assertFalse($command->isValid());
    }

    public function testIsValidWithEmptyName(): void
    {
        $command = new Command('');
        $this->assertFalse($command->isValid());
    }

    public function testReservedKeyFiltering(): void
    {
        $data = [
            'param1' => 'value1',
            Command::VAR_CONTROLLER => 'should_be_removed',
            'param2' => 'value2'
        ];

        $command = new Command('test.method', $data);

        $this->assertArrayNotHasKey(Command::VAR_CONTROLLER, $command->getData());
        $this->assertArrayHasKey('param1', $command->getData());
        $this->assertArrayHasKey('param2', $command->getData());
    }

    public function testGetName(): void
    {
        $command = new Command('controller.action');
        $this->assertSame('controller.action', $command->getName());
    }

    public function testGetData(): void
    {
        $data = ['key' => 'value'];
        $command = new Command('test.method', $data);
        $this->assertSame($data, $command->getData());
    }

    public function testGetDataReturnsEmptyArray(): void
    {
        $command = new Command('test.method');
        $this->assertSame([], $command->getData());
    }

    public function testSpecialCharactersInName(): void
    {
        $command = new Command('user.get-profile');
        $this->assertTrue($command->isValid());
        $this->assertSame('user.get-profile', $command->getName());
    }

    public function testSetUpdatesNameAndData(): void
    {
        $command = new Command('old.method', ['old' => 'data']);
        $command->set('new.method', ['new' => 'data']);

        $this->assertSame('new.method', $command->getName());
        $this->assertSame(['new' => 'data'], $command->getData());
    }
}
