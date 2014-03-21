<?php
function timer_render_placeholder() {
	return Performance::stop('template') . " Mem: " . memory_get_peak_usage(true);
}

Hook::applyFilter('render_placeholder_timer', 'timer_render_placeholder');