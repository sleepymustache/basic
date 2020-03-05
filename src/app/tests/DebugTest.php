<?php
/**
 * PHPUnit Unit Tests
 *
 * Unit tests for \Sleep\Core\Debug
 *
 * php version 7.0.0
 *
 * @category Test
 * @package  Sleepy
 * @author   Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @license  http://opensource.org/licenses/MIT; MIT
 * @link     https://sleepymustache.com
 */

require_once dirname(__FILE__) . '/../sleepy/core/Loader.php';

use PHPUnit\Framework\TestCase;
use Sleepy\Core\SM;
use Sleepy\Core\Debug;
use Sleepy\Core\Loader;

Loader::register();
Loader::addNamespace('Sleepy', dirname(__FILE__) . '/../sleepy');
Loader::addNamespace('Sleepy\Core', dirname(__FILE__) . '/../sleepy/core');


require_once dirname(__FILE__) . '/../../settings.php';

/**
 * Debugging Unit Test
 *
 * @category Test
 * @package  Sleepy
 * @author   Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @license  http://opensource.org/licenses/MIT; MIT
 * @link     https://sleepymustache.com
 */
class DebugTest extends TestCase
{

    /**
     * Setup before Class
     *
     * @return void
     */
    public static function setUpBeforeClass() : void
    {
    }

    /**
     * Reset the Debug class in case someone else wants to use it
     *
     * @return void
     */
    function tearDown() : void
    {
        Debug::disable();
        Debug::$enableShow = true;
    }

    /**
     * There is only a static instance of Debug
     *
     * @return void
     */
    function testDebugHighlander()
    {
        $this->assertFalse(is_callable(array('Debug', '__construct')));
        $this->assertFalse(is_callable(array('Debug', '__clone')));
    }

    /**
     * Debug should have show enabled by default
     * Make sure debug output is wrapped in pre
     *
     * @return void
     */
    function testDebugShow()
    {
        ob_start();
        Debug::out('Testing, testing, 123.');
        $output = ob_get_clean();
        $this->assertMatchesRegularExpression(
            '/<pre>Testing, testing, 123.<\/pre>/is',
            $output
        );
    }

    /**
     * Make sure an email is sent
     *
     * @return void
     */
    function testDebugEmail()
    {
        Debug::$enableShow = false;
        Debug::$enableSend = true;
        Debug::out('Testing, testing, 123');
        $this->assertTrue(Debug::sendEmail());
        Debug::$enableSend = false;
    }

    /**
     * No output when $enabled_show is false
     *
     * @return void
     */
    function testDebugDoNotShow()
    {
        Debug::$enableShow = false;
        ob_start();
        Debug::out('Testing, testing, 123');
        $output = ob_get_clean();
        $this->assertEquals('', $output);
    }

    /**
     * No email is sent with $enabled_send is false
     *
     * @return void
     */
    function testDebugDoNotEmail()
    {
        Debug::$enableSend = false;
        $this->assertFalse(Debug::sendEmail());
    }

    /**
     * Database logging is working
     *
     * No database logging occurs when $enabled_log is false
     *
     * @return void
     */
    function testDBLogging()
    {
        if (strlen(DBPASS) > 0) {
            Debug::$enableShow = false;
            Debug::$enableLog = true;
            $this->assertTrue(Debug::out('Testing, testing, 123'));
        } else {
            $this->assertTrue(strlen(DBPASS) == 0);
        }
    }
}