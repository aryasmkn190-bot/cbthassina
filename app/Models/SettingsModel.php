<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{

    var $table = 'settings';

    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        // $db = \Config\Database::connect();
    }

    public function get_all_settingss()
    {
        //       $query = $this->db->table('settingss');
        $query = $this->db->query('select * from settingss');
        //      print_r($query->getResult());
        // $query = $this->db->get();
        return $query->getResult();
    }

    public function get_by_id($id)
    {

        $query =  $this->db->table($this->table)
            ->select('settings.*')
            ->where('settings.id', $id)
            ->get();

        return $query->getRow();
    }

    public function settings_add($data)
    {

        $query = $this->db->table($this->table)->insert($data);

        return $this->db->insertID();
    }

    public function settings_update($where, $data)
    {
        $this->db->table($this->table)->update($data, $where);
        //        print_r($this->db->getLastQuery());
        return $this->db->affectedRows();
    }

    public function delete_by_id($id)
    {
        $this->db->table($this->table)->delete(array('id' => $id));
    }
}
