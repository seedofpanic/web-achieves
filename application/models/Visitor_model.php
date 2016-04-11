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

    public function link($domain_id, $visitor_id){
        $this->db->query("INSERT IGNORE INTO visitor_domains SET `domain_id`='" . (int)$domain_id . "', `visitor_id`='" . (int)$visitor_id . "'");
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

    public function achieve($session, $achieves) {
        foreach ($achieves as $achieve) {
            $data = array(
                'achieve_id' => $achieve,
                'visitor_id' => $session->id,
                'achieve_date' => time()
            );
            $this->db->insert('visitor_achieves', $data);
        }
    }

    public function update_stats($domain_id, $session_id, $data) {
        $result = $this->db->get_where('visitor_stats', array(
            'domain_id' => $domain_id,
            'visitor_id' => $session_id
        ), 1, 0)->result_array();
        if (isset($result[0])) {
            $stats = $result[0];
            if ($data['url'] != $stats['last_url']) {
                $stats['visits_count']++;
            }
            $this->db->update('visitor_stats', array(
                'last_url' => $data['url'],
                'visits_count' => $stats['visits_count']
            ), array('id' => $stats['id']));
        } else {
            $stats = array(
                'domain_id' => $domain_id,
                'visitor_id' => $session_id,
                'last_url' => $data['url'],
                'visits_count' => 1,
                'first_visit' => time()
            );
            $this->db->insert('visitor_stats', $stats);
        }
        return $stats;
    }

    public function achieved($domain_id, $session){
        $query = $this->db->select('a.id')
            ->join('visitor_achieves va', 'visitor_id=' . $session->id . ' and va.achieve_id=a.id', 'inner')
        ->get_where('achieves a', array('a.domain_id' => $domain_id));
        return array_map(function ($item) {
            return $item->id;
        },$query->result());
    }
}