<?php
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
	
	if( !$reg['user_id'] || empty($reg['realname']) ) return;

	$reg['create_time'] = $reg['create_time'] ? $reg['create_time'] : current_time('mysql');
	$reg['update_time'] = $reg['update_time'] ? $reg['update_time'] : current_time('mysql');
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'gd_reg';

	if( $wpdb->insert( $table_name, $reg ) )
		return $wpdb->insert_id;
	
	return 0;
	
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

function get_gd_reg( $uid ){
	
	$uid = intval($uid);
	
	if( !$uid ) return;

	global $wpdb;
	$table_name = $wpdb->prefix . 'gd_reg';

	$check = $wpdb->get_row( "SELECT reg_id,email,realname FROM $table_name WHERE user_id='$uid'" );
	if($check)	return $check;

	return 0;

}
