<?php
/**
 * PHPUnit Unit Tests
 *
 * Unit tests for \Sleep\Core\Hook
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
use Sleepy\Core\Hook;
use Sleepy\Core\Loader;

Loader::register();
Loader::addNamespace('Sleepy', dirname(__FILE__) . '/../sleepy');
Loader::addNamespace('Sleepy\Core', dirname(__FILE__) . '/../sleepy/core');


require_once dirname(__FILE__) . '/../../settings.php';

/**
 * Hook Unit Test
 *
 * @category Test
 * @package  Sleepy
 * @author   Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @license  http://opensource.org/licenses/MIT; MIT
 * @link     https://sleepymustache.com
 */
class HookTest extends TestCase
{
    /**
     * Test if Actions work
     *
     * @return void
     */
    public function testCanAddAction() : void
    {
        /**
         * Run if action works
         *
         * @return void
         */
        function action()
        {
            echo "YES!";
        }

        Hook::doAction('TestHook', 'action');

        // lets capture what the action does
        ob_start();
        Hook::addAction('TestHook');
        $passed = ob_get_clean();

        // Did the action do anything?
        $this->assertTrue(strlen($passed) > 0);
    }

    /**
     * Test Hook Filters
     *
     * @return void
     */
    function testCanFiltersBeAdded()
    {
        /**
         * Function to run if filter works
         *
         * @param string $arg The unfiltered string
         *
         * @return void
         */
        function filter($arg)
        {
            return $arg . " Smith";
        }

        Hook::applyFilter('TestFilter', 'filter');

        // Did the filter do anything?
        $this->assertTrue(Hook::addFilter('TestFilter', "John") === "John Smith");
    }
}