<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Domain_model extends CI_Model
{
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