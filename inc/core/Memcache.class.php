<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2014
 */
class Cache {

    protected $conn = NULL;
    private $is_connect = false; //Store status of connection.
    private $srv_address = "";

    /**
     *
     * @param str $servers [ip:port] ext 127.0.0.1:11211
     */
    function __construct($servers) {
        if (!$servers) {
            trigger_error('No memcache servers to connect', E_USER_WARNING);
            exit();
        }
        $this->srv_address = $servers;
    }

    /**
     * Connect to memcached srv
     * @param None
     */
    private function connect() {
        if ($this->srv_address == "") {
            trigger_error('No memcache servers to connect', E_USER_WARNING);
            exit();
        }
        list($ip, $port) = explode(":", $this->srv_address);
        $port = ($port == "") ? 11211 : $port;

        $this->conn = @memcache_pconnect($ip, $port);
        if (!$this->conn) {
            $this->is_connect = false;
            trigger_error('Cannot connect to memcached server ' . $this->srv_address, E_USER_WARNING);
        } else {
            $this->is_connect = true;
        }

        return $this->is_connect;
    }

    /**
     * Store the value in the memcache memory (overwrite if key exists)
     *
     * @param string $key
     * @param mix $value
     * @param bool $compress
     * @param int $expire (seconds before item expires)
     * @return bool
     */
    function set($key, $value, $compress = 0, $expire = 0) {
        if (!$this->is_connect)
            $this->connect();
        return $this->conn->set($key, $value, $compress ? MEMCACHE_COMPRESSED : null, $expire);
    }

    /**
     * Returns the value stored in the memory by it's key
     *
     * @param string $key
     * @return mix
     */
    function get($key) {
        if (!$this->is_connect)
            $this->connect();
        return $this->conn->get($key);
    }

    /**
     * Delete a record or set a timeout
     *
     * @param string $key
     * @param int $timeout
     * @return bool
     */
    function delete($key, $timeout = 0) {
        if (!$this->is_connect)
            $this->connect();
        $status = false;
        for ($i = 0; $i < 5; $i++) {
            if ($this->conn->delete($key, $timeout)) {
                $status = true;
                break;
            } else
                $status = false;
        }
        return $status;
    }

    /**
     * Set the value in memcache if the value does not exist; returns FALSE if value exists
     *
     * @param sting $key
     * @param mix $value
     * @param bool $compress
     * @param int $expire
     * @return bool
     */
    function add($key, $value, $compress = 0, $expire = 0) {
        if (!$this->is_connect)
            $this->connect();
        return $this->conn->add($key, $value, $compress ? MEMCACHE_COMPRESSED : null, $expire);
    }

    /**
     * Replace an existing value
     *
     * @param string $key
     * @param mix $value
     * @param bool $compress
     * @param int $expire
     * @return bool
     */
    function replace($key, $value, $compress = 0, $expire = 0) {
        if (!$this->is_connect)
            $this->connect();
        return $this->conn->replace($key, $value, $compress ? MEMCACHE_COMPRESSED : null, $expire);
    }

    /**
     * Increment an existing integer value
     *
     * @param string $key
     * @param mix $value
     * @return bool
     */
    function increment($key, $value = 1) {
        if (!$this->is_connect)
            $this->connect();
        return $this->conn->increment($key, $value);
    }

    /**
     * Decrement an existing value
     *
     * @param string $key
     * @param mix $value
     * @return bool
     */
    function decrement($key, $value = 1) {
        if (!$this->is_connect)
            $this->connect();
        return $this->conn->decrement($key, $value);
    }

    /**
     * Set lock in memcached
     *
     * @param string $key
     * @param string $func_callback
     * @param int 	 $expire_lock
     * @param int    $lockmax
     * @return bool
     */
    function execute_lock($key, $func_callback, $expire_lock = 5, $lockmax = 8) {
        if (!$this->is_connect)
            $this->connect();
        $now = time(); //time now
        $keylock = "lock." . $key; //key lock
        for ($i = 0; $i < $lockmax; $i++) {
            $lock_status = $this->conn->get($keylock); //get lock content
            if ($lock_status === false || $lock_status[0] == 0 || $lock_status[1] < $now) {
                if ($func_callback != "") {
                    //timeout cache lock
                    $time_lock = $now + $expire_lock;
                    //lock cache
                    $lock_value = array(1, $time_lock);
                    $this->conn->set($keylock, $lock_value);
                    //execute function call back
                    eval("{$func_callback};");
                    //unlock cache
                    $lock_value = array(0, $time_lock);
                    $this->conn->set($keylock, $lock_value);
                    return true;
                } else
                    return false;
            }
            usleep(500000); //time sleep
        }
        return false;
    }

    function flush() {
        if (!$this->is_connect)
            $this->connect();
        $this->conn->flush();
    }

}