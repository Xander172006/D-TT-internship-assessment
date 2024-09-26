<?php

namespace App\Plugins\Http;

class Request {
    /**
     * Get a value from the query parameters.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getQuery($key, $default = null) {
        return $_GET[$key] ?? $default;
    }

    /**
     * Get a value from the form data.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getPost($key, $default = null) {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get a value from the request (either query or form data).
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null) {
        return $this->getQuery($key, $default) ?? $this->getPost($key, $default);
    }

    /**
     * Get a value from the headers.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getHeader($key, $default = null) {
        $key = strtoupper(str_replace('-', '_', $key));
        return $_SERVER['HTTP_' . $key] ?? $default;
    }

    /**
     * Get all query parameters.
     *
     * @return array
     */
    public function allQuery() {
        return $_GET;
    }

    /**
     * Get all form data.
     *
     * @return array
     */
    public function allPost() {
        return $_POST;
    }

    /**
     * Get all request data (query and form data).
     *
     * @return array
     */
    public function all() {
        return array_merge($this->allQuery(), $this->allPost());
    }

    /**
     * Get the request method.
     *
     * @return string
     */
    public function method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get the request URI.
     *
     * @return string
     */
    public function uri() {
        return $_SERVER['REQUEST_URI'];
    }
}