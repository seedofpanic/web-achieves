<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Achivment_model extends CI_Model
{
    public $rules = null;

    public function get_by_domain($domain_id, $limit = 100, $offset = 0)
    {
        $query = $this->db->get_where('achives', array('domain_id' => $domain_id, 'deleted' => 0), $limit, $offset);

        return $query->result();
    }

    public function save($id, $data)
    {
        if ($id > 0) {
            $this->db->update('achives', $data, array('id' => $id));
        } else {
            $this->db->insert('achives', $data);
        }
    }

    public function update($id, $data) {
        $this->db->update('achives', $data, array('id' => $id));
    }
}