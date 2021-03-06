<?php
namespace Ldrt\Blog;
use DateTime;

class Post {

    public $id;
    public $name;
    public $content;
    public $created_at;

    public function __construct()
    {
        // Thanks to PDO::FETCH_CLASS, 'Post', properties are givent values from DB
        if (is_int($this->created_at) || is_string($this->created_at)) {
            $this->created_at = new DateTime('@' . $this->created_at);
        }
    }

    public function getExcerpt() : string
    {
        return substr($this->content, 0, 150);
    }
}

?>