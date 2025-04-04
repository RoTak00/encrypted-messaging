<?php

class MessageEncModel extends BaseModel
{
    public function add($data)
    {
        $this->db->query("INSERT INTO enc_message SET 
        enc_message = '" . $this->db->escape($data['enc_message']) . "',
        public_key = ' " . $this->db->escape($data['public_key']) . "',
        enc_sym_key = '" . $this->db->escape($data['enc_sym_key']) . "'");

        return $this->db->insert_id();
    }

    public function get($id)
    {
        $sql = "SELECT * FROM enc_message WHERE message_id = '" . (int) $id . "'";

        $result = $this->db->query($sql);

        return $result->fetch_assoc();
    }

    public function addResponse($message_id, $response)
    {
        $this->db->query("INSERT INTO enc_response SET
        message_id = '" . (int) $message_id . "',
        enc_response = '" . $this->db->escape($response) . "'");

        return true;
    }

    public function getResponses($message_id)
    {
        $sql = "SELECT * FROM enc_response WHERE message_id = '" . (int) $message_id . "'";

        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

}