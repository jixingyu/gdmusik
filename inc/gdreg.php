<?php
require_once( get_stylesheet_directory() . '/inc/gdupload.php' );
/*
 * 添加数据库表 @author xy at 2015.04.28
 * 
 */

/*
 * 
 * 添加
 * 
 */

function add_gd_reg($reg) {

	$reg['user_id'] = intval($reg['user_id']);
	$reg['realname'] = sanitize_text_field($reg['realname']);
	$reg['email'] = sanitize_email($reg['email']);

	if( !$reg['user_id'] || empty($reg['realname']) || empty($reg['email']) ) return;

    if (empty($_FILES['test_image1']['name'])) {
    	return '请上传test_image1';
    }
    if (empty($_FILES['test_image2']['name'])) {
    	return '请上传test_image2';
    }

	$path = gd_path($reg['user_id']);
    if (!is_dir($path)) {
        @mkdir($path, 0755, true);
    }

    $upload = new GD_Upload();
    $config = array(
    	'upload_path' => $path,
    	'allowed_types' => 'jpg|png|gif|jpeg',
    	'max_size' => 2097152,
	);
	$uploaded_files = array();
	foreach (array('test_image1', 'test_image2') as $field) {
	    $filename = $_FILES[$field]['name'];
	    $filename = $upload->clean_file_name(strtolower($filename));

	    $config['file_name'] = $filename;

	    $upload->initialize($config);
	    $uploaded = $upload->do_upload($field);
	    if (!$uploaded) {
	    	if (!empty($uploaded_files)) {
	    		foreach ($uploaded_files as $uploaded_file) {
	    			@unlink($path . '/' . $uploaded_file);
	    		}
	    	}
	        return strip_tags($upload->display_errors());
	    } else {
	        $upload_data = $upload->data();
	        $reg[$field] = $upload_data['file_name'];
	    }
	}

	$reg['create_time'] = !empty($reg['create_time']) ? $reg['create_time'] : current_time('mysql');
	$reg['update_time'] = !empty($reg['update_time']) ? $reg['update_time'] : current_time('mysql');
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'gd_reg';

	if( $wpdb->insert( $table_name, $reg ) )
		return $wpdb->insert_id;
	
	return 0;
	
}

function gd_path($uid, $file = false){
	if ($file) {
		return WP_CONTENT_DIR . '/uploads/gd-reg/' . $uid;
	} else {
		return WP_CONTENT_DIR . '/uploads/gd-reg/' . $uid . '/' . $file;
	}
}

function gd_url($uid, $file){
	return home_url('wp-content/uploads/gd-reg/' . $uid . '/' . $file);
}

/*
 * 
 * 更新
 * 
 */

function update_gd_reg( $id, $reg ){

	if (isset($reg['user_id'])) {
		unset($reg['user_id']);
	}
	if (isset($reg['id'])) {
		unset($reg['id']);
	}
	$id = intval($id);

	if(!$id) return;
	if (isset($reg['realname'])) {
		$reg['realname'] = sanitize_text_field($reg['realname']);
	}
	if (isset($reg['email'])) {
		$reg['email'] = sanitize_email($reg['email']);
	}
	$reg['update_time'] = $reg['update_time'] ? $reg['update_time'] : current_time('mysql');

	global $wpdb;
	$table_name = $wpdb->prefix . 'gd_reg';

	return $wpdb->update( $table_name, $reg, array('reg_id' => $id));
	
}

/*
 * 
 * 删除
 * 
 */

function delete_gd_reg( $id, $uid=0){

	$id = intval($id);
	$uid = intval($uid);

	if( !$id && !$uid ) return;

	global $wpdb;
	$table_name = $wpdb->prefix . 'gd_reg';

	$where = array();
	if($id) $where['reg_id'] = $id;
	if($uid) $where['user_id'] = $uid;

	return $wpdb->delete( $table_name, $where );

}

/*
 * 
 * 获取
 * 
 */

function get_gd_reg( $id, $uid ){
	
	$id = intval($id);
	$uid = intval($uid);
	
	if( !$id || !$uid ) return;

	global $wpdb;
	$table_name = $wpdb->prefix . 'gd_reg';

	$check = $wpdb->get_row( "SELECT * FROM $table_name WHERE reg_id='$id' AND user_id='$uid'", ARRAY_A );
	if($check) {
		$check['test_image1'] = gd_url($check['user_id'], $check['test_image1']);
		$check['test_image2'] = gd_url($check['user_id'], $check['test_image2']);
		return $check;
	}

	return 0;

}

/*
 * 
 * 获取列表
 * 
 */

function get_gd_reglist( $uid=0 , $count=0, $where='', $limit=0, $offset=0 ){

	$uid = intval($uid);

	if( !$uid ) return;

	global $wpdb;
	$table_name = $wpdb->prefix . 'gd_reg';

	if($where) $where = " AND $where";
	if($count){
		$check = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE user_id='$uid' $where" );
		if($check)	return $check;
	}else{
		$check = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id='$uid' $where ORDER BY create_time DESC LIMIT $offset,$limit" );
		if($check){
			foreach ($check as $row) {
				$row->test_image1 = gd_url($row->user_id, $row->test_image1);
				$row->test_image2 = gd_url($row->user_id, $row->test_image2);
			}
			return $check;
		}
	}

	return 0;

}

