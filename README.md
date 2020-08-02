# PSR-3 compatible logger proxy for Yii2

## Installation

The preferred way to install this extension is through [composer](https://getcomposer.org/download/):

```
composer require vjik/yii2-psr-logger-proxy
```

## Base usage

```php
use Vjik\Yii2\Psr\LoggerProxy\LoggerProxy;

$loggerProxy = new LoggerProxy(\Yii::getLogger());
```

## Advanced usage

```php
use Vjik\Yii2\Psr\LoggerProxy\LoggerProxy;

// Create proxy
$loggerProxy = new LoggerProxy(\Yii::getLogger());

// Set default Yii2 log category (default "application")
$loggerProxy->setDefaultCategory('psr-3');

// Add PSR-3 log context params for use as Yii2 log category
$loggerProxy->addCategoryParam('category');
$loggerProxy->addCategoryParam('type');

// Set custom function for prepare PSR-3 log message tot Yii2 log message
// Return message or NULL for use internal prepare message function.   
$loggerProxy->setPrepareMessage(function ($message, $context) {
    if (isset($context['elapsed'])) {
        return 'Query (' . $context['elapsed'] . ' ms):' . "\n" . $message;
    }
    return null;
});
```
