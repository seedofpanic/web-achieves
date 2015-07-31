<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Achivment_model extends CI_Model
{
    public $rules = null;

    public function get_by_rules($domain_id, $data) {
        $query = $this->db->join('achive_rules', 'achive_rules.achive_id = achives.id', 'inner')
            ->where(array(
                'achive_rules.type' => 1,
                'achive_rules.data' => $data['url']
            ))
        ->select('achives.*');
        if (count($data['achieved']) > 0) {
            $query->where_not_in('achives.id', $data['achieved']);
        }
        $query = $this->db->get_where('achives', array('domain_id' => $domain_id, 'deleted' => 0));

        return $query->result();
    }

    public function get_by_id($id)
    {
        $query = $this->db->get_where('achives', array('id' => $id));

        return $query->result()[0];
    }

    public function get_by_domain($domain_id, $limit = 100, $offset = 0)
    {
        $query = $this->db->get_where('achives', array('domain_id' => $domain_id, 'deleted' => 0), $limit, $offset);

        return $query->result();
    }

    public function save($id, $data)
    {
        if ($id > 0) {
            $this->db->update('achives', $data, array('id' => $id));
            return $id;
        } else {
            $this->db->insert('achives', $data);
            return $this->db->insert_id();
        }
    }

    public function update($id, $data) {
        $this->db->update('achives', $data, array('id' => $id));
    }
}