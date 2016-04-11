<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Domain_model extends CI_Model
{
    public function check_access($user_id, $domain_id) {
        if (!($user_id > 0)) {
            return false;
        }
        $query = $this->db->get_where('domains', array('user_id' => $user_id, 'id' => $domain_id, 'deleted' => 0));
        return (count($query->result()) > 0) ? true : false;
    }

    public function get_by_user($user_id, $limit = 0, $offset = 0) {
        $query = $this->db->get_where('domains', array('user_id' => $user_id, 'deleted' => 0), $limit, $offset);

        return $query->result();
    }

    public function get_by_id($id) {
        $query = $this->db->get_where('domains', array('id' => $id, 'deleted' => 0), 1, 0);
        $results = $query->result();
        return isset($results[0]) ? $results[0] : null;
    }

    public function get_by_name($name) {
        $query = $this->db->get_where('domains', array('name' => $name, 'deleted' => 0), 1, 0);
        $results = $query->result();
        return isset($results[0]) ? $results[0] : null;
    }

    public function check($data) {
        $passed = true;
        $passed = $passed && $this->get_by_name($data['name']) == null;
        return $passed;
    }

    public function statistic($domain_id, $start_date, $end_date) {
        $totals = $this->db->where(array('domain_id' => $domain_id, 'first_visit>' => (int)$start_date, 'first_visit<' => (int)$end_date))->count_all_results('visitor_stats');
        return array('totals' => $totals);
    }

    public function create($data) {
        if ($this->check($data)) {
            $this->db->insert('domains', $data);
            $insert_id = $this->db->insert_id();
            $domain = $this->get_by_id($insert_id);
            return $domain;
        } else {
            return null;
        }
    }

    public function update($id, $data) {
        $this->db->update('domains', $data, array('id' => $id));
    }
}