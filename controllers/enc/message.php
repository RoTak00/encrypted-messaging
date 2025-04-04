<?php

class EncMessageController extends BaseController
{

    public function send()
    {
        if (!$this->request->server['REQUEST_METHOD'] == 'POST') {
            http_response_code(400);
            die();
        }

        if (!isset($this->request->post['message'])) {
            http_response_code(400);
            die();
        }

        // encrypt the message
        $sym_key = $this->encryption->generateSymmetricKey();
        $url_sym_key = $this->encryption->urlencode($sym_key);
        $data = ['date' => date("Y-m-d H:i:s"), 'message' => $this->request->post['message']];
        $encrypted = $this->encryption->encryptSymmetric($data, $sym_key);


        // create asymm key pair
        $keypair = $this->encryption->generateAsymmetricKeypair();
        $url_public_key = $this->encryption->urlencode($keypair['public']);
        $url_private_key = $this->encryption->urlencode($keypair['private']);


        $enc_sym_key = $this->encryption->encryptAsymmetricWithPublic($sym_key, $keypair['public']);
        $enc_sym_key = $this->encryption->urlencode($enc_sym_key);

        $this->loadModel('message/enc');
        $message_data =
            [
                'enc_message' => $encrypted,
                'public_key' => $url_public_key,
                'enc_sym_key' => $enc_sym_key
            ];
        $message_id = $this->model_message_enc->add($message_data);



        $responder_link = $this->url->link('enc/message/receive/' . $message_id . '/' . $url_sym_key);
        $host_link = $this->url->link('enc/message/view/' . $message_id . '/' . $url_private_key);


        return json_encode(['responder_link' => $responder_link, 'host_link' => $host_link]);
    }

    public function receive($params)
    {
        if (!isset($params[0]) || !isset($params[1])) {

            $this->response->redirect($this->url->link('common/home'));
            return;
        }


        $message_id = $params[0];
        $key = $this->encryption->urldecode($params[1]);

        $this->loadModel('message/enc');
        $message = $this->model_message_enc->get($message_id);

        if (!$message) {
            $this->response->redirect($this->url->link('common/home'));
            return;
        }

        $dec_message = $this->encryption->decryptSymmetric($message['enc_message'], $key);

        $data = [
            'date' => $dec_message['date'],
            'message' => $dec_message['message'],
            'key' => $key
        ];

        $data['send_response'] = $this->url->link('enc/message/response/' . $message_id);

        $data['footer'] = $this->loadController('common/footer');


        $head_settings = ['page_title' => 'Full Encrypted'];
        $data['head'] = $this->loadController('common/head', $head_settings);

        return $this->loadView('message/receive.php', $data);
    }

    public function response($params)
    {
        if (!isset($params[0])) {
            http_response_code(400);
            return;
        }

        $message_id = $params[0];

        $this->loadModel('message/enc');
        $message = $this->model_message_enc->get($message_id);

        if (!$message) {
            http_response_code(400);
            return;
        }

        if (!isset($this->request->post['response'])) {
            http_response_code(400);
            return;
        }

        $pub_key = $this->encryption->urldecode($message['public_key']);

        $response = $this->request->post['response'];

        $enc_response = $this->encryption->encryptAsymmetricWithPublic($response, $pub_key);

        $this->model_message_enc->addResponse($message_id, $enc_response);

        http_response_code(200);

        echo json_encode(['success' => true]);
    }

    public function view($params)
    {
        if (!isset($params[0]) || !isset($params[1])) {

            $this->response->redirect($this->url->link('common/home'));
            return;
        }


        $message_id = $params[0];
        $key = $this->encryption->urldecode($params[1]);

        $this->loadModel('message/enc');
        $message = $this->model_message_enc->get($message_id);

        if (!$message) {
            $this->response->redirect($this->url->link('common/home'));
            return;
        }

        $public_key = $this->encryption->urldecode($message['public_key']);
        $keypair = $this->encryption->getKeypairFromKeys($public_key, $key);

        $sym_key = $this->encryption->urldecode($message['enc_sym_key']);
        $sym_key = $this->encryption->decryptAsymmetric($sym_key, $keypair);

        $dec_message = $this->encryption->decryptSymmetric($message['enc_message'], $sym_key);

        $responses_enc = $this->model_message_enc->getResponses($message_id);

        $responses = array_map(function ($response) use ($keypair) {
            $response = $this->encryption->decryptAsymmetric($response['enc_response'], $keypair);
            return $response;
        }, $responses_enc);

        $data = [
            'date' => $dec_message['date'],
            'message' => $dec_message['message'],
            'responses' => $responses
        ];

        $data['footer'] = $this->loadController('common/footer');


        $head_settings = ['page_title' => 'Full Encrypted'];
        $data['head'] = $this->loadController('common/head', $head_settings);
        return $this->loadView('message/view.php', $data);

    }

}