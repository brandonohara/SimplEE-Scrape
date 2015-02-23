<?php
    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang('simplee_scrape_feed_id'),
        lang('simplee_scrape_feed_name'),
        lang('simplee_scrape_feed_type'),
        lang('simplee_scrape_feed_url'),
        lang('simplee_scrape_feed_delete')
    );

    foreach($feeds as $feed){
        $this->table->add_row(
            $feed->id,
            '<a href="'.$feed->edit_link.'">'.$feed->name.'</a>',
            $feed->type,
            $feed->url,
            '<a href="'.$feed->delete_link.'" onclick="return confirm(\'Are you sure you want to delete '.$feed->name.'?\')">Delete</a>'
        );
    }

	echo $this->table->generate();

?>