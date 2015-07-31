
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Achivment_rule_model extends CI_Model
{
    public function get_by_achive($achive_id, $limit = 0, $offset = 0)
    {
        $query = $this->db->get_where('achive_rules', array('achive_id' => $achive_id), $limit, $offset);

        return $query->result();
    }

    public function save_batch($achive_id, $rules)
    {
        if (is_array($rules)) {
            foreach ($rules as $rule) {
                if (isset($rule['deleted']) && $rule['deleted'] > 0 || !($rule['type'] > 0)) {
                    if (isset($rule['id']) && $rule['id'] > 0) {
                        $this->delete($rule['id']);
                    }
                } else {
                    $data = array(
                        'achive_id' => $achive_id,
                        'type' => isset($rule['type']) ? $rule['type'] : '',
                        'data' => isset($rule['data']) ? $rule['data'] : ''
                    );
                    $id = isset($rule['id']) && $rule['id'] > 0 ? $rule['id'] : 0;
                    $this->save($id, $data);
                }
            }
        }
    }

    public function delete($id)
    {
        $this->db->delete('achive_rules', array('id' => $id));
    }

    public function save($id, $data)
    {
        if ($id > 0) {
            $this->db->update('achive_rules', $data, array('id' => $id));
        } else {
            $this->db->insert('achive_rules', $data);
        }
    }

    public function update($id, $data) {
        $this->db->update('achive_rules', $data, array('id' => $id));
    }
}