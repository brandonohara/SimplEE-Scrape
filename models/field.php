<?php
	class SS_Field {
		public $name;
		public $value;
		public $location;
		
		function __construct($row = NULL){
			if($row){
				foreach($row as $key => $value){
					$this->$key = $value;
				}
				$this->location = explode("/", $this->location);
			}
		}
	}
?>