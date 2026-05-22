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


            $this->settings = new \App\Models\SettingsModel();
            $this->exambrosettings = new \App\Models\ExambroSettingModel();

            $this->appSetting = $this->settings->get_by_id(1);
            $this->exambroSetting = $this->exambrosettings->getExambroSetting(1);
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
