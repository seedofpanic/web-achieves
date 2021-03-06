<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Achievment_model extends CI_Model
{
    public $rules = null;

    public function check_access($user_id, $achieve_id) {
        if (!($user_id > 0)) {
            return false;
        }
        $query = $this->db->join('domains d', 'd.user_id=' . $user_id . ' and d.id=a.domain_id', 'inner')
            ->get_where('achieves a', array('a.id' => $achieve_id, 'a.deleted' => 0));
        return count($query->result()) > 0 ? true : false;
    }

    public function get_by_ids($ids) {
        if (count($ids) > 0) {
            $achieves = $this->db->select('id,title,image,text')->where_in('id', $ids)->get('achieves')->result();
            return $achieves;
        } else {
            return array();
        }
    }

    public function get_for_visitor($domain_id, $session) {
        $query = $this->db->join('achieve_rules', 'achieve_rules.achieve_id = a.id', 'inner')
            ->select('a.id,a.title,a.image,a.title_hidden,a.image_hidden,a.text_hidden,a.text');
        $query->select('EXISTS(SELECT id FROM visitor_achieves where achieve_id=a.id and visitor_id=' . $session->id . ') as achieved', false);
        $query = $this->db->get_where('achieves a', array('a.domain_id' => $domain_id, 'a.deleted' => 0, 'a.active' => 1));
        $achieves = $query->result();
        foreach ($achieves as &$achieve) {
            if (!$achieve->achieved) {
                if ($achieve->title_hidden) {
                    $achieve->title = '????? ?????';
                }
                if ($achieve->image_hidden) {
                    $achieve->image = 'http://webachievs.ru/images/hidden.jpg';
                }
                if ($achieve->text_hidden) {
                    $achieve->text = '????? ?????';
                }
            }
        }
        return $achieves;
    }

    public function get_by_rules($domain_id, $data) {
        $query = $this->db->join('achieve_rules', 'achieve_rules.achieve_id = a.id', 'inner')
            ->where(array(
                'achieve_rules.type' => 1,
                'achieve_rules.data' => $data['url']
            ));
        if (count($data['achieved']) > 0) {
            $query->where_not_in('a.id', $data['achieved']);
        }
        $query = $this->db->select('a.id')->get_where('achieves a', array('domain_id' => $domain_id, 'deleted' => 0, 'active' => 1));

        return array_map(function ($item) {return $item->id;}, $query->result());
    }

    public function get_by_stats($domain_id, $data) {
        $query = $this->db->join('achieve_rules', 'achieve_rules.achieve_id = a.id', 'inner')
            ->where(array(
                'achieve_rules.type' => 4,
                'achieve_rules.data<=' => $data['visits_count']
            ));
        if (count($data['achieved']) > 0) {
            $query->where_not_in('a.id', $data['achieved']);
        }
        $query = $this->db->select('a.id')->get_where('achieves a', array('domain_id' => $domain_id, 'deleted' => 0, 'active' => 1));

        return array_map(function ($item) {return $item->id;}, $query->result());
    }

    public function get_timer_rules($domain_id, $data) {
        $query = $this->db->join('achieve_rules', 'achieve_rules.achieve_id = a.id', 'inner')
            ->where("((achieve_rules.type=3 and
                achieve_rules.data like " . $this->db->escape($data['url'] . '%') . ") or
                achieve_rules.type=5)
            ", null,false);
        if (count($data['achieved']) > 0) {
            $query->where_not_in('a.id', $data['achieved']);
        }
        $query = $this->db->select('a.id,achieve_rules.type,achieve_rules.data')->get_where('achieves a', array('domain_id' => $domain_id, 'deleted' => 0, 'active' => 1));
        return $query->result();
    }

    public function check_achieve_rule($domain_id, $visitor_id, $new_achieves) {
        $to_achieve = array();
        $this->db->select('va.achieve_id as id');
        $this->db->where(array('visitor_id' => $visitor_id));
        $this->db->where('EXISTS(SELECT id FROM achieves a WHERE domain_id=' . (int)$domain_id . ' and a.id=va.achieve_id)', '', false);
        $achived = $this->db->get('visitor_achieves va')->result();
        $achived = array_map(function ($item) {return $item->id;}, $achived);
        $achived = array_merge($achived, array_diff($new_achieves, $achived));
        foreach ($new_achieves as $achive_id) {
            $rules = $this->db->where('EXISTS(SELECT a.id FROM achieves a WHERE domain_id=' . (int)$domain_id . ' and a.id=ar.achieve_id)', '', false)
                ->where('type', '2')
                ->where_not_in('achieve_id', $achived)
                ->where('data like', "%\"" . (int)$achive_id . "\"%")
                ->get('achieve_rules ar')
                ->result();
            foreach ($rules as $rule) {
                $need_achieves = json_decode($rule->data);
                if (count(array_diff($need_achieves, $achived)) == 0) {
                    $to_achieve[$rule->achieve_id] = $rule->achieve_id;
                }
            }
        }
        return $to_achieve;
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

    public function statistic($domain_id, $start_date, $end_date) {
        $this->db
            ->select('a.*,(SELECT count(va.id) FROM visitor_achieves va
            INNER JOIN visitor_stats vs on vs.visitor_id=va.visitor_id and vs.domain_id=' . $domain_id . ' and vs.first_visit>' . (int)$start_date . ' and vs.first_visit<' . (int)$end_date . '
            WHERE va.achieve_id=a.id and achieve_date>' . (int)$start_date . ' and achieve_date<' . (int)$end_date . ') as totals', false);
        $query = $this->db->get_where('achieves a', array('a.domain_id' => $domain_id, 'a.deleted' => 0));
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