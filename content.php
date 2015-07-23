<?php

/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */
 
?>
		<article id="content" class="col-lg-8 col-md-8 single" data-post-id="<?php the_ID(); ?>" role="article" itemscope itemtype="http://schema.org/Article">
		<?php echo dmeng_adsense('single','top');?>
			<div class="<?php echo apply_filters('dmeng_single_post_panel_class', 'panel panel-default');?>">
				<div class="panel-body">
					<div class="entry-header page-header">
						<h1 class="entry-title h3" itemprop="name"><?php echo apply_filters( 'dmeng_the_title', esc_html(get_the_title()) );?><?php if( is_preview() || current_user_can('edit_post', get_the_ID()) ) echo ' <small><a href="'.get_edit_post_link().'" data-no-instant>'.__('Edit This').'</a></small>'; ?></h1>
						<?php gd_post_meta();?>
					</div>
					<div class="entry-content"  itemprop="articleBody" data-no-instant>
						<?php the_content();?>
						<?php dmeng_post_page_nav(); ?>
					</div>
					<?php dmeng_post_copyright(get_the_ID());?>
				</div>
				<?php dmeng_post_footer();?>
			</div>
			<?php dmeng_post_nav();?>
			<?php echo dmeng_adsense('single','comment');?>
			<?php echo dmeng_adsense('single','bottom');?>
		 </article><!-- #content -->
