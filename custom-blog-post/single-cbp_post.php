<?php 
get_header();

while(have_posts()): the_post();
    $post_id = get_the_ID();

    $date = get_post_meta($post_id, 'cbp_date', true);
    $cat  = get_post_meta($post_id, 'cbp_category', true);

    $text1 = get_post_meta($post_id, 'cbp_text1', true);
    $img1  = get_post_meta($post_id, 'cbp_img1', true);

    $text2 = get_post_meta($post_id, 'cbp_text2', true);
    $img2  = get_post_meta($post_id, 'cbp_img2', true);

    $text3 = get_post_meta($post_id, 'cbp_text3', true);
?>

<style>
    .cbp-container{
        max-width: 900px;
        margin: 0 auto;
        padding: 30px;
        font-family: Arial, sans-serif;
        color:#222;
    }
    .cbp-title{
        font-size:32px;
        font-weight:700;
        margin-bottom:8px;
    }
    .cbp-meta{
        font-size:14px;
        color:#666;
        margin-bottom:25px;
    }
    .cbp-thumb{
        width:100%;
        height:auto;
        border-radius:6px;
        margin-bottom:25px;
        object-fit:cover;
    }
    .cbp-text{
        font-size:17px;
        line-height:1.7;
        margin-bottom:25px;
    }
    .cbp-img{
        width:100%;
        border-radius:6px;
        margin:25px 0;
        border:1px solid #ddd;
    }
</style>

<div class="cbp-container">

    <h1 class="cbp-title"><?php echo esc_html(get_the_title()); ?></h1>
    <div class="cbp-meta">
        <?php echo esc_html($date); ?> — <?php echo esc_html($cat); ?>
    </div>

    <?php if(has_post_thumbnail()): ?>
        <img class="cbp-thumb" src="<?php echo esc_url(get_the_post_thumbnail_url($post_id,'large')); ?>">
    <?php endif; ?>

    <?php if($text1): ?>
        <div class="cbp-text">
            <?php echo wp_kses_post(wpautop($text1)); ?>
        </div>
    <?php endif; ?>

    <?php if($img1): ?>
        <img class="cbp-img" src="<?php echo esc_url($img1); ?>" >
    <?php endif; ?>

    <?php if($text2): ?>
        <div class="cbp-text">
            <?php echo wp_kses_post(wpautop($text2)); ?>
        </div>
    <?php endif; ?>

    <?php if($img2): ?>
        <img class="cbp-img" src="<?php echo esc_url($img2); ?>" >
    <?php endif; ?>

    <?php if($text3): ?>
        <div class="cbp-text">
            <?php echo wp_kses_post(wpautop($text3)); ?>
        </div>
    <?php endif; ?>

</div>

<?php endwhile;

get_footer();
