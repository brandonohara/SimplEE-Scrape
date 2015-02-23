<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * scrapEE Module Control Panel
	 *
	 * @package   scrapEE
	 * @author    Brandon O'Hara <brandon@brandonohara.com>
	 * @copyright Copyright (c) 2014 Brandon O'Hara
	 */
	 
	require_once(PATH_THIRD."simplee_scrape/config.php");
	
	class Simplee_scrape_mcp {
	
		public $feed_fields = array();
		
		public $default_fields = array(
			'title' => 'Title', 
			'url_title' => 'URL Title', 
			'entry_date' => 'Entry Date', 
			'edit_date' => 'Edit Date'
		);
		
		public $feed_types = array(
			0 => "XML",
			1 => "JSON"
		);
		
		public $update_types = array(
			0 => "Insert New",
			1 => "Insert New, Close Old"
		);
		
		public $field_types = array(
			0 => 'None', 
			1 => 'Date', 
			2 => 'Remove Images', 
			3 => 'Image URL Only'
		);
	
		function __construct(){
			$this->base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=simplee_scrape'.AMP;
		    ee()->cp->set_right_nav(array(
		        'feed_form_button'	=> $this->base_url.'method=feed_form'
		    ));
		    
		    //get channels
		    $channels = array();
		    $query = ee()->db->from('exp_channels')->get();
		    foreach($query->result_array() as $row){
			    $channels[$row['channel_id']] = $row['channel_title'];
		    }
		    
		    $this->feed_fields = array(
				'id' => array(
					'label' => lang('simplee_scrape_feed_id'),
					'type' => 0,
					'data' => '0',
					'options' => array(),
					'js' => ''
				),
				'name' => array(
					'label' => lang('simplee_scrape_feed_name'),
					'type' => 0,
					'data' => '',
					'options' => array(),
					'js' => ''
				),
				'channel_id' => array(
					'label' => lang('simplee_scrape_feed_channel_id'),
					'type' => 1,
					'data' => '',
					'options' => $channels,
					'js' => 'onchange="simpleeScrapeShowChannelFields();"'
				),
				'url' => array(
					'label' => lang('simplee_scrape_feed_url'),
					'type' => 0,
					'data' => '',
					'options' => array(),
					'js' => ''
				),
				'type' => array(
					'label' => lang('simplee_scrape_feed_type'),
					'type' => 1,
					'data' => '0',
					'options' => $this->feed_types,
					'js' => ''
				),
				'update_type' => array(
					'label' => lang('simplee_scrape_feed_update_type'),
					'type' => 1,
					'data' => '0',
					'options' => $this->update_types,
					'js' => ''
				),
				'location' => array(
					'label' => lang('simplee_scrape_feed_location'),
					'type' => 0,
					'data' => '',
					'options' => array(),
					'js' => ''
				),
				'unique_categories' => array(
					'label' => lang('simplee_scrape_feed_unique_categories'),
					'type' => 1,
					'data' => 'No',
					'options' => array("No", "Yes"),
					'js' => 'onchange="simpleeScrapeToggleCategories(this.value)"'
				),
				'category_variable' => array(
					'label' => lang('simplee_scrape_feed_category_variable'),
					'type' => 0,
					'data' => '',
					'options' => array("No", "Yes"),
					'js' => ''
				),
				'category_delimiter' => array(
					'label' => lang('simplee_scrape_feed_category_delimiter'),
					'type' => 0,
					'data' => ',',
					'options' => array(),
					'js' => ''
				),
			);
		}
		
		function index(){
			ee()->load->library('javascript');
		    ee()->load->library('table');
		    ee()->load->helper('form');
		
		    $vars['form_hidden'] = NULL;
		    $vars['feeds'] = array();
			
			$query = ee()->db->from('simplee_scrape_feeds')->get();
			foreach($query->result_array() as $row){
				$feed = new SS_Feed($row);
			    $feed->edit_link = $this->base_url.'method=feed_form'.AMP.'feed_id='.$feed->id;
			    $feed->delete_link = $this->base_url.'method=feed_delete'.AMP.'feed_id='.$feed->id;
			    array_push($vars['feeds'], $feed);
			}
		
		    ee()->view->cp_page_title = lang('simplee_scrape_module_name');
		    return ee()->load->view('view_feeds', $vars, TRUE);
		}
		
		function feed_form(){
			$feed_id = (isset($_GET['feed_id']) ? intval($_GET['feed_id']) : NULL);
		
		    ee()->load->library('table');
		    ee()->load->library('form_validation');
		    ee()->load->helper('form');
		
		    // Validate form
		    ee()->form_validation->set_rules('name', lang('rets_class_name'), 'required');
		    if (ee()->form_validation->run())
		        return $this->feed_form_submit();
		    
		    $query = ee()->db->from('exp_channels')->get();
		    $channels = $query->result_array();
		    
		    //set field types
		    $vars['feed_fields'] = $this->feed_fields;
		    $vars['field_types'] = $this->field_types;
		    if($feed_id){
		    	$query = ee()->db->where('id', $feed_id)->from('simplee_scrape_feeds')->get();
				$row = $query->row_array();
			    foreach($vars['feed_fields'] as $key => $field){
				    $vars['feed_fields'][$key]['data'] = $row[$key];
			    }
			}
		    
		    //get channel_fields
		    $vars['fields'] = array();
		    $default_data = array('value' => '', 'type' => '', 'attribute' => '', 'location' => '');
		    foreach($this->default_fields as $id => $title){
		    	$field = array();
			    $field['id'] = $id;
			    $field['label'] = $title;
			    $field['class'] = " default_field";
			    array_push($vars['fields'], $field);
		    }
		    $query = ee()->db->from("exp_channel_fields")->get();
		    foreach($query->result_array() as $field){
		    	$field['id'] = "field_id_".$field['field_id'];
		    	$field['label'] = $field['field_label'];
			    $field['channels'] = array();
			    foreach($channels as $channel){
				    if($channel['field_group'] == $field['group_id'])
				    	array_push($field['channels'], "channel".$channel['channel_id']);
			    }
			    $field['class'] = " channel_field ".implode(" ", $field['channels']);
				
			    array_push($vars['fields'], $field);
		    }
		    for($i = 0; $i < count($vars['fields']); $i++){
			    if($feed_id){
				    $query = ee()->db->where('feed_id', $feed_id)->where('name', $vars['fields'][$i]['id'])->from('simplee_scrape_fields')->get();
				    $vars['fields'][$i]['data'] = $query->num_rows() == 1 ? $query->row_array() : $default_data;
			    } else
			    	$vars['fields'][$i]['data'] = $default_data;
		    }
		    
		    
		    //get channel categories
		    $vars['categories'] = array();
		    $default_data = array('alternate_names' => '');
		    $query = ee()->db->from("exp_categories")->order_by('cat_order', 'asc')->get();
		    foreach($query->result_array() as $category){
			    $category['channels'] = array();
			    foreach($channels as $channel){
				    if($channel['cat_group'] == $category['group_id'])
				    	array_push($category['channels'], "channel".$channel['channel_id']);
			    }
			    $category['class'] = " channel_category ".implode(" ", $category['channels']);
			    if($feed_id){
				    $query = ee()->db->where('feed_id', $feed_id)->where('category_id', $category['cat_id'])->from('simplee_scrape_categories')->get();
				    $category['data'] = $query->num_rows() == 1 ? $query->row_array() : $default_data;
			    } else
			    	$category['data'] = $default_data;
				
			    array_push($vars['categories'], $category);
		    }
		
		
		    $vars['action_url'] = $this->base_url.'method=feed_form';
		    $vars['form_hidden'] = NULL;
		    
			return ee()->load->view('feed_form', $vars, TRUE);
		}
		
		private function feed_form_submit(){

			$id = ee()->input->post('id');
			
			$feed = array();
			$feed['name'] = ee()->input->post('name');
			$feed['channel_id'] = ee()->input->post('channel_id');
			$feed['url'] = ee()->input->post('url');
			$feed['type'] = ee()->input->post('type');
			$feed['location'] = ee()->input->post('location');
			$feed['update_type'] = ee()->input->post('update_type');
			$feed['unique_categories'] = ee()->input->post('unique_categories');
			$feed['category_variable'] = ee()->input->post('category_variable');
			$feed['category_delimiter'] = ee()->input->post('category_delimiter');
			
			
			if($id){
				ee()->db->where('id', $id)->update('simplee_scrape_feeds', $feed);
				ee()->db->where('feed_id', $id)->delete('simplee_scrape_fields');
				ee()->db->where('feed_id', $id)->delete('simplee_scrape_categories');
			} else {
				ee()->db->insert('simplee_scrape_feeds', $feed);
				$id = ee()->db->insert_id();
			}
			
			
			$fields = array();
			foreach($this->default_fields as $key => $label){
				$value = ee()->input->post($key);
				$type = ee()->input->post($key."_type");
				$attribute = ee()->input->post($key."_attribute");
				$location = ee()->input->post($key."_location");
				
				if($value != "" || $attribute != ""){
					$f = array(
						"feed_id" 	=> $id,
						"name"		=> $key,
						"value"		=> $value,
						"type"		=> $type,
						"attribute"	=> $attribute,
						"location"	=> $location
					);
					ee()->db->insert('simplee_scrape_fields', $f);
				}
			}
			
			$query = ee()->db->from('exp_channel_fields')->get();
			foreach($query->result_array() as $field){
				$name = "field_id_".$field['field_id'];
				$value = ee()->input->post($name);
				$type = ee()->input->post($name."_type");
				$attribute = ee()->input->post($name."_attribute");
				$location = ee()->input->post($name."_location");
				
				if($value != "" || $attribute != ""){
					$f = array(
						"feed_id" 	=> $id,
						"name"		=> $name,
						"value"		=> $value,
						"type"		=> $type,
						"attribute"	=> $attribute,
						"location"	=> $location
					);
					ee()->db->insert('simplee_scrape_fields', $f);
				}
			}
			
			$query = ee()->db->from('exp_categories')->get();
			foreach($query->result_array() as $category){
				$name = "category_".$category['cat_id'];
				$value = ee()->input->post($name);
				if($value && $value != ""){
					$c = array(
						"feed_id" 			=> $id,
						"category_id"		=> $category['cat_id'],
						"alternate_names"	=> $value
					);
					ee()->db->insert('simplee_scrape_categories', $c);
				}
			}
			
			ee()->functions->redirect($this->base_url);
		}
		
		function feed_delete(){
			$feed_id = $_GET['feed_id'];
			if(!$feed_id) return ee()->output->show_user_error('general', 'Must specify feed id.');
			ee()->db->where('id', $feed_id)->delete('simplee_scrape_feeds');
			ee()->db->where('feed_id', $feed_id)->delete('simplee_scrape_fields');
			ee()->db->where('feed_id', $feed_id)->delete('simplee_scrape_categories');
			ee()->functions->redirect($this->base_url);
		}
	}
?>