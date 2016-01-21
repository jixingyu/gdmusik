<?php
/**
 * Template Name: Enroll Template
 *
 * Displays the Enroll Template of the theme.
 */
require_once( get_stylesheet_directory() . '/inc/gdreg.php' );
global $wp_query;

if (is_user_logged_in()) {
	$current_user = wp_get_current_user();
	// $myreg = get_gd_reg($current_user->ID);
}

get_header(); ?>
<?php
get_header('masthead');

if (isset($_POST['gdregNonce']) && is_user_logged_in()) {
	if (!wp_verify_nonce($_POST['gdregNonce'], 'gdreg-nonce' ) ) {
		$message = __('安全认证失败，请重试！','dmeng');
	} else {
		$email = $_POST['email'];
		$realname = $_POST['realname'];
		$postReg = array(
			'email'     => $email,
			'realname'  => $realname,
		);
		// if (empty($myreg)) {
			$postReg['user_id'] = $current_user->ID;
			$ret = add_gd_reg($postReg);
			$myreg = $postReg;
			if (is_numeric($ret)) {
				if ($ret) {
					$reg_success = '恭喜您报名成功，<a href="' . home_url('enroll-list') . '">点击</a>查看报名列表！';
				} else {
					$message = '报名失败，请重试或联系客服！';
				}
			} else {
				$message = $ret;
			}
		// }
		//  else {
		// 	if (update_gd_reg($myreg['reg_id'], $postReg)) {
		// 		$message = '更新成功';
		// 	}
		// }
	}
}

?>

<div id="main" class="container" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
	<div class="row">
		<?php while ( have_posts() ) : the_post();?>
			<article id="content" class="col-lg-8 col-md-8 single" data-post-id="<?php the_ID(); ?>" role="article" itemscope itemtype="http://schema.org/Article">
			<?php if (!is_user_logged_in()) : echo '<div class="alert alert-success" role="alert">请先<a href="' . wp_login_url(home_url(add_query_arg(array(),$wp->request))) . '">登录</a></div>';?>
			<?php else : ?>
			<?php if (!empty($message)) echo '<div class="alert alert-warning" role="alert">' . $message . '</div>';?>
				<div class="<?php echo apply_filters('dmeng_single_post_panel_class', 'panel panel-default');?>">
					<div class="panel-body">
						<?php if (!empty($reg_success)) : ?>
							<h4><?php echo $reg_success; ?></h4>
						<?php else : ?>
							<div class="entry-header page-header">
								<h1 class="entry-title h3" itemprop="name"><?php echo apply_filters( 'dmeng_the_title', esc_html(get_the_title()) );?><?php if( is_preview() || current_user_can('edit_post', get_the_ID()) ) echo ' <small><a href="'.get_edit_post_link().'" data-no-instant>'.__('Edit This').'</a></small>'; ?></h1>
							</div>
							<div class="entry-content"  itemprop="articleBody" data-no-instant>
								<?php the_content();?>
								<form action="<?php the_permalink(); ?>" class="form-horizontal" method="post" enctype="multipart/form-data">
									<div class="form-group">
										<label for="email" class="col-sm-3">邮箱</label>
										<div class="col-sm-9">
										<input type="email" class="form-control" id="email" name="email" value="<?php if (!empty($myreg)) echo $myreg['email'];?>">
										</div>
									</div>
									<div class="form-group">
										<label for="realname" class="col-sm-3">真实姓名</label>
										<div class="col-sm-9">
										<input type="text" class="form-control" id="realname" name="realname" value="<?php if (!empty($myreg)) echo $myreg['realname'];?>">
										</div>
									</div>
									<div class="form-group">
			                            <label class="col-sm-3">上传 test_image1</label>
										<div class="col-sm-9">
			                            <input type="file" name="test_image1">
			                            </div>
                                    </div>
									<div class="form-group">
			                            <label class="col-sm-3">上传 test_image2</label>
										<div class="col-sm-9">
			                            <input type="file" name="test_image2">
			                            </div>
                                    </div>
									<div class="form-group">
										<div class="col-sm-12">
										<input type="hidden" name="gdregNonce" value="<?php echo wp_create_nonce('gdreg-nonce');?>" >
										<button type="submit" class="btn btn-primary"><?php if (empty($myreg)) echo '报名'; else echo '更新';?></button>
										</div>
									</div>
								</form>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			</article>
		<?php endwhile; // end of the loop. ?>
		<?php get_sidebar();?>
	</div>
</div><!-- #main -->
<?php get_footer('colophon'); ?>
<?php get_footer(); ?>
