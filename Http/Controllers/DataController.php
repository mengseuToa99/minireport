<?php

namespace Modules\MiniReportB1\Http\Controllers;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use DB;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Dynamically add menu item for the module to the admin sidebar.
     *
     * @param string $moduleName
     * @return void
     */
    public function modifyAdminMenu()
    {
        // Get the business ID from the session
        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();

        // Check if the module is enabled in the business subscription
        $is_module_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'minireportb1_module');


        $commonUtil = new Util();
        $is_admin = $commonUtil->is_admin(auth()->user(), $business_id);

        if ($is_module_enabled) {
            // Modify the admin sidebar menu
            Menu::modify(
                'admin-sidebar-menu',
                function ($menu){                              
                    // Dynamically add menu item for the module
                    $menu->url(
                        action([\Modules\MiniReportB1\Http\Controllers\MiniReportB1Controller::class, 'index' ]), 
                        __("minireportb1::lang.minireportb1"), 
                        ['icon' => "fa fa-envelope-open-text", 'style' => 'font-size: 16px; color:#1430ff;', 'aria-hidden' => 'true', 'active' => request()->segment(1) == "minireportb1"]
                    )->order(34);
                }
            );
        }
    }

    /**
     * Creates the menu dynamically for the given module.
     *
     * @param string $moduleName
     * @return void
     */
    public function user_permissions()
    {
        return [
            [
                'value' => 'minireportb1.view_minireportb1',
                'label' => __('minireportb1::lang.create_MiniReportB1'),
                'default' => false,
            ],
            [
                'value' => 'minireportb1.create_minireportb1',
                'label' => __('minireportb1::lang.create_MiniReportB1'),
                'default' => false,
            ],
            [
                'value' => 'minireportb1.edit_minireportb1',
                'label' => __('minireportb1::lang.edit_minireportb1'),
                'default' => false,
            ],
            [
                'value' => 'minireportb1.delete_minireportb1',
                'label' => __('minireportb1::lang.delete_minireportb1'),
                'default' => false,
            ],
        ];
    }

    /**
     * Superadmin package permissions
     *
     * @return array
     */
    public function superadmin_package()
    {
        return [
            [
                'name' => 'minireportb1_module',
                'label' => __('minireportb1::lang.minireportb1_module'),
                'default' => false,
            ],
        ];
    }
}