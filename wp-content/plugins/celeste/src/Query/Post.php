<?php

namespace Celeste\Query;

use Celeste\Models\Post as PostModel;
use WP_Post;
use WP_Query;

class Post {

    /**
     * @var Post|null
     */
    private static $instance = null;

    /**
     * Whether to get a single post
     * @var bool
     */
    private bool $is_single = false;

    /**
     * Post ID
     *
     * @var int
     */
    private int $post_id = 0;

    /**
     * Args to pass along to get_posts
     * @var array
     */
    private array $args = [];

    /**
     * Taxonomy to use for tags
     * @var string
     */
    private string $tag_taxonomy = '';

    /**
     * Taxonomy to use for categories
     * @var string
     */
    private string $category_taxonomy = '';

    /**
     * Whether to include Advanced Custom Fields
     * @var bool
     */
    private bool $with_fields = false;

    /**
     * Fields to get from Advanced Custom Fields
     * @var array|null
     */
    private array|null $fields = null;

    /**
     * Whether to include Post Meta
     * @var bool
     */
    private bool $with_meta = false;

    /**
     * Whether to include the featured image
     * @var bool
     */
    private bool $with_featured_image = false;

    /**
     * Whether to include the excerpt
     * @var bool
     */
    private bool $with_excerpt = false;

    /**
     * Whether to include tags
     * @var bool
     */
    private bool $with_tags = false;

    /**
     * Whether to include categories
     * @var bool
     */
    private bool $with_categories = false;

    /**
     * Each Callback
     * @var callable|null
     */
    private $each_callback = null;

    /**
     * Post constructor.
     */
    public static function query() {
        self::$instance = new self;
        return self::$instance;
    }

    /**
     * Set the post ID
     */
    public static function id( int $post_id ) {
        $instance = new self;
        $instance->is_single = true;
        $instance->post_id = $post_id;
        return $instance;
    }

    /**
     * IDs
     *
     * Return only post IDs
     */
    public function ids() {
        $this->args['fields'] = 'ids';
        return $this;
    }

    /**
     * Set the post type
     */
    public function type( string $post_type ) {
        $this->args['post_type'] = $post_type;
        return $this;
    }

    /**
     * Set the post author
     */
    public function author( int $author_id ) {
        $this->args['author'] = $author_id;
        return $this;
    }

    /**
     * Set the post status
     */
    public function status( string $status ) {
        $this->args['post_status'] = $status;
        return $this;
    }

    /**
     * Set the post limit
     */
    public function limit( int $limit ) {
        $this->args['posts_per_page'] = $limit;
        return $this;
    }

    /**
     * Set the post offset
     */
    public function offset( int $offset ) {
        $this->args['offset'] = $offset;
        return $this;
    }

    /**
     * Set the post IDs to include
     */
    public function include( array $ids ) {
        $this->args['post__in'] = $ids;
        return $this;
    }

    /**
     * Set the post IDs to exclude
     */
    public function exclude( array $ids ) {
        $this->args['post__not_in'] = $ids;
        return $this;
    }

    /**
     * Set the post order
     */
    public function order( string $order ) {
        $this->args['order'] = strtoupper($order);
        return $this;
    }

    /**
     * Set the post order by
     */
    public function order_by( string $order_by ) {
        $this->args['orderby'] = $order_by;
        return $this;
    }

    /**
     * Set a meta query array
     */
    public function meta_query( array $meta_query ) {
        $this->args['meta_query'] = $meta_query;
        return $this;
    }

    /**
     * Set a tax query array
     */
    public function tax_query( array $tax_query ) {
        $this->args['tax_query'] = $tax_query;
        return $this;
    }

    /**
     * Set a meta key and value to query
     *
     * @param string $key The meta key to query
     * @param mixed $compare_or_value The compare operator or the value
     * @param mixed $value The value to compare if setting a compare operator
     */
    public function where_meta( string $key, $compare_or_value, $value ) {

        /**
         * If only 2 arguments are passed, the second argument is the value
         * and the compare is assumed to be '='
         */
        func_num_args() === 2
            ? $value = $compare_or_value
            : $compare = $compare_or_value;

        $this->args['meta_key'] = $key;
        $this->args['meta_value'] = $value;
        $this->args['meta_compare'] = isset($compare) ? $compare : '=';

        return $this;
    }

    /**
     * Belongs To
     *
     * Get posts that belong to a parent post
     */
    public function belongs_to( int $parent_id ) {
        $this->args['post_parent'] = $parent_id;
        return $this;
    }

    /**
     * Include tags for each post
     */
    public function with_tags( string $taxonomy = 'post_tag' ) {
        $this->tag_taxonomy = $taxonomy;
        $this->with_tags = true;
        return $this;
    }

    /**
     * Include categories for each post
     */
    public function with_categories( string $taxonomy = 'category' ) {
        $this->category_taxonomy = $taxonomy;
        $this->with_categories = true;
        return $this;
    }

    /**
     * Include ACF fields for each post
     *
     * @param array|null $fields The field keys or names to include, or null to include all fields
     */
    public function with_fields( array|null $fields = null ) {
        $this->fields = $fields;
        $this->with_fields = true;
        return $this;
    }

    /**
     * Include Post Meta for each post
     */
    public function with_meta() {
        $this->with_meta = true;
        return $this;
    }

    /**
     * Include the featured image for each post
     */
    public function with_featured_image() {
        $this->with_featured_image = true;
        return $this;
    }

    /**
     * Include the excerpt for each post
     *
     * Will generate an excerpt if one is not set
     */
    public function with_excerpt() {
        $this->with_excerpt = true;
        return $this;
    }

    /**
     * Each
     *
     * Applies a user defined callback to each post
     *
     * @param callable $callback
     * @return void
     */
    public function each( callable $callback ) {
        $this->each_callback = $callback;
        return $this;
    }

    /**
     * Get the posts
     *
     * @return WP_Post[]|null
     */
    public function get() {

        /**
         * Query for a single post
         */
        if ( $this->is_single ) {

            /**
             * If query args are set, we will warn the user that they have no effect
             */
            if ( ! empty($this->args) ) {
                _doing_it_wrong( __METHOD__, 'Query args have no effect when querying for a post by ID', '1.0.0' );
            }

            $post = WP_Post::get_instance($this->post_id);
            return $post instanceof WP_Post
                ? $this->prepare_post($post)
                : null;
        }

        $query = new WP_Query;
        $posts = $query->query($this->args);

        if ( ! $posts ) return null;

        /**
         * So long as we're not querying for post IDs, we will prepare each post
         */
        if ( ! isset( $this->args['fields'] ) ) {
            $posts = array_map( function($post) {
                return $this->prepare_post($post);
            }, $posts );
        }

        return $posts;
    }

    /**
     * Prepare the post object
     *
     * Here we prepare each post object by adding additional data
     * set in the query.
     *
     * This method also sets some convenience properties on the post object
     * including applying `the_content` filter and setting the permalink.
     *
     * @param WP_Post $post
     * @return WP_Post
     */
    private function prepare_post( WP_Post $post ): PostModel {
        $post = new PostModel($post);

        $post->content_raw = $post->content;
        $post->content = apply_filters( 'the_content', $post->content );

        $post->permalink = get_permalink($post->ID);

        if ( $this->with_meta ) {
            $post->meta = get_post_meta($post->ID);
        }

        if ( $this->with_fields ) {
            $post->fields = $this->prepare_fields($post->ID);
        }

        if ( $this->with_featured_image ) {
            $post->featured_image = get_featured_image($post->ID);
        }

        if ( $this->with_excerpt && empty($post->exerpt) ) {
            $post->excerpt = create_post_excerpt($post->content);
        }

        if ( $this->with_tags ) {
            $post->tags = get_the_terms($post->ID, $this->tag_taxonomy);
        }

        if ( $this->with_categories ) {
            $post->categories = get_the_terms($post->ID, $this->category_taxonomy);
        }

        /**
         * Apply the each callback if set
         */
        if ( is_callable( $this->each_callback ) ) {
            $post = call_user_func($this->each_callback, $post);
        }

        return $post;
    }

    /**
     * Get the Advanced Custom Fields for a post
     *
     * @param int $post_id
     * @return array|null
     */
    private function prepare_fields( int $post_id ) {

        if ( ! function_exists('acf') ) {
            _doing_it_wrong( __METHOD__, 'Advanced Custom Fields is not installed or activated', '1.0.0' );
            return null;
        }

        /**
         * If no fields keys/names are set, we will get all fields
         */
        if ( ! $this->fields ) {
            $fields = get_fields($post_id);
            return is_array($fields) ? $fields : null;
        }

        $fields = [];

        foreach ( $this->fields as $field ) {
            $fields[ $field ] = get_field($field, $post_id);
        }

        return ! empty($fields) ? $fields : null;
    }

}