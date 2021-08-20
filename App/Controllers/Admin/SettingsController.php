<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Flash;
use \App\Models\Other;

class SettingsController extends AdminAuthenticated
{

    // Show all products in product table
    public function indexAction()
    {
        $settings = Other::getAllSettings();
        View::renderTemplate('Admin/Settings/index.html',[
            'settings' => $settings ?? null
        ]);
    }

    // Add new settings value
    public function createAction()
    {
        if(isset($_POST['setting-added'])) {
            $setting = new Other($_POST);
            if($setting->save()) { 
                Flash::addMessage('הגדרה התווספה בהצלחה', Flash::SUCCESS);
                $this->redirect('/admin/settings');
            }
            else {
                $this->renderAddSetting($setting); 
            }
        }
        else {
            $this->renderAddSetting(null);
        }   
    }

    // Edit setting
    public function editAction()
    {
        $success=Other::updateSettingValue($this->route_params['id'],$_POST['settingValue']);
        $settings = Other::getAllSettings();
        if ($success) {
            Flash::addMessage('הגדרה עודכנה בהצלחה', Flash::SUCCESS);
            View::renderTemplate('Admin/Settings/index.html',[
                'settings'=> $settings ?? null
            ]);
        }else {
            Flash::addMessage('עדכון הגדרה נכשל', Flash::WARNING);
            $this->redirect('/admin/settings');
        }
      
    }

    // Render specific setting
    public function renderAddSetting($setting)
    {
        View::renderTemplate('Admin/settings/add_setting.html',[
            'setting' => $setting ?? null,
        ]);
    }
}
?>


