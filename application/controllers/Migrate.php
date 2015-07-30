<?php

class Migrate extends CI_Controller
{

    public function index()
    {
        $this->load->library('migration');
        $this->migration->latest();
        /*if ($this->migration->current() === FALSE)
        {
            show_error($this->migration->error_string());
        }*/
    }

}