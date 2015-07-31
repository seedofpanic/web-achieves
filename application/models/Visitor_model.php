<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Visitor_model extends CI_Model
{
    public function get($data) {
        if (!isset($data['session'])) {
            return $this->create();
        } else {
            $query = $this->db->get_where('visitors', array('session_id' => $data['session']));
            $results = $query->result();
            if (count($results) > 0) {
                return $results[0];
            } else {
                return $this->create();
            }
        }
    }

    public function create() {
        while (true) {
            $session_id = hash('sha256', rand());
            $query = $this->db->get_where('visitors', array('session_id' => $session_id));
            $results = $query->result();
            if (count($results) == 0) {
                break;
            }
        }
        $this->db->insert('visitors', array('session_id' => $session_id));
        return array('id' => $this->db->insert_id(), 'session_id' => $session_id);
    }

    public function update($id, $data) {
        $this->db->update('visitors', $data, array('id' => $id));
    }

    public function achive($session, $achives) {
        foreach ($achives as $achive) {
            $data = array(
                'achive_id' => $achive->id,
                'visitor_id' => $session->id
            );
            $this->db->insert('visitor_achives', $data);
        }
    }

    public function achived($domain_id, $session){
        $query = $this->db->select('a.id')
            ->join('visitor_achives va', 'visitor_id=' . $session->id . ' and va.achive_id=a.id', 'inner')
        ->get_where('achives a', array('domain_id', $domain_id));
        return array_map(function ($item) {
            return $item->id;
        },$query->result());
    }
}