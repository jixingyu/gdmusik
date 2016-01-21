<?php
/**
 * Template Name: Enroll List Template
 *
 * Displays the Enroll List Template of the theme.
 */
require_once( get_stylesheet_directory() . '/inc/gdreg.php' );
global $wp_query;

get_header();
get_header('masthead');

?>
<style type="text/css">
table tr:hover {
	cursor: pointer;
}
</style>
<div id="main" class="container" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">
	<div class="row">
			<?php while ( have_posts() ) : the_post();?>
				<article id="content" class="col-lg-8 col-md-8 single" data-post-id="<?php the_ID(); ?>" role="article" itemscope itemtype="http://schema.org/Article">
				<?php if (!is_user_logged_in()) : echo '<div class="alert alert-success" role="alert">请先<a href="' . wp_login_url(home_url(add_query_arg(array(),$wp->request))) . '">登录</a></div>';?>
				<?php else : ?>
					<div class="<?php echo apply_filters('dmeng_single_post_panel_class', 'panel panel-default');?>">
						<div class="panel-body">
							<div class="entry-header page-header">
								<h1 class="entry-title h3" itemprop="name"><?php echo apply_filters( 'dmeng_the_title', esc_html(get_the_title()) );?><?php if( is_preview() || current_user_can('edit_post', get_the_ID()) ) echo ' <small><a href="'.get_edit_post_link().'" data-no-instant>'.__('Edit This').'</a></small>'; ?></h1>
							</div>
							<div class="entry-content"  itemprop="articleBody" data-no-instant>
								<?php the_content();?>
								<?php
									$current_user = wp_get_current_user();

									$paged = max( 1, get_query_var('page') );
									$number = get_option('posts_per_page', 10);
									$offset = ($paged-1)*$number;
									$total = get_gd_reglist($current_user->ID , 'count');
									$pages = ceil($total/$number);

									$reglist = get_gd_reglist($current_user->ID, 0, '', $number, $offset);
									if($reglist){
								?>
								<ul id="author-message"><li class="tip">共有 <?php echo $total;?> 条报名记录</li></ul>
								<table class="table table-striped table-bordered table-hover reg-table">
									<thead>
										<tr>
											<th>邮箱</th>
											<th>姓名</th>
											<th>报名时间</th>
										</tr>
									</thead>
									<tbody>
								<?php
										foreach( $reglist as $reg ){
											echo sprintf('<tr data-detail="%s"><td>%s</td><td>%s</td><td>%s</td></tr>', home_url('enroll-detail?id=' . $reg->reg_id), $reg->email, $reg->realname, $reg->create_time);
										}
								?>
									</tbody>
								</table>
								<?php
										if($pages>1)
											echo '<ul id="author-message"><li class="tip">'.sprintf(__('第 %1$s 页，共 %2$s 页，每页显示 %3$s 条。','dmeng'),$paged, $pages, $number).'</li></ul>';
										echo dmeng_pager($paged, $pages);
									} else {
								?>
								<ul id="author-message"><li class="tip">暂无报名记录</li></ul>
								<?php } ?>
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
