<?php

use WordPressPluginFeed\Generators\Generator;

/**
 * Error and exception tests
 */
class ExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Unsupported format
     */
    public function testUnsupportedFormat()
    {
        putenv('OUTPUT_FORMAT=err');

        try
        {
            $generator = Generator::getInstance();

            $this->fail('Expected exception not thrown');
        }
        catch(Exception $exception)
        {
            $this->assertEquals('Format not supported', $exception->getMessage());
        }
    }
}
