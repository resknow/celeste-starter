<?php

use Celeste\BlockEditor\Blocks;

/**
 * Render ACF Block
 *
 * @param array $block The block data
 * @param string $content The block content
 * @param bool $is_preview If the block is being previewed
 * @param int $post_id The post ID
 * @return void
 */
function render_acf_block(array $block, string $content = '', bool $is_preview = false, int $post_id = 0) {
    Blocks::render($block, $content, $is_preview, $post_id);
}