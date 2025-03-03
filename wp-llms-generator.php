<?php
/**
 * Plugin Name: WP LLMs.txt Generator
 * Plugin URI: https://github.com/yourusername/wp-llms-generator
 * Description: WordPress siteler için LLMs.txt dosyası oluşturucu. İçerik türlerini seçerek özelleştirilebilir.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-llms-generator
 * Domain Path: /languages
 *
 * @package WPLLMsGenerator
 */

// Güvenlik kontrolü
if (!defined('ABSPATH')) {
    exit;
}

// Eklenti sınıfı
class WP_LLMs_Generator {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('init', array($this, 'generate_llms_txt'));
        
        // Yeni içerik eklendiğinde veya güncellendiğinde LLMs.txt'yi güncelle
        add_action('save_post', array($this, 'auto_update_llms_txt'), 10, 3);
        add_action('deleted_post', array($this, 'auto_update_llms_txt'));
        add_action('publish_post', array($this, 'auto_update_llms_txt'));
        add_action('publish_page', array($this, 'auto_update_llms_txt'));
        add_action('publish_product', array($this, 'auto_update_llms_txt'));
        add_action('add_attachment', array($this, 'auto_update_llms_txt'));
        add_action('edit_attachment', array($this, 'auto_update_llms_txt'));
        add_action('delete_attachment', array($this, 'auto_update_llms_txt'));
    }
    
    public function add_admin_menu() {
        add_options_page(
            __('LLMs.txt Generator', 'wp-llms-generator'),
            __('LLMs.txt Generator', 'wp-llms-generator'),
            'manage_options',
            'wp-llms-generator',
            array($this, 'admin_page')
        );
    }
    
    public function register_settings() {
        register_setting('wp_llms_generator_options', 'wp_llms_content_types');
        
        add_settings_section(
            'wp_llms_generator_section',
            __('İçerik Türü Ayarları', 'wp-llms-generator'),
            array($this, 'settings_section_callback'),
            'wp-llms-generator'
        );
        
        $content_types = array(
            'post' => __('Yazılar', 'wp-llms-generator'),
            'page' => __('Sayfalar', 'wp-llms-generator'),
            'product' => __('Ürünler', 'wp-llms-generator'),
            'attachment' => __('Medya', 'wp-llms-generator')
        );
        
        foreach ($content_types as $type => $label) {
            add_settings_field(
                'wp_llms_' . $type,
                $label,
                array($this, 'checkbox_callback'),
                'wp-llms-generator',
                'wp_llms_generator_section',
                array('type' => $type)
            );
        }
    }
    
    public function settings_section_callback() {
        echo '<p>' . __('LLMs.txt dosyasına dahil edilecek içerik türlerini seçin:', 'wp-llms-generator') . '</p>';
    }
    
    public function checkbox_callback($args) {
        $options = get_option('wp_llms_content_types', array());
        $checked = isset($options[$args['type']]) ? 'checked' : '';
        echo '<input type="checkbox" name="wp_llms_content_types[' . esc_attr($args['type']) . ']" ' . $checked . ' />';
    }
    
    public function admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('wp_llms_generator_options');
                do_settings_sections('wp-llms-generator');
                submit_button(__('Ayarları Kaydet', 'wp-llms-generator'));
                ?>
            </form>
            <hr>
            <h2><?php _e('LLMs.txt Dosyası Oluştur', 'wp-llms-generator'); ?></h2>
            <form method="post" action="">
                <?php wp_nonce_field('generate_llms_txt_action', 'generate_llms_txt_nonce'); ?>
                <input type="submit" name="generate_llms" class="button button-primary" value="<?php _e('LLMs.txt Oluştur', 'wp-llms-generator'); ?>">
            </form>
        </div>
        <?php
    }
    
    public function generate_llms_txt() {
        if (!isset($_POST['generate_llms']) || !isset($_POST['generate_llms_txt_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['generate_llms_txt_nonce'], 'generate_llms_txt_action')) {
            wp_die(__('Güvenlik doğrulaması başarısız oldu.', 'wp-llms-generator'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Bu işlemi gerçekleştirmek için yetkiniz yok.', 'wp-llms-generator'));
        }

        $this->update_llms_txt();
    }
    
    private function update_llms_txt() {
        $options = get_option('wp_llms_content_types', array());
        $site_name = html_entity_decode(get_bloginfo('name'), ENT_QUOTES, 'UTF-8');
        
        // Başlık ve açıklama
        $content = "# " . $site_name . "\n\n";
        $content .= "> " . html_entity_decode(get_bloginfo('description'), ENT_QUOTES, 'UTF-8') . "\n\n";
        $content .= get_bloginfo('url') . "\n\n";

        foreach ($options as $type => $enabled) {
            if ($enabled) {
                $args = array(
                    'post_type' => $type,
                    'posts_per_page' => -1,
                    'post_status' => 'publish'
                );
                
                $query = new WP_Query($args);
                
                if ($query->have_posts()) {
                    // Her içerik türü için bir bölüm oluştur
                    $content .= "## " . ucfirst($type) . "\n\n";
                    
                    while ($query->have_posts()) {
                        $query->the_post();
                        $title = html_entity_decode(get_the_title(), ENT_QUOTES, 'UTF-8');
                        $excerpt = wp_strip_all_tags(get_the_excerpt());
                        $excerpt = html_entity_decode($excerpt, ENT_QUOTES, 'UTF-8');
                        $excerpt = mb_substr($excerpt, 0, 150, 'UTF-8') . (mb_strlen($excerpt, 'UTF-8') > 150 ? '...' : '');
                        
                        // Her içerik için başlık ve URL formatı
                        $content .= "- [" . $title . "](" . get_permalink() . "): " . $excerpt . "\n";
                    }
                    $content .= "\n";
                }
                
                wp_reset_postdata();
            }
        }

        // Son güncelleme bilgisi
        $content .= "## Additional Information\n\n";
        $content .= "- Last Updated: " . current_time('Y-m-d H:i:s') . "\n";
        $content .= "- Generated by: WP LLMs.txt Generator\n";

        // WordPress ana dizinini al
        $file_path = ABSPATH . 'llms.txt';
        
        // Dosya yazma izinlerini kontrol et
        if (!is_writable(ABSPATH)) {
            add_action('admin_notices', array($this, 'permission_error_notice'));
            return false;
        }
        
        // UTF-8 BOM ekle
        $content = "\xEF\xBB\xBF" . $content;
        
        if (file_put_contents($file_path, $content)) {
            add_action('admin_notices', array($this, 'success_notice'));
            return true;
        } else {
            add_action('admin_notices', array($this, 'error_notice'));
            return false;
        }
    }
    
    public function success_notice() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php 
            echo sprintf(
                __('LLMs.txt dosyası başarıyla oluşturuldu: %s', 'wp-llms-generator'),
                esc_html(site_url('/llms.txt'))
            ); 
            ?></p>
        </div>
        <?php
    }
    
    public function error_notice() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e('LLMs.txt dosyası oluşturulurken bir hata oluştu.', 'wp-llms-generator'); ?></p>
        </div>
        <?php
    }

    public function permission_error_notice() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e('WordPress ana dizinine yazma izni yok. Lütfen dizin izinlerini kontrol edin.', 'wp-llms-generator'); ?></p>
        </div>
        <?php
    }

    public function auto_update_llms_txt($post_id = null, $post = null, $update = null) {
        // İçerik türü ayarlarını kontrol et
        $options = get_option('wp_llms_content_types', array());
        
        // Eğer post_id verilmişse, bu içerik türünün LLMs.txt'de olup olmadığını kontrol et
        if ($post_id) {
            $post_type = get_post_type($post_id);
            if (!isset($options[$post_type]) || !$options[$post_type]) {
                return;
            }
        }
        
        $this->update_llms_txt();
    }
}

// Eklentiyi başlat
function wp_llms_generator_init() {
    WP_LLMs_Generator::get_instance();
}
add_action('plugins_loaded', 'wp_llms_generator_init'); 