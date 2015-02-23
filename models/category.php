<?php
	class SS_Category {
		public $category_id;
		public $alternate_names;
		public $location;
		
		public $values = array();
		
		function __construct($row = NULL){
			if($row){
				foreach($row as $key => $value){
					$this->$key = $value;
				}
				
				//set up categories array
				$this->values = array_map("trim", explode(",", $this->alternate_names));
				$query = ee()->db->where("cat_id", $this->category_id)->from("exp_categories")->get();
				$row = $query->row_array();
				array_push($this->values, $row['cat_name']);
			}
		}
	}
?>