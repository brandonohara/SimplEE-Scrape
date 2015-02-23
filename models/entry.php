<?php
	class SS_Entry {
		
		public $entry_id;
		
		public $data = array();
		public $categories = array();
		
		function __construct($entry_id = 0){
			$this->entry_id = $entry_id;
		}
		
		function get_entry_id($channel_id, $lookup_field = "title"){
			$entry_id = 0;
			$query = ee()->db->where("channel_id", $channel_id)->where($lookup_field, $this->data[$lookup_field])->from("channel_titles")->get();
			if($query->num_rows() == 1){
				$row = $query->row_array();
				$entry_id = $row['entry_id'];
			}
			return $entry_id;
		}
		
		function should_overwrite($channel_id, $entry_id){
			$overwrite = TRUE;
			
			if(!$entry_id){
				return $overwrite;
			}
			
			$query = ee()->db->where("channel_id", $channel_id)->from("simplee_scrape_feeds")->get();
			$row = $query->row_array();
			$overwrite_field = $row["overwrite_field"];
			
			$query = ee()->db->where("channel_titles.channel_id", $channel_id)
						->where("channel_titles.entry_id", $entry_id)
						->from("channel_titles")
						->join("channel_data", "channel_titles.entry_id = channel_data.entry_id")
						->get();
			$row = $query->row_array();
			$overwrite = isset($row[$overwrite_field]) && strtolower($row[$overwrite_field]) == "no" ? FALSE : TRUE;
			
			
			
			if(!$overwrite){
				echo "Should not overwrite entry_id: ".$entry_id."<br><br>";
			}
			
			return $overwrite;
		}
		
		function save($channel_id){
			ee()->load->library('api');
			ee()->api->instantiate('channel_entries');
			ee()->api->instantiate('channel_fields');
			
			$this->entry_id = $this->get_entry_id($channel_id, "title");
			$overwrite = $this->should_overwrite($channel_id, $this->entry_id);
			
			if($overwrite){
				ee()->api_channel_fields->setup_entry_settings($channel_id, $this->data);
				if (ee()->api_channel_entries->save_entry($this->data, $channel_id, $this->entry_id) === FALSE){
				    show_error('An Error Occurred Creating the Entry');
				}
				$this->entry_id = ee()->api_channel_entries->entry_id;
				
				foreach($this->categories as $category){
					$query = ee()->db->where('entry_id', $this->entry_id)->where('cat_id', $category)->from('exp_category_posts')->get();
					if($query->num_rows() == 0)
						ee()->db->insert("exp_category_posts", array('entry_id' => $this->entry_id, 'cat_id' => $category));
				}
			}
			
			return $this->entry_id;
		}
		
		function close_entry(){
			if(!$this->entry_id)
				return false;
			$data = array('status' => 'closed');
			ee()->db->where('entry_id', $this->entry_id)->update('exp_channel_titles', $data);
		}
	}
?>