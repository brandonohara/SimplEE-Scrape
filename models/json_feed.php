<?php
	class SS_JSON_Feed extends SS_Feed {
		
		function update(){
			if($this->unique_categories){   
				ee()->load->library('template',null,'TMPL');  
				
				$query = ee()->db->where('group_id', $this->unique_categories)->from('exp_categories')->get();
				foreach($query->result_array() as $row){
					$category_id = intval($row['cat_id']);
					$url = ee()->TMPL->parse_variables_row($this->url, $row);
					$entries = $this->pull_feed($url, $category_id);
					if($entries)
						$this->update_entries($entries, $category_id);
				}
			} else {
				$entries = $this->pull_feed();
				//$this->update_entries($entries);
			}
			
		}
		
		function pull_feed($url = NULL, $cat_id = NULL){
			if(!$url) $url = $this->url;
			$entries = array();
			$jsonstr = file_get_contents($url, false, $this->context);
			if($jsonstr && $jsonstr != ""){
				$data = json_decode($jsonstr);
				
				foreach($this->location as $section){
					$data = $data->$section;
				}
				
				echo "<br><br><br>New Feed: ".$url."<br><br>";
				
				foreach($data as $item){
					
					$entry = new SS_Entry();
					$attributes = array();
					foreach($this->fields as $field){
						$subitem = $item;
						foreach($field->location as $section){
							if($section == '')
								continue;
							$subitem = $subitem->$section;
						}
						$value = NULL;
						$key = $field->value;
						if($field->attribute){
							foreach($subitem->$key->attributes() as $a => $b) {
								if($field->attribute == $a)
									$value = $b;
							}
						} else {
							$value = $subitem->$key;
						}
						
						if($field->type == 1)
							$value = strtotime($value);
						else if($field->type == 2){
							$value = $this->_remove_images($value);
						}
						
						$entry->data[$field->name] = (string) trim($value);
					}
					
					if(!$cat_id){
						$category_variable = $this->category_variable;
						$entry_categories = array_map('trim', explode($this->category_delimiter, $item->$category_variable));
						foreach($this->categories as $category){
							$match = false;
							foreach($category->values as $category_value){
								if(in_array($category_value, $entry_categories))
									$match = true;
							}
							if($match)
								array_push($entry->categories, $category->category_id);
						}
					} else
						array_push($entry->categories, $cat_id);
						
					array_push($entries, $entry);
				}
			} else
				return false;
				
			return $entries;
		}
	}
?>