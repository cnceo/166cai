<?php

class Migrate extends MY_Controller {

    public function index() {
        $data = $this->input->post();
        $data['baseUrl'] = $this->config->item('base_url');
        $data['pagesUrl'] = $this->config->item('pages_url');
        $this->load->view('migrate/index', $data);
    }

}
