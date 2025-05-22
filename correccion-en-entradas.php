<?php
/*
Plugin Name: Correcciones de Entradas
Plugin URI: https://github.com/elambmex/plugin-correciones-notas
Description: Plugin personalizado de El Ambientalista Post para informar a nuestros lectores sobre correcciones realizadas en nuestros artículos.
Version: 1.2.0
Author: El Ambientalista Post
Author URI: https://elambmex.com
GitHub Plugin URI: https://github.com/elambmex/plugin-correciones-notas
Primary Branch: main
*/

// Agrega el campo personalizado al editor de entradas
function cep_agregar_meta_box() {
    add_meta_box(
        'cep_correccion_meta_box',
        'Nota de Corrección',
        'cep_mostrar_meta_box',
        'post',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'cep_agregar_meta_box');

// Mostrar la caja del campo
function cep_mostrar_meta_box($post) {
    $correccion = get_post_meta($post->ID, '_cep_correccion', true);
    echo '<textarea style="width:100%; min-height:100px;" name="cep_correccion">'.esc_textarea($correccion).'</textarea>';
}

// Guardar el contenido del campo
function cep_guardar_correccion($post_id) {
    if (array_key_exists('cep_correccion', $_POST)) {
        update_post_meta(
            $post_id,
            '_cep_correccion',
            sanitize_textarea_field($_POST['cep_correccion'])
        );
        update_post_meta(
            $post_id,
            '_cep_correccion_fecha',
            current_time('Y-m-d')
        );
    }
}
add_action('save_post', 'cep_guardar_correccion');

// Mostrar la corrección al final del contenido del post
function cep_mostrar_correccion_en_contenido($content) {
    if (is_singular('post')) {
        $correccion = get_post_meta(get_the_ID(), '_cep_correccion', true);
        $fecha = get_post_meta(get_the_ID(), '_cep_correccion_fecha', true);
        if (!empty($correccion)) {
            $fecha_formateada = date_i18n("j \d\e F \d\e Y", strtotime($fecha));
            $correccion_html = '<div class="correccion-box"><strong>Corrección (' . esc_html($fecha_formateada) . '):</strong> ' . esc_html($correccion) . '</div>';
            $content .= $correccion_html;
        }
    }
    return $content;
}
add_filter('the_content', 'cep_mostrar_correccion_en_contenido');

// Agregar estilo CSS en línea
function cep_estilos_inline() {
    echo '<style>
    .correccion-box {
        border-left: 4px solid #e74c3c;
        background-color: #fff3f3;
        padding: 12px 16px;
        margin-top: 30px;
        font-size: 0.95em;
    }
    </style>';
}
add_action('wp_head', 'cep_estilos_inline');
?>
