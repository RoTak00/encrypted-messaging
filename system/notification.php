<?php

class Notification
{
    public $list = [];
    private $registry = [];
    public function __construct($registry)
    {

        $this->registry = $registry;

        $this->list = json_decode($this->session->data['notification'] ?? '[]', true);
    }

    public function __get($name)
    {
        // Fetch from the registry if it exists
        if (isset($this->registry->registry[$name])) {
            return $this->registry->registry[$name];
        }
        return null; // Or throw an error if you want strict behavior
    }

    public function set($type, $message, $key = null)
    {
        if (!is_null($key)) {
            $this->list[$key] = [
                'type' => $type,
                'message' => $message
            ];
        } else {

            $this->list[] = [
                'type' => $type,
                'message' => $message
            ];
        }

    }

    public function get()
    {
        return $this->list;
    }

    public function clear()
    {
        $this->list = [];
    }

    public function remove($key)
    {

        unset($this->list[$key]);
    }

    public function __destruct()
    {

        $this->session->data['notification'] = json_encode($this->list);
    }
}