<?php declare(strict_types=1);

namespace Webapps\Tests;

require_once( __DIR__ . '/../vendor/autoload.php');

use DMS\PHPUnitExtensions\ArraySubset\Assert;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    use ArraySubsetAsserts;
}
