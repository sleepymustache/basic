<?php
	require_once('class.form-builder.php');
	
	$UserEdit = new FormBuilder('{
		"id": "user",
		"action": "#",
		"method": "POST",
		"fieldsets": [
			{
				"legend": "Update your user information:",
				"fields": [
					{
						"name": "txtName",
						"label": "Name:",
						"dataMap": "name",
						"type": "text",
						"value": "Jaime Rodriguez",
						"rules": {
							"required": true,
							"lengthMax": 100
						}
					}, {
						"name": "btnSubmit",
						"label": "",
						"value": "Submit",
						"type": "submit"
					}
				]
			}
		]
	}');

	// Has the form been submitted?
	if ($UserEdit->submitted()) {
		// Validate and store the data to the bound variable
		if ($UserEdit->validate() == true) {
			$u = new stdClass();
			$u->columns = array();
			
			foreach($UserEdit->getDataMap() as $key => $value) {
				$u->columns[$key] = $value;
			}
		}
	}

	// if Form::validate() was executed, it will render with errors and updated values, otherwise it'll render normally
	$UserEdit->render();