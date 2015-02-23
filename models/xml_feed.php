<?php
	class SS_XML_Feed extends SS_Feed {
		
		function update(){
			if($this->unique_categories){   
				ee()->load->library('template',null,'TMPL');  
				
				$query = ee()->db->where('group_id', $this->unique_categories)->from('exp_categories')->get();
				foreach($query->result_array() as $row){
					$category_id = intval($row['cat_id']);
					$url = ee()->TMPL->parse_variables_row($this->url, $row);
					$entries = $this->pull_feed($url, $category_id);
					$this->update_entries($entries, $category_id);
				}
			} else {
				$entries = $this->pull_feed();
				$this->update_entries($entries);
			}
		}
		
		function pull_feed($url = NULL, $cat_id = NULL){
			if(!$url)$url = $this->url;
			$entries = array();
			$xmlstr = file_get_contents($url, false, $this->context);
			$xmlstr = mb_convert_encoding($xmlstr, 'HTML-ENTITIES', "UTF-8");
			$xmlstr = str_replace("&rsquo;", "'", $xmlstr);
			if($xmlstr){
				$xml = $data =  new SimpleXMLElement($xmlstr);
				
				$count = 0;
				foreach($this->location as $section){
					//disregard first since that's where we start
					if($count != 0 && $count != count($this->location))
						$data = $data->$section;	
					$count++;
				}
				
				foreach($data as $item){
					$entry = new SS_Entry();
					$attributes = array();
					foreach($this->fields as $field){
					
						//get to location
						$value = NULL;
						$current = $item;
						if(count($field->location) > 0 && $field->location[0] != ""){
							foreach($field->location as $segment){
								$current = $current->$segment;
							}
						}
						//obtain item
						if(($key = $field->value) != ""){
							$current = $current->$key;
						}
						//attribute
						if($field->attribute && $current){
							foreach($current->attributes() as $a => $b) {
								if($field->attribute == $a)
									$value = $b;
							}
						} else {
							$value = $current;
						}
					/*
						$value = NULL;
						if($field->attribute){
							foreach($item->$key->attributes() as $a => $b) {
								if($field->attribute == $a)
									$value = $b;
							}
						} else if(count($field->location) > 0 && $field->location[0] != ""){
							$holder = NULL;
							foreach($field->location as $segment){
								$holder = $item->$segment;
							}
							$value = $holder->$key;
						} else {
							$value = $item->$key;
						}
						*/
						
						if($field->type == 1)
							$value = strtotime($value);
						else if($field->type == 2){
							$value = $this->_remove_images($value);
						}
						
						if($field->name == "title")
							$value = $this->title_prefix.$value;
						
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
			}
			return $entries;
		}
	}
?>