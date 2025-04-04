<?php

class Setting
{

    private $registry = [];

    private $data;
    public function __construct($registry)
    {
        $this->registry = $registry;

        $this->refresh();

    }

    public function refresh()
    {
        $sql = "SELECT * FROM website_settings";

        $result = $this->db->query($sql);

        // fetch all
        $this->data = $result->fetch_all(MYSQLI_ASSOC);

        $this->data = array_map(function ($item) {
            $decoded_value = json_decode($item['value'], true);

            if (json_last_error() == JSON_ERROR_NONE) {
                $item['value'] = $decoded_value;
            }

            return $item;
        }, $this->data);

        $this->data = array_reduce($this->data, function ($result, $item) {
            $result[$item['name']] = [
                'value' => $item['value'],
                'category' => $item['setting_category']
            ];
            return $result;
        }, []);
    }


    public function set($name, $value, $category = null)
    {

        $value = (is_array($value) || is_object($value)) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;

        $sql = "INSERT INTO website_settings SET value = :value";
        if ($category)
            $sql .= ", setting_category = :category";
        $sql .= " WHERE name = :name
        ON DUPLICATE KEY UPDATE value = :value";

        if ($category)
            $sql .= ", setting_category = :category";

        $this->db->query($sql, [
            'value' => $value,
            'name' => $name,
            'category' => $category
        ]);

        if (isset($this->data[$name])) {
            $this->data[$name]['value'] = $value;
            if ($category) {
                $this->data[$name]['category'] = $category;
            }

        } else {
            $this->data[$name] = [
                'value' => $value,
                'category' => $category
            ];
        }
    }

    public function set_bulk($data)
    {
        $sql = "INSERT INTO website_settings (name, value, setting_category) VALUES ";
        foreach ($data as $name => $value) {
            $value = (is_array($value) || is_object($value)) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
            $sql .= "('" . $this->db->escape($name) . "', '" . $this->db->escape($value) . "', ''),";
        }

        $sql = substr($sql, 0, -1);

        $sql .= " ON DUPLICATE KEY UPDATE value = VALUES(value)";

        $this->db->query($sql);

        $this->refresh();

    }

    public function get($name)
    {
        return $this->data[$name]['value'] ?? null;
    }

    public function dump()
    {
        return $this->data;
    }

    public function __get($name)
    {
        // Fetch from the registry if it exists
        if (isset($this->registry->registry[$name])) {
            return $this->registry->registry[$name];
        }
        return null; // Or throw an error if you want strict behavior
    }

    public function __destruct()
    {

    }
}