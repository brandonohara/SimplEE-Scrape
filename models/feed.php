<?php
	class SS_Feed {
		
		public $id;
		public $name;
		public $channel_id;
		public $url;
		public $location;
		public $type;
		public $update_type;
		
		public $unique_categories;
		public $category_variable;
		public $category_delimiter;
		
		public $context;
		public $fields = array();
		public $categories = array();
		
		function __construct($row){
			if($row){
				foreach($row as $key => $value){
					$this->$key = $value;
				}
				
				//attach all fields
				$query = ee()->db->where("feed_id", $this->id)->from("simplee_scrape_fields")->get();
				foreach($query->result_array() as $row){
					$field = new SS_Field($row);
					array_push($this->fields, $field);
				}
				
				//attach all categories
				$query = ee()->db->where("feed_id", $this->id)->from("simplee_scrape_categories")->get();
				foreach($query->result_array() as $row){
					$category = new SS_Category($row);
					array_push($this->categories, $category);
				}
				
				//explode data base
				$this->location = explode("/", $this->location);
				
				//set context
				$opts = array(
							'http'=>array(
						        'method'=>"GET",
						        'header'=>"Accept-language: en\r\n" .
						                  "Cookie: foo=bar\r\n" .
						                  "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.107 Safari/534.13\r\n"
						      )
				);
				$this->context = stream_context_create($opts);
			}
			
		}
		
		
		
		function update_entries($entries, $category_id = NULL){
			
			if($this->update_type == SIMPLEE_SCRAPE_UT_UPDATE){
				foreach($entries as $entry){
					$entry_id = $entry->save($this->channel_id);
				}
			} else if($this->update_type == SIMPLEE_SCRAPE_UT_CLOSE_OLD){
				$ids = array();
				foreach($entries as $entry){
					array_push($ids, $entry->save($this->channel_id));
				}
				
				if($category_id){
					$query = ee()->db->query("SELECT * FROM exp_channel_titles AS T, exp_category_posts AS C
						WHERE T.channel_id=".$this->channel_id."
						AND T.entry_id=C.entry_id
						AND C.cat_id=".$category_id);
				} else {
					$query = ee()->db->where("channel_id", $this->channel_id)->from("exp_channel_titles")->get();
				}
					
				$all_ids = array();
				foreach($query->result_array() as $row){
					array_push($all_ids, $row['entry_id']);
				}
				
				foreach($all_ids as $id){
					if(!in_array($id, $ids)){
						$entry = new SS_Entry($id);
						$entry->close_entry();
					}
				}
			}
		}
		
		function _remove_images($str){
			return preg_replace("/<img[^>]+\>/i", "", $str);
		}
	}
?>