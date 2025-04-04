<?php

class CommonHomeController extends BaseController
{


    public function index()
    {
        $data = [];

        $this->response->addScript('/resources/scripts/scroll-to.js');
        $data['footer'] = $this->loadController('common/footer');


        $head_settings = ['page_title' => 'Full Encrypted'];
        $data['head'] = $this->loadController('common/head', $head_settings);
        return $this->loadView('common/home.php', $data);
    }
}