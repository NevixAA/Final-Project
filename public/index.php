<?php

/**
 * Front controller
 *
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Sessions
 */
session_start();

/**
 * Routing
 */
$router = new Core\Router();


/**
 * Femin Pages
 */
/**Home page**/
$router->add('', ['controller' => 'Home', 'action' => 'index']);


/**Login**/
$router->add('login', ['controller' => 'Login', 'action' => 'new']);


/**Logout**/
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);


/**Signup**/
$router->add('signup', ['controller' => 'Signup', 'action' => 'new']);


/**User details */
$router->add('user-details', ['controller' => 'userDetails', 'action' => 'index']);
$router->add('orders-history', ['controller' => 'userDetails', 'action' => 'ordersHistory']);


/** statute */
$router->add('statute', ['controller' => 'Statute', 'action' => 'index']);


/**about */
$router->add('about', ['controller' => 'About', 'action' => 'index']);


/** QandA */
$router->add('QandA', ['controller' => 'QandA', 'action' => 'index']);


/**Contact**/
$router->add('contact', ['controller' => 'Contact', 'action' => 'index']);
$router->add('contact/sendEmail', ['controller' => 'Contact', 'action' => 'sendEmail']);
$router->add('contact/success', ['controller' => 'Contact', 'action' => 'emailSentSuccess']);


/**Categories**/
$router->add('categories', ['controller' => 'Categories', 'action' => 'index']); 
$router->add('categories/product-imgs', ['controller' => 'Categories', 'action' => 'getImgsByColorSizeId']); 
$router->add('categories/{name}', ['controller' => 'Categories', 'action' => 'findByName']); 
$router->add('categories/{name}/{id:\d+}', ['controller' => 'Categories', 'action' => 'getProductById']); 


/**Shopping cart**/
$router->add('cart', ['controller' => 'Cart', 'action' => 'index']); 
$router->add('cart/add', ['controller' => 'Cart', 'action' => 'add']);  
$router->add('cart/clear', ['controller' => 'Cart', 'action' => 'clear']); 
$router->add('cart/remove_product', ['controller' => 'Cart', 'action' => 'removeProductFromCart']); 
$router->add('cart/products', ['controller' => 'Cart', 'action' => 'getCartProducts']);
$router->add('cart/findProductQntty', ['controller' => 'Cart', 'action' => 'findProductQntty']);
$router->add('cart/isProductInStockAJAX', ['controller' => 'Cart', 'action' => 'isProductInStockAJAX']);
$router->add('cart/getAllowedMaxQnty', ['controller' => 'Cart', 'action' => 'getAllowedMaxQnty']);
$router->add('cart/update', ['controller' => 'Cart', 'action' => 'updateProductQntty']);
$router->add('cart/set', ['controller' => 'Cart', 'action' => 'set']);
$router->add('cart/success', ['controller' => 'Cart', 'action' => 'success']);
$router->add('cart/total_price', ['controller' => 'Cart', 'action' => 'getCartItemsTotalPrice']);


/**supervisor */
$router->add('supervisor', ['namespace' => 'OrderSupervisor','controller' => 'SupervisorController', 'action' => 'index']);
$router->add('supervisor/approve/{id:\d+}', ['namespace' => 'OrderSupervisor','controller' => 'OrderController', 'action' => 'approve']);
$router->add('supervisor/send/{id:\d+}', ['namespace' => 'OrderSupervisor','controller' => 'SupervisorController', 'action' => 'send']);
$router->add('supervisor/sendToSupplier/{id:\d+}', ['namespace' => 'OrderSupervisor','controller' => 'SupervisorController', 'action' => 'sendToSupplier']);
$router->add('supervisor/order', ['namespace' => 'OrderSupervisor','controller' => 'OrderController', 'action' => 'index']);
$router->add('supervisor/orderFromSupplier', ['namespace' => 'OrderSupervisor','controller' => 'SupervisorController', 'action' => 'orderFromSupplier']);




/**
 * Admin Dashboard Pages
 */
$router->add('admin', ['namespace' => 'Admin','controller' => 'HomeController', 'action' => 'index']); 

/* Settings */
$router->add('admin/settings', ['namespace' => 'Admin','controller' => 'SettingsController', 'action' => 'index']);
$router->add('admin/settings/edit/{id:\d+}', ['namespace' => 'Admin','controller' => 'SettingsController', 'action' => 'edit']);
$router->add('admin/settings/create', ['namespace' => 'Admin','controller' => 'SettingsController', 'action' => 'create']);


/* Discount */
$router->add('admin/discounts', ['namespace' => 'Admin','controller' => 'DiscountsController', 'action' => 'index']);
$router->add('admin/discounts', ['namespace' => 'Admin','controller' => 'DiscountsController', 'action' => 'create']);
$router->add('admin/discounts/delete', ['namespace' => 'Admin','controller' => 'DiscountsController', 'action' => 'delete']);
$router->add('admin/discounts/active_dscnts', ['namespace' => 'Admin','controller' => 'DiscountsController', 'action' => 'getActvSiteDscnts']);


/**Products**/
$router->add('admin/products', ['namespace' => 'Admin','controller' => 'ProductsController', 'action' => 'index']); 
$router->add('admin/products/delete/{id:\d+}', ['namespace' => 'Admin','controller' => 'ProductsController', 'action' => 'delete']); 
$router->add('admin/products/export_txt', ['namespace' => 'Admin','controller' => 'ProductsController', 'action' => 'exportTxtAction']); 
$router->add('admin/products/create', ['namespace' => 'Admin','controller' => 'ProductsController', 'action' => 'create']); 
$router->add('admin/products/edit/{id:\d+}', ['namespace' => 'Admin','controller' => 'ProductsController', 'action' => 'edit']); 

/**Search products**/
$router->add('admin/products/search/id', ['namespace' => 'Admin','controller' => 'ProductsController', 'action' => 'searchID']); 
$router->add('admin/products/search/category', ['namespace' => 'Admin','controller' => 'ProductsController', 'action' => 'searchCategory']); 
$router->add('admin/products/search/name', ['namespace' => 'Admin','controller' => 'ProductsController', 'action' => 'searchName']); 


/**Users**/
$router->add('admin/users', ['namespace' => 'Admin','controller' => 'UsersController', 'action' => 'users']); 
$router->add('admin/users/delete/{id:\d+}', ['namespace' => 'Admin','controller' => 'UsersController', 'action' => 'delete']); 
$router->add('admin/users/edit/{id:\d+}', ['namespace' => 'Admin','controller' => 'UsersController', 'action' => 'edit']); 

/**Admin suppliers**/
$router->add('admin/suppliers',['namespace'=>'Admin','controller'=>'SupplierController','action'=>'index']);
$router->add('admin/suppliers/create', ['namespace' => 'Admin','controller' => 'SupplierController', 'action' => 'create']); 
$router->add('admin/suppliers/export_txt', ['namespace' => 'Admin','controller' => 'SupplierController', 'action' => 'exportTxtAction']);
$router->add('admin/suppliers/delete/{id:\d+}', ['namespace' => 'Admin','controller' => 'SupplierController', 'action' => 'delete']); 
$router->add('admin/suppliers/edit/{id:\d+}', ['namespace' => 'Admin','controller' => 'SupplierController', 'action' => 'edit']); 

/** Search suppliers **/
$router->add('admin/suppliers/search/companyname', ['namespace' => 'Admin','controller' => 'SupplierController', 'action' => 'searchCompanyName']); 
$router->add('admin/suppliers/search/id', ['namespace' => 'Admin','controller' => 'SupplierController', 'action' => 'searchID']); 
$router->add('admin/suppliers/search/contactname', ['namespace' => 'Admin','controller' => 'SupplierController', 'action' => 'searchContactName']); 
$router->add('admin/suppliers/search/email', ['namespace' => 'Admin','controller' => 'SupplierController', 'action' => 'searchEmail']);



/**
 * Get statistics information
 */
$router->add('statistics/users', ['controller' => 'Statistics', 'action' => 'activeUsers']);
$router->add('statistics/new_users', ['controller' => 'Statistics', 'action' => 'newUsers']);
$router->add('statistics/new_orders', ['controller' => 'Statistics', 'action' => 'newOrders']);
$router->add('statistics/sold_products', ['controller' => 'Statistics', 'action' => 'soldProducts']);
$router->add('statistics/categories_statistics', ['controller' => 'Statistics', 'action' => 'categoriesStats']);





$router->add('{controller}/{action}', ['namespace' => 'Admin']);
$router->add('{controller}/{action}');

$router->dispatch($_SERVER['QUERY_STRING']);
