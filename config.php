<?php
	define("SIMPLEE_SCRAPE_NAME", "SimplEE Scrape");
	define("SIMPLEE_SCRAPE_VERSION", "1.0.1");
	define("SIMPLEE_SCRAPE_EE_NAME", "Simplee_scrape");
	
	
	define("SIMPLEE_SCRAPE_TYPE_XML", 0);
	define("SIMPLEE_SCRAPE_TYPE_JSON", 1);
	
	define("SIMPLEE_SCRAPE_UT_UPDATE", 0);
	define("SIMPLEE_SCRAPE_UT_CLOSE_OLD", 1);

	require_once(PATH_THIRD."simplee_scrape/models/feed.php");
	require_once(PATH_THIRD."simplee_scrape/models/xml_feed.php");
	require_once(PATH_THIRD."simplee_scrape/models/json_feed.php");
	
	require_once(PATH_THIRD."simplee_scrape/models/field.php");
	require_once(PATH_THIRD."simplee_scrape/models/category.php");
	require_once(PATH_THIRD."simplee_scrape/models/entry.php");
	
?>