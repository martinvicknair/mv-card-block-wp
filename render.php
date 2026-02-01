<?php
if (! defined('ABSPATH')) exit;

if (! function_exists('mv_card_block_render')) {
    function mv_card_block_render($attributes, $content)
    {
        $title       = isset($attributes['title']) ? (string) $attributes['title'] : '';
        $subtitle    = isset($attributes['subtitle']) ? (string) $attributes['subtitle'] : '';
        $body        = isset($attributes['content']) ? (string) $attributes['content'] : '';
        $cta_type    = isset($attributes['ctaType']) ? (string) $attributes['ctaType'] : 'button';
        $cta_text    = isset($attributes['ctaText']) ? (string) $attributes['ctaText'] : '';
        $cta_url     = isset($attributes['ctaUrl']) ? (string) $attributes['ctaUrl'] : '';
        $cta_new_tab = ! empty($attributes['ctaNewTab']);
        $accent      = isset($attributes['accentColor']) ? trim((string) $attributes['accentColor']) : '';

        $default_accent   = get_option('mv_card_block_default_accent', '#228B22');
        $effective_accent = $accent !== '' ? $accent : $default_accent;

        $wrapper_attrs = get_block_wrapper_attributes(array(
            'class' => 'mv-card',
            'style' => $effective_accent ? '--mv-card-accent:' . esc_attr($effective_accent) . ';' : '',
        ));

        $cta_html = '';
        if ($cta_type !== 'none' && $cta_url !== '') {
            $target = $cta_new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';
            $label  = $cta_text !== '' ? $cta_text : 'View details';

            if ($cta_type === 'button') {
                $cta_html = sprintf(
                    '<a class="mv-card__cta-btn" href="%s"%s>%s</a>',
                    esc_url($cta_url),
                    $target,
                    esc_html($label)
                );
            } elseif ($cta_type === 'texticon') {
                $cta_html = sprintf(
                    '<a class="mv-card__cta-link" href="%s"%s>%s <span class="mv-card__icon" aria-hidden="true">🔗</span></a>',
                    esc_url($cta_url),
                    $target,
                    esc_html($label)
                );
            } else { // text
                $cta_html = sprintf(
                    '<a class="mv-card__cta-link" href="%s"%s>%s</a>',
                    esc_url($cta_url),
                    $target,
                    esc_html($label)
                );
            }
        }

        ob_start();
?>
        <div <?php echo $wrapper_attrs; ?>>
            <div class="mv-card__caption">
                <?php if ($title !== '') : ?>
                    <div class="mv-card__title"><?php echo esc_html($title); ?></div>
                <?php endif; ?>

                <?php if ($subtitle !== '') : ?>
                    <div class="mv-card__subtitle"><?php echo esc_html($subtitle); ?></div>
                <?php endif; ?>
            </div>

            <div class="mv-card__body">
                <?php echo wp_kses_post($body); ?>
            </div>

            <?php if ($cta_html !== '') : ?>
                <div class="mv-card__cta"><?php echo $cta_html; ?></div>
            <?php endif; ?>
        </div>
<?php
        return (string) ob_get_clean();
    }
}
