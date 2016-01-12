<?php
add_action('admin_init', 'gd_author_upload_image_limit');

function gd_author_upload_image_limit(){
	//除管理员以外，其他用户都限制
	// if( !current_user_can( 'manage_options') )
		add_filter( 'wp_handle_upload_prefilter', 'gd_upload_image_limit' );
}

function gd_upload_image_limit( $file ){
	// 检测文件的类型是否是图片
	$mimes = array( 'image/jpeg', 'image/png', 'image/gif' );
	// 如果不是图片，直接返回文件
	if( !in_array( $file['type'], $mimes ) )
		return $file;

	if ( $file['size'] > 2097152 )
		$file['error'] = '图片太大了，请不要超过2M';

	return $file;
}

function gd_post_meta(){

	$output = '<div class="entry-meta">';

	//~ 字体设置按钮
	if( is_single() || is_page() )
		$output .= apply_filters('dmeng_post_meta_set_font', '<div class="entry-set-font"><span id="set-font-small" class="disabled">A<sup>-</sup></span><span id="set-font-big">A<sup>+</sup></span></div>');
	
	// $output .= apply_filters('dmeng_post_meta_author', '<span class="glyphicon glyphicon-user"></span><a href="'.esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ).'" itemprop="author">'.get_the_author().'</a>');

	$output .= apply_filters('dmeng_post_meta_date', '<span class="glyphicon glyphicon-calendar"></span><time class="entry-date" title="'.sprintf( __('发布于 %1$s 最后编辑于 %2$s ', 'dmeng'), get_the_time('Y-m-d H:i:s'), get_the_modified_time('Y-m-d H:i:s') ).'" datetime="'.get_the_date( 'c' ).'"  itemprop="datePublished">'.get_the_date().'</time>');
	
	// $output .= apply_filters('dmeng_post_meta_comments_number', '<span class="glyphicon glyphicon-comment"></span><a href="'.get_permalink().'#comments" itemprop="discussionUrl" itemscope itemtype="http://schema.org/Comment"><span itemprop="interactionCount">'.get_comments_number().'</span></a>');
	
	$traffic = get_dmeng_traffic('single', get_the_ID());

	$output .= apply_filters('dmeng_post_meta_traffic', '<span class="glyphicon glyphicon-eye-open"></span>'.sprintf( __( '%s 次浏览', 'dmeng' ) , ( is_singular() ? '<span data-num-views="true">'.$traffic.'</span>' : $traffic) ));

	//~ 如果是文章页则输出分类和标签，因为只有文章才有～
	if( get_post_type()=='post' ) {
		
		if( apply_filters('dmeng_post_meta_cat_show', true) ){
			$categories = get_the_category();
			if($categories){
				foreach($categories as $category) {
					$cats[] = '<a href="'.get_category_link( $category->term_id ).'" rel="category" itemprop="articleSection">'.$category->name.'</a>';
				}
				$output .= apply_filters('dmeng_post_meta_cat', '<span class="glyphicon glyphicon-folder-open"></span>' . join(' | ',$cats) );
			}
		}
		
		if( apply_filters('dmeng_post_meta_tag_show', true) ){
			$tags = get_the_tag_list('<span class="glyphicon glyphicon-tags"></span>',' | ');
			if($tags) $output .= apply_filters('dmeng_post_meta_tag', '<span itemprop="keywords">'.$tags.'</span>');
		}
	}

	$output .= '</div>';
	
	echo $output;

}

add_theme_support( 'custom-header', array(
	'default-image'          => get_stylesheet_directory_uri().'/images/screenshot_64.png',
	'random-default'         => false,
	'width'                  => 64,
	'height'                 => 64,
	'flex-height'            => true,
	'flex-width'             => true,
	'default-text-color'     => '444444',
	'header-text'            => true,
	'uploads'                => true,
	'admin-preview-callback' => 'dmeng_custom_header_admin_preview'
) );

add_action('load-%e5%a4%9a%e6%a2%a6%e4%b8%bb%e9%a2%98%e8%ae%be%e7%bd%ae_page_dmeng_options_log', 'gd_export_reg');
function gd_export_reg(){
	if ($_GET['exportgdreg'] == 1) {
		global $wpdb;
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="registration.csv"');
		header('Cache-Control: max-age=0');

		$fp = fopen('php://output', 'w');
		fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));
		$head = array('用户', '姓名', '邮箱', '报名时间', '更新时间');
		fputcsv($fp, $head);
		$offset = 0;
		$limit = 1000;
		while ($regs = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "gd_reg ORDER BY update_time DESC LIMIT $offset,$limit" )) {
			$offset += $limit;
			foreach ($regs as $reg) {
				fputcsv($fp, array(
					get_the_author_meta('display_name', $reg->user_id),
					$reg->realname,
					$reg->email,
					$reg->create_time,
					$reg->update_time,
				));
			}
			// ob_flush();
			// flush();
		}
		exit;
	}
}

add_filter('pre_site_transient_update_core',    create_function('$a', "return null;")); // 关闭核心提示

add_filter('pre_site_transient_update_plugins', create_function('$a', "return null;")); // 关闭插件提示

add_filter('pre_site_transient_update_themes',  create_function('$a', "return null;")); // 关闭主题提示

remove_action('admin_init', '_maybe_update_core');    // 禁止 WordPress 检查更新

remove_action('admin_init', '_maybe_update_plugins'); // 禁止 WordPress 更新插件

remove_action('admin_init', '_maybe_update_themes');  // 禁止 WordPress 更新主题