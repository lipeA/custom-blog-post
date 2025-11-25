<?php
/*
Plugin Name: Custom Blog Post
Description: Plugin para gerenciar posts personalizados com imagens e textos.
Version: 1.5
Author: Felipe Assis de Jesus
*/

/* =========================
   REGISTRA O POST TYPE
   ========================= */
function cbp_register_post_type() {
    $labels = array(
        'name' => 'Blog',
        'singular_name' => 'Post',
        'add_new' => 'Adicionar novo',
        'all_items' => 'Todos os posts',
        'add_new_item' => 'Adicionar novo post',
        'edit_item' => 'Editar post',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-edit',
        'supports' => array('title', 'thumbnail'),
        'has_archive' => true,
        'rewrite' => array('slug' => 'blog'),
    );

    register_post_type('cbp_post', $args);
}
add_action('init', 'cbp_register_post_type');


/* =========================
   META BOX CUSTOM FORM
   ========================= */
function cbp_add_meta_box() {
    add_meta_box('cbp_box', 'Conteúdo do post', 'cbp_render_meta_box', 'cbp_post', 'normal', 'high');
}
add_action('add_meta_boxes', 'cbp_add_meta_box');

function cbp_render_meta_box($post){
    wp_nonce_field('cbp_meta_nonce_action', 'cbp_meta_nonce');

    $cbp_date  = get_post_meta($post->ID, 'cbp_date', true);
    $cbp_category = get_post_meta($post->ID, 'cbp_category', true);

    $cbp_text1 = get_post_meta($post->ID, 'cbp_text1', true);
    $cbp_img1  = get_post_meta($post->ID, 'cbp_img1', true);

    $cbp_text2 = get_post_meta($post->ID, 'cbp_text2', true);
    $cbp_img2  = get_post_meta($post->ID, 'cbp_img2', true);

    $cbp_text3 = get_post_meta($post->ID, 'cbp_text3', true);
?>
    <p>
        <label>Categoria:</label><br>
        <input type="text" name="cbp_category" style="width:100%;" value="<?php echo esc_attr($cbp_category); ?>">
    </p>

    <p>
        <label>Data (usada para ordenação):</label><br>
        <input type="date" name="cbp_date" style="width:200px;" value="<?php echo esc_attr($cbp_date); ?>" required>
    </p>

    <hr>
    <h3>Bloco 1</h3>
    <p><label>Primeiro texto:</label><br>
    <textarea name="cbp_text1" style="width:100%;height:120px;"><?php echo esc_textarea($cbp_text1); ?></textarea></p>

    <p>
        <label>Imagem 1 no meio:</label><br>
        <input type="hidden" id="cbp_img1" name="cbp_img1" value="<?php echo esc_attr($cbp_img1); ?>">
        <button type="button" class="button cbp_upload" data-target="cbp_img1" data-preview="cbp_preview1">Selecionar imagem</button>
        <div id="cbp_preview1" style="margin-top:10px;">
            <?php if($cbp_img1): ?>
                <img src="<?php echo esc_url($cbp_img1); ?>" style="width:200px;height:auto;border:1px solid #ddd;">
            <?php endif; ?>
        </div>
    </p>

    <hr>
    <h3>Bloco 2</h3>
    <p><label>Segundo texto:</label><br>
    <textarea name="cbp_text2" style="width:100%;height:120px;"><?php echo esc_textarea($cbp_text2); ?></textarea></p>

    <p>
        <label>Imagem 2 no meio:</label><br>
        <input type="hidden" id="cbp_img2" name="cbp_img2" value="<?php echo esc_attr($cbp_img2); ?>">
        <button type="button" class="button cbp_upload" data-target="cbp_img2" data-preview="cbp_preview2">Selecionar imagem</button>
        <div id="cbp_preview2" style="margin-top:10px;">
            <?php if($cbp_img2): ?>
                <img src="<?php echo esc_url($cbp_img2); ?>" style="width:200px;height:auto;border:1px solid #ddd;">
            <?php endif; ?>
        </div>
    </p>

    <hr>
    <h3>Bloco 3</h3>
    <p><label>Terceiro texto:</label><br>
    <textarea name="cbp_text3" style="width:100%;height:120px;"><?php echo esc_textarea($cbp_text3); ?></textarea></p>

<?php
}


/* =========================
   SALVAR OS DADOS
   ========================= */
function cbp_save_data($post_id){
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['cbp_meta_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['cbp_meta_nonce'], 'cbp_meta_nonce_action' ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    if ( get_post_type( $post_id ) !== 'cbp_post' ) return;

    update_post_meta($post_id, 'cbp_date', sanitize_text_field($_POST['cbp_date']));
    update_post_meta($post_id, 'cbp_category', sanitize_text_field($_POST['cbp_category']));
    update_post_meta($post_id, 'cbp_text1', wp_kses_post($_POST['cbp_text1']));
    update_post_meta($post_id, 'cbp_img1', esc_url_raw($_POST['cbp_img1']));
    update_post_meta($post_id, 'cbp_text2', wp_kses_post($_POST['cbp_text2']));
    update_post_meta($post_id, 'cbp_img2', esc_url_raw($_POST['cbp_img2']));
    update_post_meta($post_id, 'cbp_text3', wp_kses_post($_POST['cbp_text3']));
}
add_action('save_post', 'cbp_save_data');


/* =========================
   HABILITA A MEDIA LIBRARY
   ========================= */
function cbp_admin_enqueue(){
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'cbp_admin_enqueue');


/* =========================
   JS UPLOAD IMAGENS
   ========================= */
function cbp_media_script(){
    global $post;
    if(isset($post) && $post->post_type==='cbp_post'):
?>
<script>
jQuery(document).ready(function($){
    $(".cbp_upload").on("click", function(e){
        e.preventDefault();

        let button = $(this);
        let target = $("#" + button.data("target"));
        let preview = $("#" + button.data("preview"));

        if (button.data('frame')) {
            button.data('frame').open();
            return;
        }

        let frame = wp.media({
            title: "Selecionar imagem",
            button: { text: "Usar imagem" },
            multiple: false
        });

        frame.on("select", function(){
            let attachment = frame.state().get("selection").first().toJSON();
            target.val(attachment.url);
            preview.html(`<img src="${attachment.url}" style="width:200px;height:auto;border:1px solid #ddd;">`);
        });

        button.data('frame', frame);
        frame.open();
    });
});
</script>
<?php
    endif;
}
add_action('admin_footer', 'cbp_media_script');


/* =========================
   SHORTCODE: BLOG GRID
   ========================= */
function cbp_generate_blog_grid($atts = []) {
    $atts = shortcode_atts(array(
        'posts_per_page' => -1,
    ), $atts, 'blog_grid');

    // CONFIGURAÇÃO DA ORDENAÇÃO POR DATA CUSTOMIZADA
    $args = array(
        'post_type'      => 'cbp_post',
        'posts_per_page' => $atts['posts_per_page'],
        'post_status'    => 'publish',
        'meta_key'       => 'cbp_date',   // 1. Nome do campo no banco
        'orderby'        => 'meta_value', // 2. Ordenar pelo valor desse campo
        'order'          => 'DESC',       // 3. Do maior para o menor (mais recente primeiro)
    );

    $query = new WP_Query($args);
    $placeholder = plugins_url('placeholder.png', __FILE__);

    ob_start();
    ?>
    <style>
    .cbp-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;}
    .cbp-card{background:#fff;border:1px solid #eee;border-radius:8px;overflow:hidden;transition:.15s;text-decoration:none;color:#111;display:block;}
    .cbp-card:hover{transform:translateY(-3px);box-shadow:0 4px 12px rgba(0,0,0,0.06);}
    .cbp-card img{width:100%;height:200px;object-fit:cover;display:block;}
    .cbp-card .inner{padding:14px;}
    .cbp-card .title{font-size:16px;font-weight:600;margin-bottom:8px;color:#111;}
    .cbp-card .meta{font-size:13px;color:#666;}
    </style>
    <div class="cbp-grid">
    <?php

    if($query->have_posts()){
        while($query->have_posts()){
            $query->the_post();
            
            $post_id = get_the_ID();
            
            // Dados padrão
            $title = get_the_title();
            $link = get_permalink();
            
            // Imagem
            $thumb = get_the_post_thumbnail_url($post_id, 'medium');
            $thumb = $thumb ? $thumb : $placeholder;
            
            // Dados Customizados
            $cat = get_post_meta($post_id, 'cbp_category', true);
            
            // Formatar a data para o padrão brasileiro (opcional)
            $date_raw = get_post_meta($post_id, 'cbp_date', true); 
            // Se tiver data, formata. Se não, deixa vazio.
            $date_display = $date_raw ? date('d/m/Y', strtotime($date_raw)) : '';

            echo '<a class="cbp-card" href="'.esc_url($link).'">';
            echo '<img src="'.esc_url($thumb).'" alt="'.esc_attr($title).'">';
            echo '<div class="inner">';
            echo '<div class="title">'.esc_html($title).'</div>';
            echo '<div class="meta">'.esc_html($date_display).' — '.esc_html($cat).'</div>';
            echo '</div>';
            echo '</a>';
        }
        wp_reset_postdata(); // Sempre resetar após usar WP_Query
    } else {
        echo '<div style="grid-column:1/-1;padding:20px;">Nenhum post encontrado.</div>';
    }

    echo '</div>';
    return ob_get_clean();
}
add_shortcode('blog_grid', 'cbp_generate_blog_grid');
add_shortcode('blog-grid', 'cbp_generate_blog_grid');


/* =========================
   SHORTCODE: ÚLTIMOS 5 POSTS (EM CARDS)
   ========================= */
function cbp_recent_posts($atts = []) {
    
    // 1. Configura a query para usar a DATA PERSONALIZADA
    $args = array(
        'post_type'      => 'cbp_post',
        'posts_per_page' => 5,            // Mostra os 5 últimos
        'post_status'    => 'publish',
        'meta_key'       => 'cbp_date',   // Ordena pelo campo de data customizado
        'orderby'        => 'meta_value', 
        'order'          => 'DESC',       // Do mais recente para o mais antigo
    );

    $query = new WP_Query($args);
    
    // Define o placeholder caso não tenha imagem
    $placeholder = plugins_url('placeholder.png', __FILE__); 

    ob_start();
    ?>
    
    <style>
    .cbp-recent-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Cards responsivos */
        gap: 15px;
    }
    .cbp-recent-card {
        background: #fff;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .cbp-recent-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .cbp-recent-card img {
        width: 100%;
        height: 140px; /* Altura fixa para a imagem ficar uniforme */
        object-fit: cover;
        display: block;
    }
    .cbp-recent-card .content {
        padding: 12px;
    }
    .cbp-recent-card .title {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 6px;
        line-height: 1.3;
        color: #222;
    }
    .cbp-recent-card .date {
        font-size: 12px;
        color: #777;
        margin-top: auto;
    }
    </style>

    <div class="cbp-recent-grid">
    <?php

    if($query->have_posts()){
        while($query->have_posts()){
            $query->the_post();
            
            $post_id = get_the_ID();
            $title   = get_the_title();
            $link    = get_permalink();
            
            // Pega a imagem de destaque ou o placeholder
            $img_url = get_the_post_thumbnail_url($post_id, 'medium');
            $img_url = $img_url ? $img_url : $placeholder;

            // Pega e formata a data personalizada
            $date_raw = get_post_meta($post_id, 'cbp_date', true);
            $date_display = $date_raw ? date('d/m/Y', strtotime($date_raw)) : '';

            // 3. Monta o Card
            echo '<a href="'.esc_url($link).'" class="cbp-recent-card">';
            
            // Imagem
            echo '<img src="'.esc_url($img_url).'" alt="'.esc_attr($title).'">';
            
            // Conteúdo (Título + Data)
            echo '<div class="content">';
            echo '<div class="title">'.esc_html($title).'</div>';
            if($date_display) {
                echo '<div class="date"><span class="dashicons dashicons-calendar-alt" style="font-size:14px;line-height:1;margin-right:4px;"></span> '.esc_html($date_display).'</div>';
            }
            echo '</div>'; // .content
            
            echo '</a>'; // .cbp-recent-card
        }
        wp_reset_postdata();
    } else {
        echo '<div style="grid-column:1/-1; padding:15px; border:1px dashed #ccc;">Nenhum post recente encontrado.</div>';
    }

    echo '</div>'; // .cbp-recent-grid
    return ob_get_clean();
}
add_shortcode('ultimos_posts', 'cbp_recent_posts');