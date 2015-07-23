<?php
/**
 * Template Name: Registration Template
 *
 * Displays the Registration Template of the theme.
 */
require_once( get_stylesheet_directory() . '/inc/gdreg.php' );
global $wp_query;

if (is_user_logged_in()) {
	$current_user = wp_get_current_user();
	$myreg = get_gd_reg($current_user->ID);
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
		if (empty($myreg)) {
			$postReg['user_id'] = $current_user->ID;
			if (add_gd_reg($postReg)) {
				$message = '报名成功';
			}
		} else {
			if (update_gd_reg($myreg->reg_id, $postReg)) {
				$message = '更新成功';
			}
		}
	}
}

?>
<style type="text/css">
.article_form {
	background: #f1f1f1;
	border: 1px solid #dadada;
	padding: 8px;
	margin: 0 auto;
	font-size: 14px;
	line-height: 26px;
	max-width: 90%;
	border-radius: 5px;
}
</style>
<div id="main" class="container" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
	<div class="row">
			<?php while ( have_posts() ) : the_post();?>
				<article id="content" class="col-lg-8 col-md-8 single" data-post-id="<?php the_ID(); ?>" role="article" itemscope itemtype="http://schema.org/Article">
				<?php echo dmeng_adsense('single','top');?>
				<?php if (!is_user_logged_in()) echo '<div class="alert alert-success" role="alert">报名请先<a href="' . wp_login_url() . '">登录</a></div>';?>
				<?php if ($message) echo '<div class="alert alert-success" role="alert">' . $message . '</div>';?>
					<div class="<?php echo apply_filters('dmeng_single_post_panel_class', 'panel panel-default');?>">
						<div class="panel-body">
							<div class="entry-header page-header">
								<h1 class="entry-title h3" itemprop="name"><?php echo apply_filters( 'dmeng_the_title', esc_html(get_the_title()) );?><?php if( is_preview() || current_user_can('edit_post', get_the_ID()) ) echo ' <small><a href="'.get_edit_post_link().'" data-no-instant>'.__('Edit This').'</a></small>'; ?></h1>
							</div>
							<div class="entry-content"  itemprop="articleBody" data-no-instant>
								<?php the_content();?>
								<form action="<?php the_permalink(); ?>" class="form-horizontal article_form" method="post">
									<div class="form-group">
										<label for="email" class="col-sm-2 control-label">邮箱</label>
										<div class="col-sm-10">
										<input type="email" class="form-control" id="email" name="email" value="<?php if (!empty($myreg)) echo $myreg->email;?>">
										</div>
									</div>
									<div class="form-group">
										<label for="realname" class="col-sm-2 control-label">真实姓名</label>
										<div class="col-sm-10">
										<input type="text" class="form-control" id="realname" name="realname" value="<?php if (!empty($myreg)) echo $myreg->realname;?>">
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10">
										<input type="hidden" name="gdregNonce" value="<?php echo wp_create_nonce('gdreg-nonce');?>" >
										<button type="submit" class="btn btn-primary"><?php if (empty($myreg)) echo '报名'; else echo '更新';?></button>
										</div>
									</div>
								</form>
							<?php dmeng_post_copyright(get_the_ID());?>
						</div>
						<?php dmeng_post_footer();?>
					</div>
					<?php dmeng_post_nav();?>
					<?php echo dmeng_adsense('single','comment');?>
					<?php echo dmeng_adsense('single','bottom');?>
				</article>
			<?php endwhile; // end of the loop. ?>
		<?php get_sidebar();?>
	</div>
</div><!-- #main -->
<?php get_footer('colophon'); ?>
<?php get_footer(); ?>
