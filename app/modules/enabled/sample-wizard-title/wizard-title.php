<?php
namespace Module\SampleWizardTitle;

/**
 * Sample module adds wizard to the title
 * @param  string $title The page title
 * @return string        The modified string
 * @internal
 */
function change($title) {
	return '<|:{) - ' . $title;
}

\Sleepy\Hook::applyFilter('render_placeholder_title', '\Module\SampleWizardTitle\change');