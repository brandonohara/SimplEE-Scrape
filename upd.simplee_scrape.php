<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * scrapEE Module Control Panel
	 *
	 * @package   scrapEE
	 * @author    Brandon O'Hara <brandon@brandonohara.com>
	 * @copyright Copyright (c) 2014 Brandon O'Hara
	 */
	 
	require_once(PATH_THIRD."simplee_scrape/config.php");
	
	class Simplee_scrape_upd {
	
		var $version = SIMPLEE_SCRAPE_VERSION;
		
		function install() {
			ee()->load->dbforge();

			// -------------------------------------------
			//  Add row to exp_modules
			// -------------------------------------------
	
			ee()->db->insert('modules', array(
				'module_name'        => SIMPLEE_SCRAPE_EE_NAME,
				'module_version'     => SIMPLEE_SCRAPE_VERSION,
				'has_cp_backend'     => 'y',
				'has_publish_fields' => 'n'
			));
	
			// -------------------------------------------
			//  Add rows to exp_actions
			// -------------------------------------------
	
			// file manager actions
			$actions = array("update");
			foreach($actions as $action){
				ee()->db->insert('actions', array('class' => SIMPLEE_SCRAPE_EE_NAME, 'method' => $action));
			}
			
			$fields = array(
				'id'				=> array('type' => 'int', 'constraint' => 10, 'unsigned' => TRUE, 'auto_increment' => TRUE),
				'channel_id'		=> array('type' => 'int', 'constraint' => 10, 'unsigned' => TRUE),
				'name'				=> array('type' => 'varchar', 'constraint' => 50),
				'url'				=> array('type' => 'varchar', 'constraint' => 500),
				'location'			=> array('type' => 'varchar', 'constraint' => 100),
				'type'				=> array('type' => 'int', 'constraint' => 3, 'unsigned' => TRUE),
				'update_type'		=> array('type' => 'int', 'constraint' => 3, 'unsigned' => TRUE),
				'unique_categories'	=> array('type' => 'smallint', 'constraint' => 1, 'unsigned' => TRUE),
				'category_variable'	=> array('type' => 'varchar', 'constraint' => 100),
				'category_delimiter'=> array('type' => 'varchar', 'constraint' => 25),
				'title_prefix'		=> array('type' => 'varchar', 'constraint' => 50)
			);
	
			ee()->dbforge->add_field($fields);
			ee()->dbforge->add_key('id', TRUE);
			ee()->dbforge->create_table('simplee_scrape_feeds');
			
			
			
			
			
			$fields = array(
				'feed_id'			=> array('type' => 'int', 'constraint' => 10, 'unsigned' => TRUE),
				'name'				=> array('type' => 'varchar', 'constraint' => 50),
				'value'				=> array('type' => 'varchar', 'constraint' => 50),
				'location'			=> array('type' => 'varchar', 'constraint' => 100),
				'attribute'			=> array('type' => 'varchar', 'constraint' => 100),
				'type'				=> array('type' => 'int', 'constraint' => 11)
			);
	
			ee()->dbforge->add_field($fields);
			ee()->dbforge->add_key('feed_id', TRUE);
			ee()->dbforge->add_key('name', TRUE);
			ee()->dbforge->create_table('simplee_scrape_fields');
			
			
			
			
			$fields = array(
				'feed_id'			=> array('type' => 'int', 'constraint' => 10, 'unsigned' => TRUE),
				'category_id'		=> array('type' => 'int', 'constraint' => 10, 'unsigned' => TRUE),
				'alternate_names'	=> array('type' => 'varchar', 'constraint' => 250)
			);
	
			ee()->dbforge->add_field($fields);
			ee()->dbforge->add_key('feed_id', TRUE);
			ee()->dbforge->add_key('category_id', TRUE);
			ee()->dbforge->create_table('simplee_scrape_categories');
		}
		
		function uninstall() {
			ee()->load->dbforge();
	
			ee()->db->select('module_id');
			$module_id = ee()->db->get_where('modules', array('module_name' => SIMPLEE_SCRAPE_EE_NAME))->row('module_id');
	
			ee()->db->where('module_id', $module_id);
			ee()->db->delete('module_member_groups');
	
			ee()->db->where('module_name', SIMPLEE_SCRAPE_EE_NAME);
			ee()->db->delete('modules');
	
			ee()->db->where('class', SIMPLEE_SCRAPE_EE_NAME);
			ee()->db->delete('actions');
	
			ee()->dbforge->drop_table('simplee_scrape_feeds');
			ee()->dbforge->drop_table('simplee_scrape_fields');
			ee()->dbforge->drop_table('simplee_scrape_categories');
			
			return TRUE;
		}
		
		function update($current = '') {
			return TRUE;
		}
	}
?>