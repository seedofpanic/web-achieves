<?php

class Api extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library(array('ion_auth','form_validation'));
        //TODO добавить проверку авторизованности
        $this->user_id = $this->ion_auth->session->userdata('user_id');
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
    }

    public function domains($id = null)
    {
        $this->load->model('Domain_model');

        $domains = $this->Domain_model->get_by_user($this->user_id);

        print json_encode($domains);
    }

    public function domain($id = null, $action = null)
    {
        $this->load->model('Domain_model');
        if ($action == 'new') {
            $this->form_validation->set_rules('name', 'error', 'required');
            if ($this->form_validation->run() == true) {
                $new_domain = array(
                    'name' => parse_url($this->input->post('name'))['host'],
                    'user_id' => $this->user_id
                );
                $domain = $this->Domain_model->create($new_domain);
                print json_encode($domain);
            } else {
                print '{}';
            }
        }
        if ($action == 'update') {
            $this->form_validation->set_rules('name', 'error', 'required');
            if ($this->form_validation->run() == true) {
                $domain_data = array(
                    'name' => $this->input->post('name')
                );
                $domain = $this->Domain_model->update($id, $domain_data);
                print json_encode($domain);
            } else {
                $domain = $this->Domain_model->get_by_id($id);
                print json_encode($domain);
            }
        }
        if ($action == 'delete') {
            $params = array(
                'deleted' => $this->input->post('deleted')
            );
            $domain = $this->Domain_model->update($id, $params);
            print $domain;
        }
    }

    public function achivments($id, $offset = 0)
    {
        //TODO проверить принадлежит ли домен юзеру
        $this->load->model('Achivment_model');
        $this->load->model('Achivment_rule_model');

        $achivments = $this->Achivment_model->get_by_domain($id);

        foreach ($achivments as &$achivment) {
            $achivment->rules = $this->Achivment_rule_model->get_by_achive($achivment->id);
        }

        print json_encode($achivments);
    }

    public function achivment($id, $action) {
        $this->load->model('Achivment_model');
        $this->load->model('Achivment_rule_model');
        if ($action == 'save')
        {
            $data = array(
                'name' => $this->input->post('name'),
                'title' => $this->input->post('title'),
                'image' => $this->input->post('image'),
                'text' => $this->input->post('text'),
                'domain_id' => $this->input->post('domain_id')
            );
            $new_id = $this->Achivment_model->save($id, $data);
            $achivment = $this->Achivment_model->get_by_id($new_id);
            $this->Achivment_rule_model->save_batch($achivment->id, $this->input->post('rules'));
            print json_encode($achivment);
        }
        if ($action == 'delete') {
            $params = array(
                'deleted' => $this->input->post('deleted')
            );
            $domain = $this->Achivment_model->update($id, $params);
            print $domain;
        }
    }

    public function check($domain_id = null) {
        //TODO проверить принадлежит ли домен юзеру
        $this->load->model('Achivment_model');
        $this->load->model('Visitor_model');

        $url = parse_url($this->input->post('url'));
        $session = $this->Visitor_model->get(array('session' => $this->input->post('session_id')));
        $achieved = $this->Visitor_model->achived($domain_id, $session);
        $data = array(
            'url' => $url['path'] . (isset($url['query']) ? $url['query'] : ''),
            'achieved' => $achieved
        );

        $achivments = $this->Achivment_model->get_by_rules($domain_id, $data);

        $this->Visitor_model->achive($session, $achivments);

        print json_encode($achivments);
    }

    public function create_session() {
        $this->load->model('Visitor_model');
        $session = $this->Visitor_model->create();
        print $session['session_id'];
    }
}