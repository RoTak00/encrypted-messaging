<?php

class MessageEncModel extends BaseModel
{
    public function add($data)
    {
        $message_id = $this->encryption->generate_uuid_v4();

        $this->db->query("INSERT INTO enc_message SET 
        message_id = '" . $this->db->escape($message_id) . "',
        enc_message = '" . $this->db->escape($data['enc_message']) . "',
        public_key = ' " . $this->db->escape($data['public_key']) . "',
        enc_sym_key = '" . $this->db->escape($data['enc_sym_key']) . "'");

        return $message_id;
    }

    public function get($id)
    {
        $sql = "SELECT * FROM enc_message WHERE message_id = '" . $this->db->escape($id) . "'";

        $result = $this->db->query($sql);

        return $result->fetch_assoc();
    }

    public function addResponse($message_id, $response)
    {
        $this->db->query("INSERT INTO enc_response SET
        message_id = '" . $this->db->escape($message_id) . "',
        enc_response = '" . $this->db->escape($response) . "'");

        return true;
    }

    public function getResponses($message_id)
    {
        $sql = "SELECT * FROM enc_response WHERE message_id = '" . $this->db->escape($message_id) . "'";

        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

}