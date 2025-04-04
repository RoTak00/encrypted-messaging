<?php

class CommonHomeController extends BaseController
{


    public function index()
    {
        $data = [];

        $data['send_message'] = $this->url->link('enc/message/send');

        $data['footer'] = $this->loadController('common/footer');


        $head_settings = ['page_title' => 'Full Encrypted'];
        $data['head'] = $this->loadController('common/head', $head_settings);
        return $this->loadView('common/home.php', $data);
    }
}