<?php
	//require_once('../include/class.hooks.php');
	require_once('../core/class.router.php');

	class TestOfRouter extends UnitTestCase {
		function setUp() {
			\Sleepy\Router::route(['users/', '/users/list'], function ($route) {
				echo "1";
			});

			\Sleepy\Router::route('/user/{{ id }}/edit/*', function ($route) {
				echo "2";
			});

			\Sleepy\Router::route('/user/{{ id }}/', function ($route) {
				echo "3";
			});

			\Sleepy\Router::route('/{{ foo }}/{{ bar }}/{{ foo }}/{{ bar }}', function ($route) {
				echo "4";
			});
		}

		function tearDown() {
			\Sleepy\Router::reset();
		}
		function testRouterMatch1() {
			ob_start();
			\Sleepy\Router::start('/users');
			$this->assertEqual(ob_get_clean(), "1");

			// test for \Sleepy\Router::$routeFound
			$this->assertEqual(\Sleepy\Router::$routeFound, true);
		}

		function testRouterMatch2() {
			ob_start();
			\Sleepy\Router::start('/user/1/edit');
			$this->assertEqual(ob_get_clean(), "2");
		}

		function testRouterMatch3() {
			ob_start();
			\Sleepy\Router::start('/user/1');
			$this->assertEqual(ob_get_clean(), "3");
		}

		function testRouterMatch4() {
			ob_start();
			\Sleepy\Router::start('/user/test/user/test');
			$this->assertEqual(ob_get_clean(), "4");
		}

		function testRouterMisatch4() {
			ob_start();
			\Sleepy\Router::start('/user/test/user/test1');
			$this->assertNotEqual(ob_get_clean(), "4");

			// test for \Sleepy\Router::$routeFound
			$this->assertEqual(\Sleepy\Router::$routeFound, false);
		}

		function testRouterPlaceholders() {
			\Sleepy\Router::route('/fruit/{{ color }}/*', function ($route) {
				// Test $route Properties
				$this->assertEqual($route->params['color'], 'yellow');
				$this->assertEqual($route->method, 'GET');
				$this->assertEqual($route->splat, 'banana');

				echo "fruit";
			});

			// Test that route matched
			ob_start();
			\Sleepy\Router::start('/fruit/yellow/banana');
			$this->assertEqual(ob_get_clean(), "fruit");
		}

		function testRouterDelimeter() {
			\Sleepy\Router::$delimiter = '-';

			\Sleepy\Router::route('fruit-{{ color}}', function ($route) {
				$this->assertEqual($route->params['color'], 'yellow');

				echo "fruit";
			});

			ob_start();
			\Sleepy\Router::start('fruit-yellow');
			$this->assertEqual(ob_get_clean(), "fruit");

			\Sleepy\Router::$delimiter = '/';
		}

		function testRouterQuerystring() {
			\Sleepy\Router::$querystring = true;

			\Sleepy\Router::route('/fruit/{{color}}', function ($route) {
				$this->assertEqual($route->params['color'], 'yellow');

				echo "fruit";
			});

			ob_start();
			\Sleepy\Router::start('/?q=/fruit/yellow');
			$this->assertEqual(ob_get_clean(), "fruit");

			\Sleepy\Router::$querystring = false;
		}
	}