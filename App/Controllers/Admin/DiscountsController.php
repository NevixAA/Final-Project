<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Flash;
use \App\Auth;
use \App\Models\Category;
use \App\Models\Discount;



class DiscountsController extends AdminAuthenticated
{
    // Render home page
    public function indexAction($data = null)
    {
        if ($data == null)
            $data = [];
        $data['categories']        = Category::getAllCategories();
        $data['site_discounts']    = Discount::getAllStoreDiscounts();
        $data['cat_discounts']     = Discount::getDiscounts();
        $data['active_discounts']  = Discount::getActiveDiscounts();
        View::renderTemplate('Admin/Discounts/index.html',$data);
    }


    // Add new discont
    public function createAction()
    {   
        $site = isset($_POST['add_site_dscnt']) ? 'site_' : '';
        if (isset($_POST['add_dscnt']) || !empty($site)) {
            $dscnt = new Discount($_POST);
            if ($dscnt->save()) {
                Flash::addMessage('ההנחה נוספה בהצלחה',Flash::SUCCESS);
                $this->redirect('/admin/discounts');
            }else {
                $this->index([
                    'errors'          => $dscnt->errors,
                    'dscntId'         => $dscnt->dscntId,
                    $site.'percent'   => $dscnt->percent,
                    'from_item'       => $dscnt->from_item ?? null,
                    $site.'end_date'  => $dscnt->end_date
                ]);
            }
        }else {
            $this->index();
        }
    }


    // Remove discount
    public function deleteAction()
    {
        if (isset($_POST['del_dscnt'])) {
            Discount::removeDiscount($_POST['dscntId'],$_POST['catId']);
            Flash::addMessage('ההנחה נמחקה בהצלחה!',Flash::SUCCESS);
            $this->redirect('/admin/discounts');
        }
    }

    
    /**
     * Return site discount 
     * Response to AJAX Request
     */
    public function getActvSiteDscnts()
    {
        $dscnts = Discount::getActiveDiscounts();
        header('Content-Type: application/json');
        echo json_encode($dscnts[0]);
        // var_dump($dscnts[0]);

    }

}
