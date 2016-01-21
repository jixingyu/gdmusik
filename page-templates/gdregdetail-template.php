<?php
/**
 * Template Name: Enroll Detail Template
 *
 * Displays the Enroll Detail Template of the theme.
 */
require_once( get_stylesheet_directory() . '/inc/gdreg.php' );
global $wp_query;

if (is_user_logged_in()) {
	$current_user = wp_get_current_user();
	if (!isset($_GET['id']) || empty($_GET['id'])) {
		$message = '报名信息不存在！';
	} else {
		$reg = get_gd_reg($_GET['id'], $current_user->ID);
		if (empty($reg)) {
			$message = '报名信息不存在！';
		}
	}
}

get_header();
get_header('masthead');

?>
<style type="text/css">
dl dt {
    color: #999;
}
dl dd {
	margin: 5px 0;
}
dd img {
	margin-top: 5px;
}
</style>
<div id="main" class="container" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
	<div class="row">
		<?php while ( have_posts() ) : the_post();?>
			<article id="content" class="col-lg-8 col-md-8 single" data-post-id="<?php the_ID(); ?>" role="article" itemscope itemtype="http://schema.org/Article">
			<?php if (!is_user_logged_in()) : echo '<div class="alert alert-success" role="alert">请先<a href="' . wp_login_url(home_url(add_query_arg(array(),$wp->request))) . '">登录</a></div>';?>
			<?php elseif (!empty($message)) : echo '<div class="alert alert-warning" role="alert">' . $message . '</div>';?>
			<?php else : ?>
				<div class="<?php echo apply_filters('dmeng_single_post_panel_class', 'panel panel-default');?>">
					<div class="panel-body">
						<div class="entry-header page-header">
							<h1 class="entry-title h3" itemprop="name"><?php echo apply_filters( 'dmeng_the_title', esc_html(get_the_title()) );?><?php if( is_preview() || current_user_can('edit_post', get_the_ID()) ) echo ' <small><a href="'.get_edit_post_link().'" data-no-instant>'.__('Edit This').'</a></small>'; ?></h1>
						</div>
						<div class="entry-content"  itemprop="articleBody" data-no-instant>
							<?php the_content();?>
							<dl class="dl-horizontal">
								<dt>邮箱：</dt>
								<dd><?php echo $reg['email']; ?></dd>
								<dt>姓名：</dt>
								<dd><?php echo $reg['realname']; ?></dd>
								<dt>test_image1：</dt>
								<dd><img src="<?php echo $reg['test_image1']; ?>" /></dd>
								<dt>test_image2：</dt>
								<dd><img src="<?php echo $reg['test_image2']; ?>" /></dd>
							</dl>
						</div>
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
