<?php

/**
 * Plugin Name: MV Card Block
 * Description: A simple custom Gutenberg "Card" block with title/subtitle/body and optional CTA.
 * Version: 1.1.0
 * Author: Martin
 */

if (! defined('ABSPATH')) exit;

define('MV_CARD_BLOCK_OPT_DEFAULT_ACCENT', 'mv_card_block_default_accent');
define('MV_CARD_BLOCK_DEFAULT_ACCENT_FALLBACK', '#228B22');

/**
 * Admin settings: default accent color.
 */
function mv_card_block_register_settings()
{
    register_setting(
        'mv_card_block_settings',
        MV_CARD_BLOCK_OPT_DEFAULT_ACCENT,
        array(
            'type'              => 'string',
            'sanitize_callback' => function ($v) {
                $v = trim((string) $v);
                if ($v === '') return MV_CARD_BLOCK_DEFAULT_ACCENT_FALLBACK;

                // Allow only #RGB or #RRGGBB
                if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $v)) {
                    return strtoupper($v);
                }
                return MV_CARD_BLOCK_DEFAULT_ACCENT_FALLBACK;
            },
            'default'           => MV_CARD_BLOCK_DEFAULT_ACCENT_FALLBACK,
        )
    );
}
add_action('init', 'mv_card_block_register_settings');

function mv_card_block_add_settings_page()
{
    add_options_page(
        'MV Card Block',
        'MV Card Block',
        'manage_options',
        'mv-card-block',
        'mv_card_block_render_settings_page'
    );
}
add_action('admin_menu', 'mv_card_block_add_settings_page');

function mv_card_block_render_settings_page()
{
    if (! current_user_can('manage_options')) return;

    $val = get_option(MV_CARD_BLOCK_OPT_DEFAULT_ACCENT, MV_CARD_BLOCK_DEFAULT_ACCENT_FALLBACK);
?>
    <div class="wrap">
        <h1>MV Card Block</h1>
        <form method="post" action="options.php">
            <?php settings_fields('mv_card_block_settings'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="mv_card_block_default_accent">Default accent color</label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="mv_card_block_default_accent"
                            name="<?php echo esc_attr(MV_CARD_BLOCK_OPT_DEFAULT_ACCENT); ?>"
                            value="<?php echo esc_attr($val); ?>"
                            class="regular-text"
                            placeholder="#228B22" />
                        <p class="description">
                            Used when a card does not specify its own accent color. Example: <code>#228B22</code>.
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>

        <p>
            In the editor, each MV Card also has an <strong>Accent color (optional)</strong> control in the block sidebar.
            Leaving it blank will use this global default.
        </p>
    </div>
<?php
}

/**
 * Register block + assets.
 */
function mv_card_block_register()
{
    $dir = __DIR__;

    // Shared styles (front-end + editor).
    wp_register_style(
        'mv-card-block-style',
        plugins_url('style.css', __FILE__),
        array(),
        filemtime($dir . '/style.css')
    );

    // Editor-only styles.
    wp_register_style(
        'mv-card-block-editor-style',
        plugins_url('editor.css', __FILE__),
        array('mv-card-block-style'),
        filemtime($dir . '/editor.css')
    );

    // Editor script (no build step).
    wp_register_script(
        'mv-card-block-editor',
        plugins_url('editor.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'),
        filemtime($dir . '/editor.js'),
        true
    );

    // Provide global defaults to editor.
    $default_accent = get_option(MV_CARD_BLOCK_OPT_DEFAULT_ACCENT, MV_CARD_BLOCK_DEFAULT_ACCENT_FALLBACK);
    wp_localize_script(
        'mv-card-block-editor',
        'MVCardDefaults',
        array(
            'defaultAccent' => $default_accent,
            'settingsUrl'   => admin_url('options-general.php?page=mv-card-block'),
        )
    );

    require_once $dir . '/render.php';

    register_block_type(
        $dir . '/block.json',
        array(
            'editor_script'   => 'mv-card-block-editor',
            'editor_style'    => 'mv-card-block-editor-style',
            'style'           => 'mv-card-block-style',
            'render_callback' => 'mv_card_block_render',
        )
    );
}
add_action('init', 'mv_card_block_register');
