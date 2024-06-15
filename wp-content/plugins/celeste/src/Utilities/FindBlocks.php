<?php

namespace Celeste\Utilities;

class FindBlocks {

    private bool $has_parsed_template_blocks = false;
    private string $current_block_template_id;

    public function __construct( private string $post_content ) {
        global $_wp_current_template_id;
        $this->current_block_template_id = $_wp_current_template_id;
    }

    public function parse() {
        return parse_blocks( $this->post_content );
    }

    public function find( array $blocks ) {

        // If block template is being used, parse those blocks too
        if ( $this->is_using_block_template() && ! $this->has_parsed_template_blocks ) {
            $template_blocks = parse_blocks( $this->get_block_template_content() );

            if ( is_array( $template_blocks ) ) {
                foreach( $template_blocks as $block ) {
                    $blocks[] = $block;
                }
            }

            /**
             * !Important
             *
             * Prevents infinite loop when block template is used
             */
            $this->has_parsed_template_blocks = true;
        }

        $filtered_blocks = $this->find_block_names( $blocks );

        // Only return blocks with acf/ prefix
        if ( ! empty($filtered_blocks) ) {
            $filtered_blocks = array_filter( $filtered_blocks, function( $block ) {
                return ! empty($block) && !is_null($block) && strpos( $block, 'acf/' ) !== false;
            } );
        }

        $found_blocks = array_unique( array_filter( $filtered_blocks ) );
        $found_components = $this->find_components_inside_blocks( $found_blocks );

        return [ 'blocks' => $found_blocks, 'components' => $found_components ];
    }

    private function is_using_block_template() {
        return $this->current_block_template_id !== null;
    }

    private function get_block_template_content() {
        return get_block_template( $this->current_block_template_id )->content;
    }

    private function find_block_names( array $blocks ) {

        if ( ! is_array( $blocks ) || empty( $blocks ) ) {
            return [];
        }

        $block_names = [];

        foreach ( $blocks as $block ) {
            $block_names[] = $block['blockName'];

            // Search innerBlocks
            if ( array_key_exists( 'innerBlocks', $block ) && is_array( $block['innerBlocks'] ) ) {
                $block_names = array_merge( $block_names, $this->find_block_names( $block['innerBlocks'] ) );
            }
        }

        // Remove empty values & duplicates
        return $block_names;
    }

    private function find_components_inside_blocks( array $block_names ) {
        $found_components = [];

        if ( empty( $block_names ) ) {
            return [];
        }

        foreach( $block_names as $block_name ) {
            $template = sprintf( 'blocks/%s/template.twig', str_replace( 'acf/', '', $block_name ) );
            $find_components = new FindComponents();
            $found_components = array_merge( $found_components, $find_components->find( $template ) );
        }

        return array_unique( $found_components );
    }

}