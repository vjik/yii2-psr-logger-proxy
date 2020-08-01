<?php

declare(strict_types=1);

namespace Vjik\Yii2\Psr\LoggerProxy;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use yii\helpers\ArrayHelper;
use yii\log\Logger;

class LoggerProxy implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @var string[]
     */
    protected $categoryParams = [];

    /**
     * @var string
     */
    protected $defaultCategory = 'application';

    protected $levelMap = [
        LogLevel::EMERGENCY => Logger::LEVEL_ERROR,
        LogLevel::ALERT => Logger::LEVEL_ERROR,
        LogLevel::CRITICAL => Logger::LEVEL_ERROR,
        LogLevel::ERROR => Logger::LEVEL_ERROR,
        LogLevel::WARNING => Logger::LEVEL_WARNING,
        LogLevel::NOTICE => Logger::LEVEL_INFO,
        LogLevel::INFO => Logger::LEVEL_INFO,
        LogLevel::DEBUG => Logger::LEVEL_TRACE,
    ];

    protected $defaultLevel = Logger::LEVEL_INFO;

    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $param
     * @return void
     */
    public function addCategoryParam(string $param)
    {
        $this->categoryParams[] = $param;
    }

    /**
     * @param string $category
     * @return void
     */
    public function setDefaultCategory(string $category)
    {
        $this->defaultCategory = $category;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->logger->log(
            $this->prepareMessage($message, $context),
            $this->prepareLevel($level),
            $this->prepareCategory($context)
        );
    }

    protected function prepareMessage(string $message, array $context): string
    {
        return preg_replace_callback('/{([\w.]+)}/', static function ($matches) use ($context) {
            $placeholderName = $matches[1];
            if (isset($context[$placeholderName])) {
                return static::getString($context[$placeholderName]);
            }
            return $matches[0];
        }, $message);
    }

    protected function prepareLevel($level): int
    {
        return ArrayHelper::getValue($this->levelMap, $level, $this->defaultLevel);
    }

    protected function prepareCategory(array $context): string
    {
        foreach ($this->categoryParams as $key) {
            if (isset($context[$key])) {
                return static::getString($context[$key]);
            }
        }
        return $this->defaultCategory;
    }

    protected static function getString($value): string
    {
        if (
            !is_array($value) &&
            (!is_object($value) || method_exists($value, '__toString'))
        ) {
            return (string)$value;
        }
        return '';
    }
}
