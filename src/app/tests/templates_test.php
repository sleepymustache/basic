<?php
	require_once('../core/class.template.php');


	class TestOfTemplate extends UnitTestCase {

		// replace placeholder with bind
		// make sure placeholders are case insensitive
		// trim whitespace
		function testBind() {
			$t = new \Sleepy\Template();
			$t->directory = "./templates/";
			$t->setTemplate('bind');
			$t->bind('   naMe ', 'Sleepy Mustache!');
			ob_start();
			$t->show();
			$name = ob_get_clean();
			$this->assertEqual($name, 'Sleepy Mustache!');
		}

		// bind large chunks w/ bindStart/bindEnd
		function testBindChunk() {
			$t = new \Sleepy\Template();
			$t->directory = "./templates/";
			$t->setTemplate('bind');
			$t->bindStart();
			?>
			Sleepy Mustache!
			<?php
			$t->bindStop('NaMe ');
			ob_start();
			$t->show();
			$name = ob_get_clean();
			$this->assertEqual(trim($name), "Sleepy Mustache!");
		}
		// Test placeholder hooks
		function testPlaceholderHooks() {
			// function to run if filter works
			function render_placeholder_filter($x) {
				return $x . "!";
			}

			\Sleepy\Hook::applyFilter('render_placeholder_name', 'render_placeholder_filter');

			// lets capture what the \Sleepy\Template
			$t = new \Sleepy\Template();
			$t->directory = "./templates/";
			$t->setTemplate('bind');
			$t->bind('   naMe ', 'Sleepy Mustache!');
			ob_start();
			$t->show();
			$name = ob_get_clean();

			// test if the extra bang was added
			$this->assertEqual($name, 'Sleepy Mustache!!');
		}

		// Test placeholder hooks w/ parameters
		function testPlaceholderHookParameters() {
			// function to run if filter works
			function render_placeholder_parameter($x) {
				$colors = array();

				foreach (func_get_args() as $fruit) {
					switch ($fruit) {
					case 'apple':
						$colors[] = 'Red';
						break;
					case 'banana':
						$colors[] = 'Yellow';
						break;
					}
				}

				return implode($colors, " ");
			}

			\Sleepy\Hook::applyFilter('render_placeholder_colorof', 'render_placeholder_parameter');

			// lets capture what the \Sleepy\Template
			$t = new \Sleepy\Template();
			$t->directory = "./templates/";
			$t->setTemplate('parameters');
			ob_start();
			$t->show();
			$color = ob_get_clean();

			// test if the extra bang was added
			$this->assertEqual($color, 'Yellow Red');
		}

		// Test #each placeholder
		function testEach() {
			$t = new \Sleepy\Template();
			$t->directory = "./templates/";
			$t->setTemplate('each');
			$t->bind('poem', array(
				array(
					'number' => 1,
					'text'   => 'Roses are red.'
				), array(
					'number' => 2,
					'text'   => 'Violets are blue.'
				), array(
					'number' => 3,
					'text'   => 'Sugar is sweet,'
				), array(
					'number' => 4,
					'text'   => 'and so are you!'
				)
			));
			ob_start();
			$t->show();
			$poem = ob_get_clean();
			$this->assertPattern("/1. Roses are red(.*)?2. Violets are blue/is", $poem);
		}

		// Test #include placeholder
		// Test hooks inside of included \Sleepy\Templates
		function testInclude() {
			$t = new \Sleepy\Template();
			$t->directory = "./templates/";
			$t->setTemplate('include');
			$t->bind('   naMe ', 'Sleepy Mustache!');
			ob_start();
			$t->show();
			$name = ob_get_clean();
			// The double !! is because the hook above...
			$this->assertEqual($name, 'Sleepy Mustache!!');
		}

		// Test inline placeholders
		function testTwoInline() {
			$t = new \Sleepy\Template();
			$t->directory = "./templates/";
			$t->setTemplate('two-inline');
			$t->bind('firstname', 'Jaime');
			$t->bind('lastname', 'Rodriguez');
			ob_start();
			$t->show();
			$name = ob_get_clean();
			// The double !! is because the hook above...
			$this->assertEqual(trim($name), 'Jaime Rodriguez');
		}

		// Test if \Sleepy\Template doesn't exist
		function testTemplateMissing() {
			$this->expectException(new Exception("Template './templates/missing.tpl' does not exist."));
			$t = new \Sleepy\Template();
			$t->directory = "./templates/";
			$t->setTemplate('missing');
			$t->show();
		}

		// Test in #include doesn't exist
		function testIncludeMissing() {
			$this->expectException(new Exception("Template './templates/binding.tpl' does not exist."));
			$t = new \Sleepy\Template();
			$t->directory = "./templates/";
			$t->setTemplate('binding');
			$t->show();
		}
	}