<?php
namespace Module\FormBuilder;

/**
 * Builds and valids forms
 *
 * This class allows for building forms using JSON. Fields are automatically
 * validated based on Rules allowing for easy server-side validation. The markup
 * closely resembles jQuery validation plugin so you can use one stylesheet for
 * both client- and server-side validation.
 *
 * ### Usage
 *
 * <code>
 *   $UserEdit = new \Module\FormBuilder\Form('{
 *     "id": "user",
 *     "action": "#",
 *     "method": "POST",
 *     "fieldsets": [
 *       {
 *         "legend": "Update your user information:",Fnew
 *         "fields": [
 *           {
 *             "name": "txtName",
 *             "label": "Name",
 *             "dataMap": "name",
 *             "type": "text",
 *             "value": "Jaime Rodriguez",
 *             "rules": {
 *               "required": true,
 *               "lengthMax": 20
 *             }
 *           }, {
 *             "name": "txtEmail",
 *             "label": "Email",
 *             "dataMap": "email",
 *             "type": "text",
 *             "value": "hi.i.am.jaime@gmail.com",
 *             "rules": {
 *               "required": true,
 *               "email": true
 *             }
 *           }, {
 *             "name": "txtDate",
 *             "label": "Date",
 *             "dataMap": "date",
 *             "type": "text",
 *             "value": "04/11/1984",
 *             "rules": {
 *               "required": true,
 *               "date": true
 *             }
 *           }, {
 *             "name": "ddlRole",
 *             "label": "Role",
 *             "dataMap": "role",
 *             "type": "select",
 *             "values": [
 *               {
 *                 "name":  "Administrator",
 *                 "value": "admin"
 *               }, {
 *                 "name":  "Subscriber",
 *                 "value": "subscriber"
 *               }, {
 *                 "name":  "User",
 *                 "value": "user",
 *                 "selected": true
 *               }
 *             ]
 *           }
 *         ]
 *       }, {
 *         "class": "submit",
 *         "fields": [
 *           {
 *             "name": "btnSubmit",
 *             "label": "",
 *             "value": "Submit",
 *             "type": "submit"
 *           }
 *         ]
 *       }
 *     ]
 *   }');
 *
 *   // Simulate a record Object
 *   $u = new stdClass();
 *   $u->columns = array();
 *
 *   // Has the form been submitted?
 *   if ($UserEdit->submitted()) {
 *     // Validate the form
 *     $passed = $UserEdit->validate();
 *
 *     if ($passed === true) {
 *       // put the values into the record Object
 *       $u->columns = array_merge($u->columns, $UserEdit->getDataMap());
 *     }
 *   }
 *
 *   // if Form::validate() was executed, it will render with errors and
 *   // updated values, otherwise it'll render normally
 *   echo $UserEdit->render();
 * </code>
 *
 * ### Changelog
 *
 * ## Version 1.6
 * * Added track attribute for google data tracking
 *
 * ## Version 1.5
 * * Throws an exception when you make a JSON error
 *
 * ## Version 1.4
 * * Added ability to overwrite errors
 * * Fixed equalTo rule validation bug
 *
 * ## Version 1.2
 * * Added placeholder for inputs
 * * Added namespacing
 * * Fixed error class bug
 *
 * ## Version 1.1
 * * Added the date and changelog sections to documentation
 *
 * @date September 3, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.6
 * @license  http://opensource.org/licenses/MIT
 */
class Form {
	/**
	 * The ID of the form
	 * @var string
	 */
	public $id;

	/**
	 * Class to apply to the field
	 * @var string
	 */
	public $class;

	/**
	 * The action of the form
	 * @var string
	 */
	public $action;

	/**
	 * The method of the form
	 * @var string
	 */
	public $method;

	/**
	 * Should the form be validated?
	 * @var boolean
	 */
	public $validate = true;

	/**
	 * An array of fieldsets
	 * @var array of FormBuilderFieldset
	 */
	private $fieldsets;

	/**
	 * Creates a Form based on JSON
	 * @param string $json
	 */
	public function __construct($json) {
		$data = \json_decode(str_replace('\\', '\\\\', $json));

		if (!is_object($data)) {
			var_dump($json);
			throw new \Exception('There is an error in your JSON. Cannot continue.');
		}

		if (!isset($data->action)) {
			$data->action = "#";
		}

		if (!isset($data->method)) {
			$data->method = "POST";
		}

		$this->action = $data->action;
		$this->method = $data->method;

		if (isset($data->id)) {
			$this->id = $data->id;
		}

		if (isset($data->class)) {
			$this->class = $data->class;
		}

		if (isset($data->validate)) {
			$this->validate = $data->validate;
		}

		foreach ($data->fieldsets as $fieldset) {
			$this->fieldsets[] = new FormBuilderFieldset($fieldset);
		}
	}

	/**
	 * Checks if the form has been submitted?
	 * @return boolean
	 */
	public function submitted() {
		if ($_SERVER['REQUEST_METHOD'] == $this->method) {
			if (strtoupper($this->method) === "POST") {
				$id = $_POST['frmID'];
			} else {
				$id = $_GET['frmID'];
			}

			if ($id == $this->id) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Validates the form
	 * @return array
	 */
	public function validate() {
		$errors = array();

		foreach($this->fieldsets as $fieldset) {
			$error = $fieldset->validate();
			if (is_array($error)) {
				$errors = array_merge($errors, $error);
			}
		}

		return (count($errors) == 0) ? true : $errors;
	}

	/**
	 * Renders a form
	 * @return string
	 */
	public function render() {
		$validate = (!$this->validate) ? "novalidate" : "";
		$buffer = array();
		$buffer[] = "<form id=\"{$this->id}\" class=\"{$this->class}\" action=\"{$this->action}\" method=\"{$this->method}\" {$validate}>";
		$buffer[] = "<input type=\"hidden\" name=\"frmID\" id=\"frmID\" value=\"{$this->id}\">";

		foreach($this->fieldsets as $fieldset) {
			$buffer[] = $fieldset->render($this->validate && $this->submitted());
		}

		$buffer[] = "</form>";

		return implode(" ", $buffer);
	}

	/**
	 * Get the datamap for the form
	 * @return array
	 */
	public function getDataMap() {
		$formData = array();

		foreach ($this->fieldsets as $fieldset) {
			if (is_array($fieldset->getDataMap())) {
				$formData = array_merge($formData, $fieldset->getDataMap());
			}
		}

		return $formData;
	}
}

/**
 * Creates a Field
 * @internal
 */
class FormBuilderField {
	// Manditory properties

	/**
	 * The name of the field
	 * @var string
	 */
	public $name;

	/**
	 * The label for the field
	 * @var string
	 */
	public $label;

	/**
	 * The type of input for the field
	 * @var string
	 */
	public $type;

	// Optional properties

	/**
	 * The rules for validation
	 * @var object
	 */
	public $rules;

	/**
	 * The value of the field
	 * @var array
	 */
	public $values = array();

	/**
	 * The placeholder for the field
	 * @var string
	 */
	public $placeholder;

	/**
	 * The google event tracking for the field
	 * @var string
	 */
	public $track;

	/**
	 * Should we autofocus on this field?
	 * @var boolean
	 */
	public $autofocus;

	/**
	 * The data mapping for this field
	 * @var string
	 */
	public $dataMap;

	/**
	 * Class to apply to the field
	 * @var string
	 */
	public $class;

	/**
	 * Is this field disabled?
	 * @var boolean
	 */
	public $disabled = false;

	/**
	 * Constructor for the field
	 * @param object $object
	 */
	public function __construct($object) {
		if (!isset($object->type)) {
			$object->type = "text";
		}

		if (!isset($object->name)) {
			throw new \Exception('FormBuilderField: Name is manditory.');
		}

		$this->name = $object->name;
		$this->track = @$object->track;
		$this->label = @$object->label;
		$this->type = $object->type;

		if (isset($object->label)) {
			$this->label = $object->label;
		}

		if (isset($object->dataMap)) {
			$this->dataMap = $object->dataMap;
		}

		if (isset($object->class)) {
			$this->class = $object->class;
		}

		if (isset($object->disabled)) {
			$this->disabled = $object->disabled;
		}

		if (isset($object->placeholder)) {
			$this->placeholder = $object->placeholder;
		}

		if (isset($object->autofocus)) {
			$this->autofocus = $object->autofocus;
		}

		if (isset($object->rules)) {
			$this->rules = $object->rules;
		}

		if (isset($object->errors)) {
			$this->errors = $object->errors;
		}

		// These two have to be redone
		if (isset($object->values)) {
			$this->values = $object->values;
		} elseif (isset($object->value)) {
			$this->values[] = $object->value;
		}
	}

	/**
	 * Validates a date
	 * @param  string $date
	 * @param  string $format
	 * @return boolean
	 * @private
	 */
	private function validateDate($date, $format = 'Y-m-d H:i:s') {
		$d = \DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}

	/**
	 * Renders the field
	 * @return string
	 */
	public function render($validate) {
		// Get any errors
		$errors = ($validate) ? $this->validate() : "";

		$disabled = ($this->disabled) ? "disabled " : "";
		$track = (@isset($this->track)) ? "data-track=\"{$this->track}\" " : "";
		$autofocus = ($this->autofocus) ? "autofocus " : "";
		$placeholder = ($this->placeholder) ? "placeholder='{$this->placeholder}' " : "";

		// Setup rules for client-side processing
		$required =  (@$this->rules->required) ? "required " : "";
		$equalTo =   (@isset($this->rules->equalTo)) ? "equalTo='#{$this->rules->equalTo}' " : "";
		$minLength = (@isset($this->rules->minLength)) ? "minlength='{$this->rules->minLength}'' " : "";
		$maxLength = (@isset($this->rules->maxLength)) ? "maxlength='{$this->rules->maxLength}'' " : "";
		$digits =    (@$this->rules->digits) ? "digits " : "";
		$email =     (@$this->rules->email) ? "email " : "";
		$date =      (@$this->rules->date) ? "date " : "";

		// Add all the rules to one string for brevity
		$rules = "{$track}{$required}{$minLength}{$maxLength}{$equalTo}{$disabled}{$autofocus}{$placeholder}{$digits}{$email}{$date}";

		if (is_array($errors)) {
			$this->class = $this->class . " error";
		}

		if (count($this->values) == 0) {
			$this->values[0]= "";
		}

		$buffer = array();

		switch ($this->type) {
		case 'copy':
			if (isset($this->label)) {
				$buffer[] = "<label for=\"{$this->name}\">{$this->label}</label>";
			}

			$buffer[] = $this->values[0];
			break;
		case 'textbox':
			if (isset($this->label)) {
				$buffer[] = "<label for=\"{$this->name}\">{$this->label}</label>";
			}

			$buffer[] = "<textbox {$rules} id=\"{$this->name}\" name=\"{$this->name}\" class=\"{$this->class}\">";
			$buffer[] = "{$this->values[0]}";
			$buffer[] = "</textbox>";
			break;
		case 'select':
			if (isset($this->label)) {
				$buffer[] = "<label for=\"{$this->name}\">{$this->label}</label>";
			}

			$buffer[] = "<select {$rules} id=\"{$this->name}\" name=\"{$this->name}\" class=\"{$this->class}\">";

			foreach($this->values as $option) {
				$disabledField = "";
				$selected = "";

				if (isset($option->disabled)) {
					$disabledField = ($option->disabled) ? "disabled" : "";
				}

				if (isset($option->selected)) {
					$selected = ($option->selected) ? "selected" : "";
				}

				$buffer[] = "<option {$disabledField} {$selected} value=\"{$option->value}\">{$option->name}</option>";
			}

			$buffer[] = "</select>";
			break;
		case 'radio':
			if (isset($this->label)) {
				$buffer[] = "<label for=\"{$this->name}\">{$this->label}</label>";
			}

			$buffer[] = "<ul class=\"radios {$this->class}\">";

			foreach($this->values as $option) {
				$track = (@isset($option->track)) ? "data-track=\"{$option->track}\" " : "";
				$disabledField = "";
				$selected = "";

				if (isset($option->disabled)) {
					$disabledField = ($option->disabled) ? "disabled " : "";
				}

				if (isset($option->selected)) {
					$selected = ($option->selected) ? "checked " : "";
				}

				$buffer[] = "<li>";
				$buffer[] = "<input {$track} {$rules} {$selected} type=\"radio\" id=\"{$option->id}\" name=\"{$this->name}\" class=\"{$this->class}\" value=\"{$option->value}\">";

				if (isset($option->label)) {
					$buffer[] = "<label for='{$option->id}'>{$option->label}</label>";
				}

				$buffer[] = "</li>";
			}
			$buffer[] = "</ul>";

			break;
		case 'checkbox':
			$selected = "";

			if (isset($this->selected)) {
				$selected = ($this->selected) ? "checked " : "";
			}

			$buffer[] = "<input {$rules} {$selected} type=\"{$this->type}\" id=\"{$this->name}\" name=\"{$this->name}\" class=\"{$this->class}\" value=\"{$this->values[0]}\">";

			if (isset($this->label)) {
				$buffer[] = "<label for=\"{$this->name}\">{$this->label}</label>";
			}
			break;
		default:
			if (isset($this->label)) {
				$buffer[] = "<label for=\"{$this->name}\">{$this->label}</label>";
			}
			$buffer[] = "<input {$rules} type=\"{$this->type}\" id=\"{$this->name}\" name=\"{$this->name}\" class=\"{$this->class}\" value=\"{$this->values[0]}\">";
		}

		if (is_array($errors)) {
			foreach($errors as $error) {
				$buffer[] = "<label class=\"error\" for=\"{$this->name}\">{$error}</label>";
			}
		}

		return implode(" ", $buffer);
	}

	/**
	 * Validates the field
	 * @return array Errors
	 */
	public function validate() {
		$errors = array();

		// assign the new values
		switch ($this->type) {
			case 'submit':
				break;
			case 'select':
			case 'radio':
				if (!empty($_POST[$this->name])) {
					foreach($this->values as $key => $object) {
						$this->values[$key]->selected = ($_POST[$this->name] == $object->value) ? true : false;
					}
				}
				break;
			case 'checkbox':
				if (!empty($_POST[$this->name])) {
					$this->selected = ($_POST[$this->name] == $this->values[0]) ? true : false;
				}
				break;
			default:
				if (!empty($_POST[$this->name])) {
					$this->values[0] = $_POST[$this->name];
				} else {
					unset($this->values[0]);
				}
		}

		if (is_object($this->rules)) {
			foreach ($this->rules as $rule => $value) {
				switch($rule) {
				case 'required':
					if ($value != false) {
						if (count($this->values) == 0) {
							if (isset($this->errors->$rule)) {
								$errors[] = $this->errors->$rule;
							} else {
								$errors[] = "'{$this->label}' is a required field.";
							}
						}
					}
					break;
				case 'lengthMax':
					if ($value != false) {
						if (isset($this->values[0])) {
							if (strlen($this->values[0]) >= $value) {
								if (isset($this->errors->$rule)) {
									$errors[] = $this->errors->$rule;
								} else {
									$errors[] = "'{$this->label}' should be a maximum of {$value} characters.";
								}
							}
						}
					}
					break;
				case 'lengthMin':
					if ($value != false) {
						if (strlen($this->values[0]) <= $value) {
							if (isset($this->errors->$rule)) {
								$errors[] = $this->errors->$rule;
							} else {
								$errors[] = "'{$this->label}' should be a minimum of {$value} characters.";
							}
						}
					}
					break;
				case 'digits':
					if ($value != false) {
						if (!filter_var($this->values[0], FILTER_VALIDATE_FLOAT)) {
							if (isset($this->errors->$rule)) {
								$errors[] = $this->errors->$rule;
							} else {
								$errors[] = "'{$this->label}' is not a valid number.";
							}
						}
					}
					break;
				case 'email':
					if ($value != false) {
						if (count($this->values) == 0) {
							$this->values[0] = NULL;
						} else {
							if (!filter_var($this->values[0], FILTER_VALIDATE_EMAIL)) {
								if (isset($this->errors->$rule)) {
									$errors[] = $this->errors->$rule;
								} else {
									$errors[] = "'{$this->label}' is not a valid email address.";
								}
							}
						}
					}
					break;
				case 'date':
					if (count($this->values) == 0) {
						$this->values[0] = NULL;
					}

					if (!$this->validateDate($this->values[0], 'm/d/Y')) {
						if (isset($this->errors->$rule)) {
								$errors[] = $this->errors->$rule;
						} else {
							$errors[] = "'{$this->label}' is not a valid date (mm/dd/yyyy).";
						}
					}
					break;
				case 'equal':
				case 'equalTo':
					if ($value != false) {
						if (count($this->values) == 0) {
							$this->values[0] = NULL;
						}

						if ($this->values[0] != $_POST[$value]) {
							if (isset($this->errors->$rule)) {
								$errors[] = $this->errors->$rule;
							} else {
								$errors[] = "'{$this->label}' does not match '{$value}'.";
							}
						}
					}
					break;
				}
			}
		}

		return $errors;
	}

	/**
	 * Returns an array with mapping => value
	 * @return array
	 */
	public function getDataMap() {
		if (isset($this->dataMap)) {
			switch($this->type) {
			case 'select':
				foreach ($this->values as $option) {
					if ($option->selected) {
						return array(
							$this->dataMap => $option->value
						);
					}
				}
				break;
			default:
				if (count($this->values) == 0) {
					$this->values[0] = NULL;
				}

				return array(
					$this->dataMap => $this->values[0]
				);
			}
		}
	}
}

/**
 * Creates a Fieldset
 * @internal
 */
class FormBuilderFieldset {
	/**
	 * Class to apply to the field
	 * @var string
	 */
	public $class;

	/**
	 * The legend of the fieldset
	 * @var string
	 */
	public $legend;

	/**
	 * an array of FormBuilderField
	 * @var [type]
	 */
	private $fields;

	/**
	 * Constructor for the fieldset
	 * @param object $object
	 */
	public function __construct($object) {
		if (isset($object->class)) {
			$this->class = $object->class;
		}

		if (isset($object->legend)) {
			$this->legend = $object->legend;
		}


		foreach ($object->fields as $field) {
			$this->fields[] = new FormBuilderField($field);
		}
	}

	/**
	 * Renders a complete fieldset with fields
	 * @param  boolean $validate
	 * @return string
	 */
	public function render($validate) {
		$buffer = array();
		$buffer[] = "<fieldset class=\"{$this->class}\">";

		if (isset($this->legend)) {
			$buffer[] = "<legend>{$this->legend}</legend>";
		}

		$buffer[] = "<ul>";

		foreach($this->fields as $field) {
			$buffer[] = "<li>" . $field->render($validate) . "</li>";
		}

		$buffer[] = "</ul>";
		$buffer[] = "</fieldset>";

		return implode(" ", $buffer);
	}

	/**
	 * Validates all the fields in a fieldset
	 * @return array
	 */
	public function validate() {
		$errors = array();

		foreach($this->fields as $field) {
			$error = $field->validate();
			if (is_array($error)) {
				$errors = array_merge($errors, $error);
			}
		}

		return $errors;
	}

	/**
	 * Gets the datamap for all fields in a fieldset
	 * @return [type]
	 */
	public function getDataMap() {
		$fieldsetData = array();

		foreach ($this->fields as $field) {
			if (is_array($field->getDataMap())) {
				$fieldsetData = array_merge($fieldsetData, $field->getDataMap());
			}
		}

		return $fieldsetData;
	}
}

/**
 * Gets a list of states in JSON format to be used in select fields
 */
class States {
	function jsonArray($placeholder="State") {
		return '[
			{
				"name": "' . $placeholder . '",
				"disabled": true,
				"selected": true,
				"value": ""
			}, {
				"name" : "AL",
				"value": "Alabama"
			}, {
				"name" : "AK",
				"value": "Alaska"
			}, {
				"name" : "AZ",
				"value": "Arizona"
			}, {
				"name" : "AR",
				"value": "Arkansas"
			}, {
				"name" : "CA",
				"value": "California"
			}, {
				"name" : "CO",
				"value": "Colorado"
			}, {
				"name" : "CT",
				"value": "Connecticut"
			}, {
				"name" : "DE",
				"value": "Delaware"
			}, {
				"name" : "DC",
				"value": "District Of Columbia"
			}, {
				"name" : "FL",
				"value": "Florida"
			}, {
				"name" : "GA",
				"value": "Georgia"
			}, {
				"name" : "HI",
				"value": "Hawaii"
			}, {
				"name" : "ID",
				"value": "Idaho"
			}, {
				"name" : "IL",
				"value": "Illinois"
			}, {
				"name" : "IN",
				"value": "Indiana"
			}, {
				"name" : "IA",
				"value": "Iowa"
			}, {
				"name" : "KS",
				"value": "Kansas"
			}, {
				"name" : "KY",
				"value": "Kentucky"
			}, {
				"name" : "LA",
				"value": "Louisiana"
			}, {
				"name" : "ME",
				"value": "Maine"
			}, {
				"name" : "MD",
				"value": "Maryland"
			}, {
				"name" : "MA",
				"value": "Massachusetts"
			}, {
				"name" : "MI",
				"value": "Michigan"
			}, {
				"name" : "MN",
				"value": "Minnesota"
			}, {
				"name" : "MS",
				"value": "Mississippi"
			}, {
				"name" : "MO",
				"value": "Missouri"
			}, {
				"name" : "MT",
				"value": "Montana"
			}, {
				"name" : "NE",
				"value": "Nebraska"
			}, {
				"name" : "NV",
				"value": "Nevada"
			}, {
				"name" : "NH",
				"value": "New Hampshire"
			}, {
				"name" : "NJ",
				"value": "New Jersey"
			}, {
				"name" : "NM",
				"value": "New Mexico"
			}, {
				"name" : "NY",
				"value": "New York"
			}, {
				"name" : "NC",
				"value": "North Carolina"
			}, {
				"name" : "ND",
				"value": "North Dakota"
			}, {
				"name" : "OH",
				"value": "Ohio"
			}, {
				"name" : "OK",
				"value": "Oklahoma"
			}, {
				"name" : "OR",
				"value": "Oregon"
			}, {
				"name" : "PA",
				"value": "Pennsylvania"
			}, {
				"name" : "RI",
				"value": "Rhode Island"
			}, {
				"name" : "SC",
				"value": "South Carolina"
			}, {
				"name" : "SD",
				"value": "South Dakota"
			}, {
				"name" : "TN",
				"value": "Tennessee"
			}, {
				"name" : "TX",
				"value": "Texas"
			}, {
				"name" : "UT",
				"value": "Utah"
			}, {
				"name" : "VT",
				"value": "Vermont"
			}, {
				"name" : "VA",
				"value": "Virginia"
			}, {
				"name" : "WA",
				"value": "Washington"
			}, {
				"name" : "WV",
				"value": "West Virginia"
			}, {
				"name" : "WI",
				"value": "Wisconsin"
			}, {
				"name" : "WY",
				"value": "Wyomin"
			}
		]';
	}
}