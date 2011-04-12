<?php

/*
+----------------------------------------------------------------+
+	wp-table-install V1.11
+	by Alex Rabe
+       required for wp-table
+----------------------------------------------------------------+
*/

//#################################################################

function wptable_install() {

global $table_prefix, $wpdb;

	$wptable_cfg=get_option('wptable');
 
	// set tablename

	$table_name = $table_prefix . "golftable"; 		// main 
	$table_name2 = $table_prefix . "golfresult"; 	// contain values
	
	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

	if($wpdb->get_var("show tables like '$table_name'") != $table_name){
	 
	      $sql = "CREATE TABLE ".$table_name." (
	      table_aid MEDIUMINT(10) NOT NULL AUTO_INCREMENT,
	      table_name VARCHAR(200) DEFAULT 'Table name' NOT NULL,
	      description VARCHAR(200) NOT NULL,
		  border_color VARCHAR(7) DEFAULT '#E58802' NOT NULL,
		  head_color VARCHAR(7) DEFAULT '#E58802' NOT NULL,
		  alt_color VARCHAR(7) DEFAULT '#F4F4EC' NOT NULL,
		  alternative TINYINT(1) DEFAULT '1' NOT NULL,
		  show_name TINYINT(1) DEFAULT '1' NOT NULL,
		  show_desc TINYINT(1) DEFAULT '0' NOT NULL,
		  head_bold TINYINT(1) DEFAULT '1' NOT NULL,
		  table_align VARCHAR(7) DEFAULT 'center' NOT NULL,
		  border_size MEDIUMINT(5) DEFAULT '1' NOT NULL,
		  cellpadding MEDIUMINT(5) DEFAULT '0' NOT NULL,
		  cellspacing MEDIUMINT(5) DEFAULT '1' NOT NULL,
		  table_width MEDIUMINT(5) DEFAULT '0' NOT NULL,
	      UNIQUE KEY id (table_aid),
	      PRIMARY KEY (table_aid)
	     );";
	     
    dbDelta($sql);

	  // Insert first sample table
      $results = $wpdb->query("INSERT INTO $table_name (table_name,description) VALUES ('Table name 1','This is your first demo table') ");

	}

	if($wpdb->get_var("show tables like '$table_name2'") != $table_name2){

		  $sql = "CREATE TABLE ".$table_name2." (
	      result_aid mediumint(10) NOT NULL AUTO_INCREMENT,
	      table_id MEDIUMINT(10) DEFAULT '0' NOT NULL,
	      row_id MEDIUMINT(10) DEFAULT '1' NOT NULL,
	      value VARCHAR(200) NOT NULL,
	      UNIQUE KEY id (result_aid),
	      PRIMARY KEY (result_aid)
	     );";

      dbDelta($sql);

	  // Insert first sample entries
      $results = $wpdb->query("INSERT INTO $table_name2 (table_id,row_id,value) VALUES ('1','0','100') ");
      $results = $wpdb->query("INSERT INTO $table_name2 (table_id,row_id,value) VALUES ('1','0','100') ");
      $results = $wpdb->query("INSERT INTO $table_name2 (table_id,row_id,value) VALUES ('1','0','100') ");
      $results = $wpdb->query("INSERT INTO $table_name2 (table_id,row_id,value) VALUES ('1','1','Column 1') ");
      $results = $wpdb->query("INSERT INTO $table_name2 (table_id,row_id,value) VALUES ('1','1','Column 2') ");
      $results = $wpdb->query("INSERT INTO $table_name2 (table_id,row_id,value) VALUES ('1','1','Column 3') ");
	
	}

	// update to v1.10 table
	if ($wptable_cfg[wpt_version] < 110 )
	{

		$result=$wpdb->query('SHOW COLUMNS FROM '.$table_name.' LIKE "table_align"');
		if (!$result) $wpdb->query("ALTER TABLE ".$table_name." ADD table_align VARCHAR(7) DEFAULT 'center' NOT NULL");

		$result=$wpdb->query('SHOW COLUMNS FROM '.$table_name.' LIKE "border_size"');
		if (!$result) $wpdb->query("ALTER TABLE ".$table_name." ADD border_size MEDIUMINT(5) DEFAULT '1' NOT NULL");
		
		$result=$wpdb->query('SHOW COLUMNS FROM '.$table_name.' LIKE "cellpadding"');
		if (!$result) $wpdb->query("ALTER TABLE ".$table_name." ADD cellpadding MEDIUMINT(5) DEFAULT '0' NOT NULL");
		
		$result=$wpdb->query('SHOW COLUMNS FROM '.$table_name.' LIKE "cellspacing"');
		if (!$result) $wpdb->query("ALTER TABLE ".$table_name." ADD cellspacing MEDIUMINT(5) DEFAULT '1' NOT NULL");
		
		$result=$wpdb->query('SHOW COLUMNS FROM '.$table_name.' LIKE "table_width"');
		if (!$result) $wpdb->query("ALTER TABLE ".$table_name." ADD table_width MEDIUMINT(5) DEFAULT '0' NOT NULL");
 
	}

	$wptable_cfg[wpt_version]= 110; // set to actual version
	
	update_option('wptable', $wptable_cfg);
	
}

?>