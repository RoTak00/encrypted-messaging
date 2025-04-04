<?php

class CommonHeadController extends BaseController
{

    public function index($setting = [])
    {
        global $HOST;
        $this->response->addStyle("/resources/inc/bootstrap.min.css", [], 1);
        $this->response->addScript("/resources/inc/bootstrap.bundle.min.js", [], 1);
        $this->response->addScript("/resources/inc/jquery.js", [], 1);


        if ($this->setting->get('is_local') !== '1') {
            $this->response->addScript("/resources/scripts/wireboard.js");
        }

        $this->response->addStyle("/resources/css/common/styles.css");

        $data = [];

        $data['styles'] = $this->response->getStyles();
        $data['scripts'] = $this->response->getScripts();

        $this->response->addMeta('og:site_name', $this->setting->get('og_site_name') ?? 'My website');
        $this->response->addMeta('og:url', $HOST . '/' . $this->request->get['path']);

        $data['meta'] = $this->response->getMeta();


        $data['title'] = $setting['page_title'] ?? $this->setting->get('default_page_name') ?? 'My website';

        $data['notification'] = $this->loadController('common/notification');


        return $this->loadView('common/head.php', $data);
    }
}