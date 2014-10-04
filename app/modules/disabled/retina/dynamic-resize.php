<?php
namespace Module\Mobile;

/**
 * Resize all images ending with @2x
 * @return void
 * @internal
 */
function resize() {
	$r = new Retina();
	$r->processDownsize([dirname(__file__) . '/../../../../images/']);
}

\Sleepy\Hook::doAction('sleepy_preprocess', '\Module\Mobile\resize');