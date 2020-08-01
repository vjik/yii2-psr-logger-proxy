<?php

namespace Vjik\Yii2\Psr\LoggerProxyTests;

use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Vjik\Yii2\Psr\LoggerProxy\LoggerProxy;
use Yii;
use yii\log\Logger;

class LoggerProxyTest extends TestCase
{

    public function testBase()
    {
        $logger = Yii::getLogger();
        $proxy = new LoggerProxy($logger);

        $proxy->log(LogLevel::ERROR, 'test');
        $message = end($logger->messages);

        $this->assertSame('test', $message[0]);
        $this->assertSame(Logger::LEVEL_ERROR, $message[1]);
        $this->assertSame('application', $message[2]);
    }

    public function testDefaultCategory()
    {
        $logger = Yii::getLogger();
        $proxy = new LoggerProxy($logger);
        $proxy->setDefaultCategory('psr3');

        $proxy->log(LogLevel::INFO, 'test');
        $message = end($logger->messages);

        $this->assertSame('psr3', $message[2]);
    }

    public function testCategoryContext()
    {
        $logger = Yii::getLogger();
        $proxy = new LoggerProxy($logger);
        $proxy->addCategoryParam('category');

        $proxy->log(LogLevel::INFO, 'test', ['category' => 'psr3']);
        $message = end($logger->messages);

        $this->assertSame('psr3', $message[2]);
    }

    public function testMessage()
    {
        $logger = Yii::getLogger();
        $proxy = new LoggerProxy($logger);

        $proxy->log(LogLevel::WARNING, 'Hello, {name}!', ['name' => 'Yoda']);
        $message = end($logger->messages);

        $this->assertSame('Hello, Yoda!', $message[0]);
    }
}
