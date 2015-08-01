<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Achievment_model extends CI_Model
{
    public $rules = null;

    public function get_for_visitor($domain_id, $session) {
        $query = $this->db->join('achieve_rules', 'achieve_rules.achieve_id = achieves.id', 'inner')
            ->where(array(
                'achieve_rules.type' => 1
            ))
            ->select('achieves.*');
        $query->select('EXISTS(SELECT id FROM visitor_achieves where achieve_id=achieves.id and visitor_id=' . $session->id . ') as achieved', false);
        $query = $this->db->get_where('achieves', array('domain_id' => $domain_id, 'deleted' => 0, 'active' => 1));

        return $query->result();
    }

    public function get_by_rules($domain_id, $data) {
        $query = $this->db->join('achieve_rules', 'achieve_rules.achieve_id = achieves.id', 'inner')
            ->where(array(
                'achieve_rules.type' => 1,
                'achieve_rules.data' => $data['url']
            ))
        ->select('achieves.*');
        if (count($data['achieved']) > 0) {
            $query->where_not_in('achieves.id', $data['achieved']);
        }
        $query = $this->db->get_where('achieves', array('domain_id' => $domain_id, 'deleted' => 0, 'active' => 1));

        return $query->result();
    }

    public function get_by_id($id)
    {
        $query = $this->db->get_where('achieves', array('id' => $id));

        return $query->result()[0];
    }

    public function get_by_domain($domain_id, $limit = 100, $offset = 0)
    {
        $query = $this->db->get_where('achieves', array('domain_id' => $domain_id, 'deleted' => 0), $limit, $offset);

        return $query->result();
    }

    public function save($id, $data)
    {
        if ($id > 0) {
            $this->db->update('achieves', $data, array('id' => $id));
            return $id;
        } else {
            $this->db->insert('achieves', $data);
            return $this->db->insert_id();
        }
    }

    public function update($id, $data) {
        $this->db->update('achieves', $data, array('id' => $id));
    }
}