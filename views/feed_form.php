<?=form_open_multipart($action_url, '', $form_hidden);?>
<?=validation_errors()?>
<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <th><?=lang('add_class_label_title')?></th>
            <th><?=lang('add_class_label_value')?></th>
        </tr>
    </thead>
    <tbody>
    	<?php
	    	$count = 0;
        	$oddeven = array('odd', 'even');
        	
    		foreach($feed_fields as $key => $field){
	        	
	        	$text  = "<tr class='".$oddeven[$count % 2]."' ".($count == 0 ? "style='display: none'" : "").">";
	        	$text .= "<td>".$field['label']."</td>";
	        	$text .= "<td>";
	        	
	        	if($field['type'] == 0)
	        		$text .= form_input($key, $field['data']);
	        	else if($field['type'] == 1)
	        		$text .= form_dropdown($key, $field['options'], $field['data'], $field['js']);
	        		
	        	$text .= "</td></tr>";
	        	echo $text;
	        	$count++;
    		}
    	?>
    </tbody>
</table>       
<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <th>Field Name</th>
            <th>Field Value</th>
            <th>Field Type</th>
            <th>Attribute</th>
            <th>Location</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        	$count = 0;
        	$oddeven = array('odd', 'even');
        
        	foreach($fields as $field){
	        	
	        	$text  = "<tr class='".$oddeven[$count % 2].$field['class']."'>";
	        	$text .= "<td>".$field['label']."</td>";
	        	$text .= "<td>".form_input($field['id'], $field['data']['value'])."</td>";
	        	$text .= "<td>".form_dropdown($field['id']."_type", $field_types, $field['data']['type'])."</td>";
	        	$text .= "<td>".form_input($field['id']."_attribute", $field['data']['attribute'])."</td>";
	        	$text .= "<td>".form_input($field['id']."_location", $field['data']['location'])."</td>";
	        	$text .= "</tr>";
	        	echo $text;
	        	
	        	$count++;
        	}
        ?>
    </tbody>
</table>

<table class="mainTable category_table" border="0" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <th>Category</th>
            <th>Alternate Names</th>
        </tr>
    </thead>
    <tbody>
        
        <?php
        	$count = 0;
        	$oddeven = array('odd', 'even');
        
        	foreach($categories as $category){
	        	
	        	$text  = "<tr class='".$oddeven[$count % 2].$category['class']."'>";
	        	$text .= "<td>".$category['cat_name']."</td>";
	        	$text .= "<td>".form_input("category_".$category['cat_id'], $category['data']['alternate_names'])."</td>";
	        	$text .= "</tr>";
	        	echo $text;
	        	
	        	$count++;
        	}
        ?>
    </tbody>
</table>

<script type="text/javascript">
	function simpleeScrapeShowChannelFields(){
		var id = $("select[name=channel_id]").val();
		$(".channel_field").not(".channel" + id).hide();
		$(".channel_category").not(".channel" + id).hide();
		$(".channel" + id).show();
	}
	
	function simpleeScrapeToggleCategories(val){
		console.log(val);
		if(val == 1)
			$(".category_table").hide();
		else
			$(".category_table").show();
	}
	simpleeScrapeShowChannelFields();
</script>


<?=form_submit('submit', lang('simplee_scrape_feed_submit'), 'class="submit"')?>

<?=form_close()?>