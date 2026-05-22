<?php

namespace App\Models;

use CodeIgniter\Model;

class ExambroSettingModel extends Model
{
    protected $table = 'settings_exambro';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'logo_resource',
        'banner_img',
        'default_brightness',
        'bell_sound',
        'exit_sound',
        'app_volume',
        'school_name',
        'app_name',
        'version',
        'password_exit',
        'secret_code',
        'menu_url',
        'menu_scanqr',
        'bluetooth',
        'headset',
        'theme_color',
        'restrict_user_agent',
        'portal_ujian',
        'login_nopassword',
        'file_exam_config',
        'file_exam_config_upload',
        'user_agent',
        'informasi',
        'updated_at'
    ];

    public function getExambroSetting($id)
    {
        return $this->where('id', $id)->first();
    }
    public function updateExamConfig($id, string $filename)
    {
        return $this->update($id, [
            'file_exam_config' => $filename,
            'updated_at'       => date('Y-m-d H:i:s')
        ]);
    }
    public function updateExamConfigUpload($id, string $filename)
    {
        return $this->update($id, [
            'file_exam_config_upload' => $filename,
            'updated_at'       => date('Y-m-d H:i:s')
        ]);
    }

    public function updateExambroSetting($id, $data)
    {
        return $this->update($id, $data);
    }

    public function getFullConfigWithMenuAndBlock($id = 1)
    {
        $setting = $this->getExambroSetting($id);

        $blockModel = new \App\Models\ExambroBlockModel();
        $menuModel = new \App\Models\ExambroMenuModel();

        $blockedApps = $blockModel
            ->where('is_blocked', 1)
            ->findAll();

        $menus = $menuModel
            ->where('is_active', 1)
            ->orderBy('order', 'ASC')
            ->findAll();

        return [
            'setting' => $setting,
            'blocked_apps' => $blockedApps,
            'menus' => $menus
        ];
    }
}
