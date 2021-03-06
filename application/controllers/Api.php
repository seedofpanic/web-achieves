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

    public function me() {
        print json_encode(
            $this->ion_auth->get_user()
        );
    }

    private function check_access($user_id, $domain_id = null, $achieve_id = null) {
        if ($domain_id != null) {
            $this->load->model('Domain_model');
            return $this->Domain_model->check_access($user_id, $domain_id);
        }
        if ($achieve_id != null) {
            $this->load->model('Achievment_model');
            return $this->Achievment_model->check_access($user_id, $achieve_id);
        }
    }

    public function domains($id = null)
    {
        $this->load->model('Domain_model');

        if (!$this->ion_auth->logged_in()) {
            http_response_code(401);
            print '{}';
            return;
        }

        $domains = $this->Domain_model->get_by_user($this->user_id);

        print json_encode($domains);
    }

    public function visitor($domain_id, $action) {
        if ($action == 'achieves') {
            $this->load->model('Achievment_model');
            $this->load->model('Visitor_model');
            $session = $this->Visitor_model->get(array('session' => $this->input->post('session_id')));
            $achievments = $this->Achievment_model->get_for_visitor($domain_id, $session);
            print json_encode($achievments);
        }
    }

    public function domain($id = null, $action = null)
    {
        $this->load->model('Domain_model');
        if ($action == 'new') {
            $this->form_validation->set_rules('name', 'error', 'required');
            if ($this->form_validation->run() == true) {
                $name = $this->input->post('name');
                $url = parse_url($name);
                if (!isset($url['host'])) {
                    if (preg_match('/([\w-]+?\.[\w]+)/', $name, $matches)) {
                        $url['host'] = $matches[1];
                    } else {
                        $this->error('Введенная строка не является названием домена.');
                        return;
                    }
                }
                $new_domain = array(
                    'name' => $url['host'],
                    'user_id' => $this->user_id
                );
                $domain = $this->Domain_model->create($new_domain);
                print json_encode($domain);
            } else {
                print '{}';
            }
            return;
        }
        if (!$this->check_access($this->user_id, $id)) {
            http_response_code(401);
            print '{}';
            return;
        }

        if ($action == 'statistics') {
            // Даты за которые выбирать
            $start_date_str = $this->input->get('start_date');
            $end_date_str = $this->input->get('end_date');
            if ($start_date_str) {
                $start_date = (new DateTime($start_date_str))->getTimestamp();
            } else {
                $start_date = (new DateTime())->modify('-1 month')->getTimestamp();
            }
            if ($end_date_str) {
                $end_date = (new DateTime($end_date_str))->getTimestamp();
            } else {
                $end_date = (new DateTime())->getTimestamp();
            }

            $this->load->model('Achievment_model');
            $data = $this->Domain_model->statistic($id, $start_date, $end_date);

            $statistic = array(
                'start_date' => date('Y/m/d h:i', $start_date),
                'end_date' => date('Y/m/d h:i', $end_date),
                'vals' => array(
                    array('name' => 'Поситителей зарегистрировано', 'data' => $data['totals'])
                )
            );
            $statistic['totals'] = $data['totals'];
            $statistic['achieves'] = $this->Achievment_model->statistic($id, $start_date, $end_date);
            print json_encode($statistic);
        }
        if ($action == 'starter_pack') {
            $this->load->model('Achievment_model');
            $this->load->model('Achievment_rule_model');
            if ($this->input->post('type') === '0') {
                $rules_data = $this->input->post('data');
                $achieves = array();
                //1)Посищение 1 страницы
                $data = array(
                    'name' => 'Стандарт 1',
                    'title' => 'Добро пожаловать!',
                    'image' => 'http://webachievs.ru/images/prep/default/1.jpg',
                    'text' => '<p>Мы рады вас видеть сайте. У вас получилось выполнить первое достижение.</p><p><a href="javascript:;" onclick="WA.showAll()">Раскрыть все достижения которые мы для вас приготовили.</a></p>',
                    'domain_id' => $id
                );
                $rules = array(array(
                    'type' => '4',
                    'data' => '1'
                ));
                $new_id = $this->Achievment_model->save(null, $data);
                $this->Achievment_rule_model->save_batch($new_id, $rules);
                $achieves[] = (string)$new_id;
                //2)Посещение 3х страниц
                $data = array(
                    'name' => 'Стандарт 2',
                    'title' => 'Первое знакомство.',
                    'image' => 'http://webachievs.ru/images/prep/default/2.jpg',
                    'text' => 'Посмотрите 3 страницы нашего сайта.',
                    'domain_id' => $id
                );
                $rules = array(array(
                    'type' => '4',
                    'data' => (int)$rules_data['2']
                ));
                $new_id = $this->Achievment_model->save(null, $data);
                $this->Achievment_rule_model->save_batch($new_id, $rules);
                $achieves[] = (string)$new_id;
                //3)30 секунд на странице
                $data = array(
                    'name' => 'Стандарт 3',
                    'title' => 'Внимательный читатель.',
                    'image' => 'http://webachievs.ru/images/prep/default/3.jpg',
                    'text' => 'Провести 30 секунд на странице.',
                    'domain_id' => $id
                );
                $rules = array(array(
                    'type' => '5',
                    'data3' => json_encode($rules_data['3']),
                    'data' => ''
                ));
                $new_id = $this->Achievment_model->save(null, $data);
                $this->Achievment_rule_model->save_batch($new_id, $rules);
                $achieves[] = (string)$new_id;
                //4)Посещение страницы
                $data = array(
                    'name' => 'Стандарт 4',
                    'title' => 'Интересная страница.',
                    'image' => 'http://webachievs.ru/images/prep/default/4.jpg',
                    'text' => 'Вы посетили важную страницу нашего сайта',
                    'domain_id' => $id
                );
                $rules = array(array(
                    'type' => '1',
                    'data' => $this->Achievment_rule_model->parseLink($rules_data['4'])
                ));
                $new_id = $this->Achievment_model->save(null, $data);
                $this->Achievment_rule_model->save_batch($new_id, $rules);
                $achieves[] = (string)$new_id;
                //5)Выполнение всех ачивок
                $data = array(
                    'name' => 'Стандарт 4',
                    'title' => 'Король достижений.',
                    'image' => 'http://webachievs.ru/images/prep/default/5.jpg',
                    'text' => 'Поздравляем, вы собрали все достижения которые мы вам приготовили.',
                    'domain_id' => $id
                );
                $rules = array(array(
                    'type' => '2',
                    'data2' => $achieves,
                    'data' => ''
                ));
                $new_id = $this->Achievment_model->save(null, $data);
                $this->Achievment_rule_model->save_batch($new_id, $rules);
                $achieves[] = (string)$new_id;

                $achievments = $this->Achievment_model->get_by_domain($id);

                foreach ($achievments as &$achievment) {
                    $achievment->rules = $this->Achievment_rule_model->get_by_achieve($achievment->id);
                }

                print json_encode($achievments);
            }
        }
        if ($action == 'get') {
            $domain = $this->Domain_model->get_by_id($id);
            print json_encode($domain);
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

    public function achievments($id, $offset = 0)
    {
        if (!$this->check_access($this->user_id, $id)) {
            http_response_code(401);
            print '[]';
            return;
        }

        $this->load->model('Achievment_model');
        $this->load->model('Achievment_rule_model');

        $achievments = $this->Achievment_model->get_by_domain($id);

        foreach ($achievments as &$achievment) {
            $achievment->rules = $this->Achievment_rule_model->get_by_achieve($achievment->id);
        }

        print json_encode($achievments);
    }

    public function achievment($id, $action) {
        if ($id > 0 && !$this->check_access($this->user_id, null, $id)) {
            http_response_code(401);
            print '[]';
            return;
        }
        if (!($id > 0)) {
            if (!$this->check_access($this->user_id, $this->input->post('domain_id'))) {
                http_response_code(401);
                print '{}';
                return;
            }
        }
        $this->load->model('Achievment_model');
        $this->load->model('Achievment_rule_model');
        if ($action == 'activate') {
            $data = array(
                'active' => $this->input->post('activate') == 'true' ? 1 : 0
            );
            $this->Achievment_model->update($id, $data);
            print '{}';
        }
        if ($action == 'save')
        {
            $data = array(
                'name' => $this->input->post('name'),
                'title' => $this->input->post('title'),
                'image' => $this->input->post('image'),
                'text' => $this->input->post('text'),
                'domain_id' => $this->input->post('domain_id'),
                'title_hidden' => $this->input->post('title_hidden'),
                'image_hidden' => $this->input->post('image_hidden'),
                'text_hidden' => $this->input->post('text_hidden')
            );
            $new_id = $this->Achievment_model->save($id, $data);
            $achievment = $this->Achievment_model->get_by_id($new_id);
            $this->Achievment_rule_model->save_batch($achievment->id, $this->input->post('rules'));
            print json_encode($achievment);
        }
        if ($action == 'delete') {
            $params = array(
                'deleted' => $this->input->post('deleted')
            );
            $domain = $this->Achievment_model->update($id, $params);
            print $domain;
        }
    }

    public function achieve($achieve_id) {
        $this->timeout($achieve_id);
    }

    public function timeout($achieve_id) {
        //TODO проверить принадлежит ли домен юзеру
        $this->load->model('Achievment_model');
        $this->load->model('Visitor_model');

        $url = parse_url($this->input->post('url'));
        $session = $this->Visitor_model->get(array('session' => $this->input->post('session_id')));
        $achieve = $this->Achievment_model->get_by_id($achieve_id);
        $achieved = $this->Visitor_model->achieved($achieve->domain_id, $session);
        $data = array(
            'url' => $url['path']
                . (isset($url['query']) ? '?' . $url['query'] : '')
                . (isset($url['fragment']) ? '#' . $url['fragment'] : ''),
            'achieved' => $achieved
        );

        $achievments = array($achieve_id);

        $this->doAchieve($achievments, $achieved, $achieve->domain_id, $session, $data);
    }

    //Должен вызываться один раз за страницу
    public function check($domain_id = null) {
        $this->load->model('Achievment_model');
        $this->load->model('Visitor_model');

        $url = parse_url($this->input->post('url'));
        $parsed_url = $url['path']
            . (isset($url['query']) ? '?' . $url['query'] : '')
            . (isset($url['fragment']) ? '#' . $url['fragment'] : '');
        $session = $this->Visitor_model->get(array('session' => $this->input->post('session_id')));
        $new_stats = $this->Visitor_model->update_stats($domain_id, $session->id,
            array('url' => $parsed_url)
        );
        $achieved = $this->Visitor_model->achieved($domain_id, $session);
        $data = array(
            'url' => $parsed_url,
            'achieved' => $achieved
        );

        // Проверяем выполненные по заходу на урл
        $achievments = $this->Achievment_model->get_by_rules($domain_id, $data);

        $new_achieved = $achievments;

        // Проверяем выполненные по набранной статистике
        $new_stats['achieved'] = $achieved;
        $achievments = $this->Achievment_model->get_by_stats($domain_id, $new_stats);

        $new_achieved = array_merge($achievments, array_diff($new_achieved, $achievments));

        $this->doAchieve($new_achieved, $achieved, $domain_id, $session, $data);
    }

    // Записываем выполненные достижения, затем проверяем не получили ли мы достижений за выполнение достижений... We need to go deeper...
    private function doAchieve($achievments, $achieved, $domain_id, $session, $data) {
        $achieved = array_merge($achievments, array_diff($achieved, $achievments));
        do {
            $new_achieved = $this->Achievment_model->check_achieve_rule($domain_id, $session->id, $achieved);
            $achievments = array_merge($achievments, array_diff($new_achieved, $achievments));
            $achieved = array_merge($new_achieved, array_diff($achieved, $new_achieved));
        } while (count($new_achieved) != 0);

        $this->Visitor_model->achieve($session, $achievments);

        $this->Visitor_model->link($domain_id, $session->id);

        $data['achieved'] = $achieved;

        $timers = $this->Achievment_model->get_timer_rules($domain_id, $data);

        $result = array(
            'achieved' => $this->Achievment_model->get_by_ids($achievments),
            'timers'   => $timers
        );
        print json_encode($result);
    }

    public function create_session() {
        $this->load->model('Visitor_model');
        $session = $this->Visitor_model->create();
        print $session['session_id'];
    }

    public function error($msg) {
        http_response_code(400);
        print json_encode($msg);
    }
}