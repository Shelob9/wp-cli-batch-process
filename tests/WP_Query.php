<?php

class WP_Query {
    public $found_posts = 0;
	public $max_num_pages = 0;
	public $post_count = 0;
    /** array */
    public $query;
    public function get_posts(){
        return [];
    }
    public function parse_query(){}
}