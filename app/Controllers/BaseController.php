<?php

namespace App\Controllers;

use App\Models\SettingsModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = [];

    private $settings;
    private $exambrosettings;
    private $appSetting;
    private $exambroSetting;
    protected $session;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $controller = service('router')->controllerName();

        // Jika bukan controller Install dan DB tidak connect, redirect ke /install
        if (!str_contains($controller, 'InstallController')) {
            if (!$this->isDbConnected()) {
                return redirect()->to(base_url('install'))->send(); // aman dari loop
            }

            $cache = service('cache');

            // 1. App Setting
            try {
                $this->appSetting = $cache->get('app_setting');
            } catch (\Throwable $e) {
                log_message('error', 'Redis get app_setting failed, falling back to DB: ' . $e->getMessage());
                $this->appSetting = null;
            }

            if ($this->appSetting === null) {
                $this->settings = new \App\Models\SettingsModel();
                $this->appSetting = $this->settings->get_by_id(1);
                try {
                    $cache->save('app_setting', $this->appSetting, 86400); // 24 hours
                } catch (\Throwable $e) {
                    // Ignore cache write error
                }
            }

            // 2. Exambro Setting
            try {
                $this->exambroSetting = $cache->get('exambro_setting');
            } catch (\Throwable $e) {
                log_message('error', 'Redis get exambro_setting failed, falling back to DB: ' . $e->getMessage());
                $this->exambroSetting = null;
            }

            if ($this->exambroSetting === null) {
                $this->exambrosettings = new \App\Models\ExambroSettingModel();
                $this->exambroSetting = $this->exambrosettings->getExambroSetting(1);
                try {
                    $cache->save('exambro_setting', $this->exambroSetting, 86400); // 24 hours
                } catch (\Throwable $e) {
                    // Ignore cache write error
                }
            }
        }
    }

    protected function isDbConnected(): bool
    {
        try {
            $db = \Config\Database::connect();
            $db->initialize();
            $db->query("SELECT 1");
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function appSetting()
    {
        return $this->appSetting;
    }
    protected function exambroSetting()
    {
        return $this->exambroSetting;
    }
}
