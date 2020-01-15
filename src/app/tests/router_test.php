<?php
  require_once('../core/class.router.php');

  use \Sleepy\Router;

  class TestOfRouter extends UnitTestCase {
    function setUp() {
      Router::route(['users/', '/users/list'], function ($route) {
        echo "1";
      });

      Router::route('/user/{{ id }}/edit/*', function ($route) {
        echo "2";
      });

      Router::route('/user/{{ id }}/', function ($route) {
        echo "3";
      });

      Router::route('/{{ foo }}/{{ bar }}/{{ foo }}/{{ bar }}', function ($route) {
        echo "4";
      });
    }

    function tearDown() {
      Router::reset();
    }

    function testRouterMatch1() {
      ob_start();
      Router::start('/users');
      $this->assertEqual(ob_get_clean(), "1");

      // test for Router::$routeFound
      $this->assertEqual(Router::$routeFound, true);
    }

    function testRouterMatch2() {
      ob_start();
      Router::start('/user/1/edit');
      $this->assertEqual(ob_get_clean(), "2");
    }

    function testRouterMatch3() {
      ob_start();
      Router::start('/user/1');
      $this->assertEqual(ob_get_clean(), "3");
    }

    function testRouterMatch4() {
      ob_start();
      Router::start('/user/test/user/test');
      $this->assertEqual(ob_get_clean(), "4");
    }

    function testRouterMismatch4() {
      $this->expectException(new \Sleepy\RouteNotFound("Router: Route not found."));
      ob_start();
      Router::start('/user/test/user/test1');
      $this->assertNotEqual(ob_get_clean(), "4");

      // test for Router::$routeFound
      $this->assertEqual(Router::$routeFound, false);
    }

    function testRouterPlaceholders() {
      Router::route('/fruit/{{ color }}/*', function ($route) {
        // Test $route Properties
        $this->assertEqual($route->params['color'], 'yellow');
        $this->assertEqual($route->method, 'GET');
        $this->assertEqual($route->splat, 'banana');
        ob_start();
        echo "fruit";
      });

      // Test that route matched
      Router::start('/fruit/yellow/banana');
      $fruit = ob_get_clean();
      $this->assertEqual($fruit, "fruit");
    }

    function testRouterDelimeter() {
      Router::$delimiter = '-';
      Router::route('fruit-{{ color}}', function ($route) {
        $this->assertEqual($route->params['color'], 'yellow');
        ob_start();
        echo "fruit";
      });

      Router::start('fruit-yellow');
      $this->assertEqual(ob_get_clean(), "fruit");
      Router::$delimiter = '/';
    }

    function testRouterQuerystring() {
      Router::$querystring = true;

      Router::route('/fruit/{{color}}', function ($route) {
        $this->assertEqual($route->params['color'], 'yellow');
        ob_start();
        echo "fruit";
      });

      Router::start('/?q=/fruit/yellow');
      $this->assertEqual(ob_get_clean(), "fruit");

      Router::$querystring = false;
    }
  }