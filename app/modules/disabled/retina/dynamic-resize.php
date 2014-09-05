<?php
namespace Mobile;

function resize() {
	$r = new Retina();
	$r->processDownsize([dirname(__file__) . '/../../../../images/']);
}

\Sleepy\Hook::doAction('sleepy_preprocess', '\Mobile\resize');