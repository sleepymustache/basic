<?php
	function timer_render_placeholder() {
		return Performance::stop('template');
	}

Hook::applyFilter('render_placeholder_timer', 'timer_render_placeholder');