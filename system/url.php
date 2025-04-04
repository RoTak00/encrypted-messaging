<?php


class Url
{
    private $registry = [];

    public $aliases = [];
    public function __construct($registry)
    {

        $this->registry = $registry;
    }


    public function __get($name)
    {
        if (isset($this->registry->registry[$name])) {
            return $this->registry->registry[$name];
        }
        return null; // Or throw an error if you want strict behavior
    }

    public function loadAlias($alias)
    {
        $sql = "SELECT * FROM url_alias WHERE alias = '" . $this->db->escape($alias) . "' AND status = 1";

        $result = $this->db->query($sql);

        if ($result->num_rows) {
            $row = $result->fetch_assoc();

            $sql = "UPDATE url_alias SET hits = hits + 1 WHERE alias = '" . $this->db->escape($alias) . "' AND status = 1";

            $this->db->query($sql);

            return $row['link'];
        }

        return $alias;
    }


    public function createAlias($alias, $link)
    {
        $sql = "INSERT INTO url_alias(alias, link) VALUES ('" . $this->db->escape($alias) . "', '" . $this->db->escape($link) . "')";

        $this->db->query($sql);

    }

    public function editAlias($alias, $link)
    {
        $sql = "UPDATE url_alias SET alias = '" . $this->db->escape($alias) . "' WHERE link = '" . $this->db->escape($link) . "'";

        $this->db->query($sql);
    }





    public function removeAlias($alias)
    {
        $sql = "DELETE FROM url_alias WHERE alias = '" . $this->db->escape($alias) . "'";
        $this->db->query($sql);
    }

    public function link($controller)
    {
        global $HOST;

        return $HOST . BASE_LOCATION . $controller;
    }

}