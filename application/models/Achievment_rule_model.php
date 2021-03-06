<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Achievment_rule_model extends CI_Model
{
    public function get_by_achieve($achieve_id, $limit = 0, $offset = 0)
    {
        $query = $this->db->get_where('achieve_rules', array('achieve_id' => $achieve_id), $limit, $offset);

        return $query->result();
    }

    public function parseLink($link) {
        $url = parse_url($link);
        return $url['path']
        . (isset($url['query']) ? '?' . $url['query'] : '')
        . (isset($url['fragment']) ? '#' . $url['fragment'] : '');
    }

    public function save_batch($achieve_id, $rules)
    {
        if (is_array($rules)) {
            foreach ($rules as $rule) {
                if (isset($rule['deleted']) && $rule['deleted'] > 0 || !($rule['type'] > 0)) {
                    if (isset($rule['id']) && $rule['id'] > 0) {
                        $this->delete($rule['id']);
                    }
                } else {
                    $rule['data'] = $this->parseLink($rule['data']);
                    if ($rule['type'] == '2') {
                        $rule_data = json_encode(isset($rule['data2']) ? $rule['data2'] : array());
                    } else if ($rule['type'] == '3') {
                        $rule_data = $rule['data'] . '::' . json_encode(
                            isset($rule['data3']) ? $rule['data3'] : ''
                        );
                    } else if ($rule['type'] == '5') {
                        $rule_data = json_encode(
                                isset($rule['data3']) ? $rule['data3'] : ''
                            );
                    } else {
                        $rule_data = isset($rule['data']) ? $rule['data'] : '';
                    }
                    $data = array(
                        'achieve_id' => $achieve_id,
                        'type' => isset($rule['type']) ? $rule['type'] : '',
                        'data' => $rule_data
                    );
                    $id = isset($rule['id']) && $rule['id'] > 0 ? $rule['id'] : 0;
                    $this->save($id, $data);
                }
            }
        }
    }

    public function delete($id)
    {
        $this->db->delete('achieve_rules', array('id' => $id));
    }

    public function save($id, $data)
    {
        if ($id > 0) {
            $this->db->update('achieve_rules', $data, array('id' => $id));
        } else {
            $this->db->insert('achieve_rules', $data);
        }
    }

    public function update($id, $data) {
        $this->db->update('achieve_rules', $data, array('id' => $id));
    }
}