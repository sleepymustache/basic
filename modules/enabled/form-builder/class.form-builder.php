<?php
	class FormBuilderField {
		// Manditory properties
		public $name;
		public $label;
		public $type;

		// Optional properties
		public $rules;
		public $values;
		public $placeholder;
		public $autofocus;
		public $dataMap;
		public $class;
		public $disabled = false;

		public function __construct($object) {
			if (!isset($object->name)) {
				throw new Exception('FormBuilderField: Name is manditory.');
			}

			if (!isset($object->label)) {
				throw new Exception('FormBuilderField: Label is manditory.');
			}

			if (!isset($object->type)) {
				throw new Exception('FormBuilderField: Type is manditory.');
			}

			$this->name = $object->name;
			$this->label = $object->label;
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

			// These two have to be redone
			if (isset($object->values)) {
				$this->values = $object->values;
			} elseif (isset($object->value)) {
				$this->values[] = $object->value;
			}
		}

		public function render() {
			$disabled = ($this->disabled) ? "disabled" : "";
			$autofocus = ($this->autofocus) ? "autofocus" : "";

			$buffer = array();
			$buffer[] = "<label for=\"{$this->name}\">{$this->label}</label>";
			switch ($this->type) {
			case 'textbox':
				$buffer[] = "<textbox {$disabled} {$autofocus} id=\"{$this->name}\" name=\"{$this->name}\" class=\"{$this->class}\">";
				$buffer[] = "{$this->value[0]}";
				$buffer[] = "</textbox>";
				break;
			case 'select':
				$buffer[] = "<select {$disabled} {$autofocus} id=\"{$this->name}\" name=\"{$this->name}\" class=\"{$this->class}\">";
				foreach($this->values as $value) {
					$disabledField = ($value->disabled) ? "disabled" : "";
					$selected = ($this->selected) ? "selected" : "";
					$buffer[] = "<option {$selected} value=\"{$this->value}\">{$this->text}</option>";	
				}
				$buffer[] = "</select>";
				break;
			default:
				$buffer[] = "<input type=\"{$this->type}\" {$disabled} {$autofocus} id=\"{$this->name}\" name=\"{$this->name}\" class=\"{$this->class}\" value=\"{$this->values[0]}\">";
			}

			return implode(" ", $buffer);
		}

		public function validate() {
			switch ($this->type) {
				case 'submit':
					break;
				default:
					$this->values[0] = $_POST[$this->name];
			}
		}

		public function getDataMap() {
			if (isset($this->dataMap)) {
				return array(
					$this->dataMap => $this->values[0]
				);
			}
		}
	}

	class FormBuilderFieldset {
		public $class;
		public $legend;

		private $fields;

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

		public function render() {
			$buffer = array();
			$buffer[] = "<fieldset class=\"{$this->class}\">";
			$buffer[] = "<legend>{$this->legend}</legend>";
			$buffer[] = "<ul>";

			foreach($this->fields as $field) {
				$buffer[] = "<li>" . $field->render() . "</li>";
			}

			$buffer[] = "</ul>";
			$buffer[] = "</fieldset>";

			return implode(" ", $buffer);
		}

		public function validate() {
			$errors = array();
			foreach($this->fields as $field) {
				$error = $field->validate();
				if ($error != true) {
					$errors[] = $error;
				}
			}

			if (count($errors) == 0) {
				return true;
			} else {
				return $errors;
			}
		}

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

	class FormBuilder {
		public $id;
		public $class;
		public $action;
		public $method;
		public $validate;

		private $fieldsets;

		public function __construct($json) {
			$data = json_decode($json);

			if (!isset($data->action)) {
				throw new Exception('FormBuilder: Action is manditory.');
			}

			if (!isset($data->method)) {
				throw new Exception('FormBuilder: Method is manditory.');
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

		// returns true is submitted
		public function submitted() {
			if ($_SERVER['REQUEST_METHOD'] == $this->method) {
				return true;
    		} else {
    			return false;
    		}
		} 

		// returns true if valid, else array of errors. Updates data if there is a binding
		public function validate() {
			$errors = array();
			foreach($this->fieldsets as $fieldset) {
				$error = $fieldset->validate();
				if ($error != true) {
					$errors[] = $error;
				}
			}

			if (count($errors) == 0) {
				return true;
			} else {
				return $errors;
			}
		}

		// returns a string to echo, adds data if there is a binding
		public function render() {
			$validate = (!$this->validate) ? "novalidate" : "";
			$buffer = array();
			$buffer[] = "<form class=\"{$this->class}\" action=\"{$this->action}\" method=\"{$this->method}\" {$validate}>";
			
			foreach($this->fieldsets as $fieldset) {
				$buffer[] = $fieldset->render();
			}

			$buffer[] = "</form>";
			echo implode(" ", $buffer);
		}

		// returns a field object
		public function getField($name)   {

		}

		// adds a field to the form to $idx, or the end if blank
		public function addField($json, $idx="") {

		}

		// updates a field with the new JSON
		public function updateField($name, $json) {

		}

		public function getDataMap() {
			$formData = array();

			foreach ($this->fieldsets as $fieldset) {
				if (is_array($fieldset->getDataMap())) {
					$formData = array_merge($formData, $fieldset->getDataMap());
				}
			}
			return $formData;
		}

		public function getValues() {

		}

		public function getValue($name) {

		}
	}