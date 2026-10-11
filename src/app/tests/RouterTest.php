<?php
/**
 * PHPUnit Unit Tests
 *
 * Unit tests for \Sleep\Core\Router
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
use Sleepy\MVC\Router;
use Sleepy\MVC\RouteNotFound;
use Sleepy\Core\Loader;

Loader::register();
Loader::addNamespace('Sleepy', dirname(__FILE__) . '/../sleepy');
Loader::addNamespace('Sleepy\Core', dirname(__FILE__) . '/../sleepy/core');
Loader::addNamespace('Sleepy\MVC', dirname(__FILE__) . '/../sleepy/mvc');

require_once dirname(__FILE__) . '/../../settings.php';

/**
 * Router Unit Test
 *
 * @category Test
 * @package  Sleepy
 * @author   Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @license  http://opensource.org/licenses/MIT; MIT
 * @link     https://sleepymustache.com
 */
class RouterTest extends TestCase
{
    /**
     * Setup method
     *
     * @return void
     */
    public function setUp() : void
    {
        Router::route(
            ['users/', '/users/list'],
            function ($route) {
                 echo "1";
            }
        );

        Router::route(
            '/user/{{ id }}/edit/*',
            function ($route) {
                echo "2";
            }
        );

        Router::route(
            '/user/{{ id }}/',
            function ($route) {
                echo "3";
            }
        );

        Router::route(
            '/{{ foo }}/{{ bar }}/{{ foo }}/{{ bar }}',
            function ($route) {
                echo "4";
            }
        );
    }

    /**
     * Tear down Method
     *
     * @return void
     */
    public function tearDown() : void
    {
        Router::reset();
    }

    /**
     * Can Match Simpe Route
     *
     * @return void
     */
    public function testCanMatchSimpleRoute() : void
    {
        ob_start();
        Router::start('/users');
        $this->assertEquals(ob_get_clean(), "1");

        // test for Router::$routeFound
        $this->assertEquals(Router::$routeFound, true);
    }

    /**
     * Can match two level route
     *
     * @return void
     */
    public function testCanMatchTwoLevelRoute() : void
    {
        ob_start();
        Router::start('/user/1');
        $this->assertEquals(ob_get_clean(), "3");
    }

    /**
     * Can match three level route
     *
     * @return void
     */
    public function testCanMatchThreeLevelRoute() : void
    {
        ob_start();
        Router::start('/user/1/edit');
        $this->assertEquals(ob_get_clean(), "2");
    }



    /**
     * Can match four level route
     *
     * @return void
     */
    public function testCanMatchFourLevelRoute() : void
    {
        ob_start();
        Router::start('/user/test/user/test');
        $this->assertEquals(ob_get_clean(), "4");
    }

    /**
     * Can throw exceptions when no route is found
     *
     * @return void
     */
    public function testCanThrowExceptionsWhenNoRouteIsFound() : void
    {
        $this->expectExceptionMessageMatches(
            "/Router: Route not found./"
        );

        Router::start('/user/test/user/test1');
        $this->assertNotEqual(ob_get_clean(), "4");

        // test for Router::$routeFound
        $this->assertEquals(Router::$routeFound, false);
    }

    /**
     * Can parse placeholders
     *
     * @return void
     */
    public function testCanParsePlaceholders() : void
    {
        Router::route(
            '/fruit/{{ color }}/*',
            function ($route) {
                // Test $route Properties
                $this->assertEquals($route->params['color'], 'yellow');
                $this->assertEquals($route->method, 'GET');
                $this->assertEquals($route->splat, 'banana');
                ob_start();
                echo "fruit";
            }
        );

        // Test that route matched
        Router::start('/fruit/yellow/banana');
        $fruit = ob_get_clean();
        $this->assertEquals($fruit, "fruit");
    }

    /**
     * Can change delimeters
     *
     * @return void
     */
    public function testCanChangeDelimeters() : void
    {
        Router::$delimiter = '-';
        Router::route(
            'fruit-{{ color}}',
            function ($route) {
                $this->assertEquals($route->params['color'], 'yellow');
                ob_start();
                echo "fruit";
            }
        );

        Router::start('fruit-yellow');
        $this->assertEquals(ob_get_clean(), "fruit");
        Router::$delimiter = '/';
    }

    /**
     * Can parse querystrings
     *
     * @return void
     */
    public function testCanParseQuerystrings() : void
    {
        Router::$querystring = true;

        Router::route(
            '/fruit/{{color}}',
            function ($route) {
                $this->assertEquals($route->params['color'], 'yellow');
                ob_start();
                echo "fruit";
            }
        );

        Router::start('/?q=/fruit/yellow');
        $this->assertEquals(ob_get_clean(), "fruit");

        Router::$querystring = false;
    }
}