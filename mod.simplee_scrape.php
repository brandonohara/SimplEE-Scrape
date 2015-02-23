<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * simplEE Scrape Module
	 *
	 * @package   simplEE Scrape
	 * @author    Brandon O'Hara <brandon@brandonohara.com>
	 * @copyright Copyright (c) 2014 Brandon O'Hara
	 */
	 
	require_once(PATH_THIRD."simplee_scrape/config.php");
	
	class Simplee_scrape {
		
		public $feeds = NULL;
		
		function __construct() {
			ee()->session->create_new_session(1);
			ee()->session->fetch_session_data();
			ee()->session->fetch_member_data();
		}
		
		function print_array($arr){
			echo "<pre>";
			print_r($arr);
			echo "</pre>";
		}
		
		function _get_feeds(){
			if(!$this->feeds){
				$this->feeds = array();
				$query = ee()->db->from("simplee_scrape_feeds")->get();
				foreach($query->result_array() as $row){
					switch($row['type']){
						case SIMPLEE_SCRAPE_TYPE_XML: $feed = new SS_XML_Feed($row); break;
						case SIMPLEE_SCRAPE_TYPE_JSON: $feed = new SS_JSON_Feed($row); break;
					}
					array_push($this->feeds, $feed);
				}
			}
			return $this->feeds;
		}
		
		function update(){
			$feeds = $this->_get_feeds();
			foreach($feeds as $feed){
				echo $feed->name."<br>";
				$feed->update();
			}
		}
		
	}

?> 