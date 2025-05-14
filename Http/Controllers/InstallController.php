<?php

namespace Modules\MiniReportB1\Http\Controllers;

use App\System;
use Composer\Semver\Comparator;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstallController extends Controller
{
   
    public function __construct()
    {
        $this->module_name = 'minireportb1';
        $this->appVersion = config('minireportb1.module_version');
        $this->module_display_name = 'MiniReportB1';
    }

    /**
     * Install
     *
     * @return Response
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $this->installSettings();

        // Check if installed or not
        $is_installed = System::getProperty($this->module_name . '_version');
        if (empty($is_installed)) {
            DB::statement('SET default_storage_engine=INNODB;');
            Artisan::call('module:migrate', ['module' => 'MiniReportB1', '--force' => true]);
            System::addProperty($this->module_name . '_version', $this->appVersion);
        }

        $action_url = action([\Modules\MiniReportB1\Http\Controllers\InstallController::class, 'install']);
        $intruction_type = 'uf';
        $action_type = 'install';
        $module_display_name = $this->module_display_name;
        return view('install.install-module')
            ->with(compact('action_url', 'intruction_type', 'action_type', 'module_display_name'));
    }

    /**
     * Initialize all install functions
     */
    private function installSettings()
    {
        config(['app.debug' => true]);
        Artisan::call('config:clear');
        Artisan::call('optimize:clear');
    }

    /**
     * Update module
     *
     * @return Response
     */
    public function update()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');

            $modulecreatemodule_version = System::getProperty($this->module_name . '_version');

            if (Comparator::greaterThan($this->appVersion, $modulecreatemodule_version)) {
                $this->installSettings();

                DB::statement('SET default_storage_engine=INNODB;');
                Artisan::call('module:migrate', ['module' => 'MiniReportB1', '--force' => true]);

                System::setProperty($this->module_name . '_version', $this->appVersion);
            } else {
                abort(404);
            }

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => 'MiniReportB1 module updated successfully to version ' . $this->appVersion . ' !!',
            ];

            return redirect()
                ->action([\App\Http\Controllers\HomeController::class, 'index'])
                ->with('status', $output);
        } catch (\Exception $e) {
            DB::rollBack();

            $output = [
                'success' => false,
                'msg' => $e->getMessage(),
            ];

            return redirect()->back()->with(['status' => $output]);
        }
    }

    public function install()
    {
        try {

          
            // Optionally set the database engine, if needed (usually not necessary as InnoDB is default)
            DB::statement('SET default_storage_engine=INNODB;');
            // Always run migrations to ensure tables are up to date
            Artisan::call('migrate:refresh', [
                '--path' => 'Modules/MiniReportB1/Database/Migrations',
                '--force' => true
            ]);

            // Publish module assets or configurations
            Artisan::call('module:publish', ['module' => 'MiniReportB1']);

            // Optimize the application clearing cache
            Artisan::call('optimize:clear');

            $output = ['success' => 1, 'msg' => 'MiniReportB1 module installed successfully'];
        } catch (\Exception $e) {

            $output = ['success' => false, 'msg' => $e->getMessage()];
        }

        return redirect()
            ->action([\App\Http\Controllers\Install\ModulesController::class, 'index'])
            ->with('status', $output);
    }

    private function shouldResetMigrations()
    {
        $tables = ['modulecreator'];
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Uninstall
     *
     * @return Response
     */
    public function uninstall()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            System::removeProperty($this->module_name . '_version');

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            $output = [
                'success' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }
}