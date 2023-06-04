<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hello_bootstrap extends CI_Controller
{

    public function index()
    {
        $this->load->view('helloBootstrap');
    }
}
