<?php

class shop86Admin {
  
  public function productsPage() {
    $data = array();
    $subscriptions = array('0' => 'None');
    
    if(class_exists('SpreedlySubscription')) {
      $spreedlySubscriptions = SpreedlySubscription::getSubscriptions();
      foreach($spreedlySubscriptions as $s) {
        $subs[(int)$s->id] = (string)$s->name;
      }
      if(count($subs)) {
        asort($subs);
        foreach($subs as $id => $name) {
          $subscriptions[$id] = $name;
        }
      }
    }
    else {
      shop86Opt::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Not loading Spreedly data because Spreedly class has not been loaded");
    }
    
    $data['subscriptions'] = $subscriptions;
    $view = shop86Opt::getView('admin/products.php', $data);
    echo $view;
  }
  
  public function settingsPage() {
    $tabs = array(
      'main' => array('tab' => 'Main', 'title' => ''),
      'tax' => array('tab' => 'Tax', 'title' => ''),
      'cart_checkout' => array('tab' => 'Cart & Checkout', 'title' => ''),
      'gateways' => array('tab' => 'Gateways', 'title' => ''),
      'notifications' => array('tab' => 'Notifications', 'title' => ''),
      'integrations' => array('tab' => 'Integrations', 'title' => ''),
      'debug' => array('tab' => 'Debug', 'title' => '')
    );
    $setting = new shop86Setting($tabs);
    $data = array(
      'setting' => $setting
    );
    $view = shop86Opt::getView('admin/settings.php', $data);
    echo $view;
  }
  
  public function notificationsPage() {
    $view = shop86Opt::getView('admin/notifications.php');
    echo $view;
  }
  
  public function ordersPage() {
    if($_SERVER['REQUEST_METHOD'] == 'GET' && shop86Opt::getVal('task') == 'view') {
      $order = new shop86Order($_GET['id']);
      $view = shop86Opt::getView('admin/order-view.php', array('order'=>$order));
    }
    elseif($_SERVER['REQUEST_METHOD'] == 'POST' && shop86Opt::postVal('task') == 'resend email receipt') {
      if(shop86_PRO && shop86Setting::getValue('enable_advanced_notifications') == 1) {
        $notify = new shop86AdvancedNotifications($_POST['order_id']);
        $notify->sendAdvancedEmailReceipts(false);
      }
      else {
        $notify = new shop86Notifications($_POST['order_id']);
        $notify->sendEmailReceipts();
      }
      $order = new shop86Order($_POST['order_id']);
      $view = shop86Opt::getView('admin/order-view.php', array('order'=>$order, 'resend'=>true));
    }
    elseif($_SERVER['REQUEST_METHOD'] == 'POST' && shop86Opt::postVal('task') == 'reset download amount') {
      $product = new shop86Product();
      $product->resetDownloadsForDuid($_POST['duid'], $_POST['order_item_id']);
      $order = new shop86Order($_POST['order_id']);
      $view = shop86Opt::getView('admin/order-view.php', array('order'=>$order));
    }
    elseif($_SERVER['REQUEST_METHOD'] == 'GET' && shop86Opt::getVal('task') == 'delete') {
      $order = new shop86Order($_GET['id']);
      $order->deleteMe();
      $view = shop86Opt::getView('admin/orders.php');
    }
    elseif($_SERVER['REQUEST_METHOD'] == 'POST' && shop86Opt::postVal('remove') && shop86Opt::postVal('remove') != 'all') {
      $order = new shop86Order($_GET['id']);
      shop86AdvancedNotifications::removeTrackingNumber($order);
      $order = new shop86Order($_GET['id']);
      $view = shop86Opt::getView('admin/order-view.php', array('order'=>$order));
    }
    elseif($_SERVER['REQUEST_METHOD'] == 'POST' && shop86Opt::postVal('remove') == 'all') {
      $order = new shop86Order($_GET['id']);
      $order->updateTracking(null);
      $order = new shop86Order($_GET['id']);
      $view = shop86Opt::getView('admin/order-view.php', array('order'=>$order));
    }
    elseif($_SERVER['REQUEST_METHOD'] == 'POST' && shop86Opt::postVal('task') == 'update order status') {
      $order = new shop86Order($_POST['order_id']);
      //$order->updateStatus(shop86Opt::postVal('status'));
      //$order->updateNotes($_POST['notes']);
      $data = array(
        'status' => shop86Opt::postVal('status'),
        'notes' => shop86Opt::postVal('notes')
      );
      $order->setData($data);
      $order->save();
      if(shop86Opt::postVal('send_email_status_update') && shop86_PRO) {
        shop86AdvancedNotifications::addTrackingNumbers($order);
        $status = shop86Opt::postVal('status');
        if(shop86Setting::getValue('status_options') != null) {
          $notify = new shop86AdvancedNotifications($_POST['order_id']);
          $notify->sendStatusUpdateEmail($status);
        }
      }
      elseif(shop86_PRO) {
        shop86AdvancedNotifications::addTrackingNumbers($order);
      }
      $view = shop86Opt::getView('admin/orders.php');
      //$order = new shop86Order($_POST['order_id']);
      //$view = shop86Common::getView('admin/order-view.php', array('order'=>$order));
    }
    else {
      $view = shop86Opt::getView('admin/orders.php');
    }

    echo $view;
  }

  public function inventoryPage() {
    $view = shop86Opt::getView('admin/inventory.php');
    echo $view; 
  }

  public function promotionsPage() {
    $view = shop86Opt::getView('admin/promotions.php');
    echo $view;
  }

  public function shippingPage() {
    $view = shop86Opt::getView('admin/shipping.php');
    echo $view;
  }

  public function reportsPage() {
    $view = shop86Opt::getView('admin/reports.php');
    echo $view;
  }
  
  public function shop86Help() {
    $setting = new shop86Setting();
    define('HELP_URL', "http://www.shop86.com/shop86-help/?order_number=".shop86Setting::getValue('order_number'));
    $view = shop86Opt::getView('admin/help.php');
    echo $view;
  }
  
  public function paypalSubscriptions() {
    $data = array();
    if(shop86_PRO) {
      $sub = new shop86PayPalSubscription();
      $data['subscription'] = $sub;

      if($_SERVER['REQUEST_METHOD'] == 'POST' && shop86Opt::postVal('shop86-action') == 'save paypal subscription') {
        $subData = shop86Opt::postVal('subscription');
        $subData['setup_fee'] = isset($subData['setup_fee']) ? shop86Opt::convert_currency_to_number($subData['setup_fee']) : '';
        $subData['price'] = isset($subData['price']) ? shop86Opt::convert_currency_to_number($subData['price']) : '';
        $sub->setData($subData);
        $errors = $sub->validate();
        if(count($errors) == 0) {
          $sub->save();
          $sub->clear();
          $data['subscription'] = $sub;
        }
        else {
          $data['errors'] = $sub->getErrors();
          $data['jqErrors'] = $sub->getJqErrors();
        }
      }
      else {
        if(shop86Opt::getVal('task') == 'edit' && isset($_GET['id'])) {
          $sub->load(shop86Opt::getVal('id'));
          $data['subscription'] = $sub;
        }
        elseif(shop86Opt::getVal('task') == 'delete' && isset($_GET['id'])) {
          $sub->load(shop86Opt::getVal('id'));
          $sub->deleteMe();
          $sub->clear();
          $data['subscription'] = $sub;
        }
      }

      $data['plans'] = $sub->getModels('where is_paypal_subscription>0', 'order by name', '1');
      $view = shop86Opt::getView('pro/admin/paypal-subscriptions.php', $data);
      echo $view;
    }
    else {
      echo '<h2>PayPal Subscriptions</h2><p class="description">This feature is only available in <a href="http://shop86.com">shop86 Professional</a>.</p>';
    }
    
  }
  
  public function accountsPage() {
    $data = array();
    if(shop86_PRO) {
      $data['plan'] = new shop86AccountSubscription();
      $data['activeUntil'] = '';
      $account = new shop86Account();

      if(isset($_REQUEST['shop86-action']) && $_REQUEST['shop86-action'] == 'delete_account') {
        // Look for delete request
        if(isset($_REQUEST['accountId']) && is_numeric($_REQUEST['accountId'])) {
          $account = new shop86Account($_REQUEST['accountId']);
          $account->deleteMe();
          $account->clear();
        }
      }
      elseif(isset($_REQUEST['accountId']) && is_numeric($_REQUEST['accountId'])) {
        if(isset($_REQUEST['opt_out'])) {
          $account = new shop86Account();
          $account->load($_REQUEST['accountId']);
          $data = array(
            'opt_out' => $_REQUEST['opt_out']
          );
          $account->setData($data);
          $account->save();
          $account->clear();
        }
        // Look in query string for account id
        $account = new shop86Account();
        $account->load($_REQUEST['accountId']);
        $id = $account->getCurrentAccountSubscriptionId(true);
        $data['plan'] = new shop86AccountSubscription($id); // Return even if plan is expired
        if(date('Y', strtotime($data['plan']->activeUntil)) <= 1970) {
          $data['activeUntil'] = '';
        }
        else {
          $data['activeUntil'] = date('m/d/Y', strtotime($data['plan']->activeUntil));
        }
      }

      if($_SERVER['REQUEST_METHOD'] == 'POST' && shop86Opt::postVal('shop86-action') == 'save account') {
        $acctData = $_POST['account'];

        // Format or unset password
        if(empty($acctData['password'])) {
          unset($acctData['password']);
        }
        else {
          $acctData['password'] = md5($acctData['password']);
        }

        // Strip HTML tags on notes field
        $acctData['notes'] = strip_tags($acctData['notes'], '<a><strong><em>');

        $planData = $_POST['plan'];
        $planData['active_until'] = date('Y-m-d 00:00:00', strtotime($planData['active_until']));

        // Updating an existing account
        if($acctData['id'] > 0) {
          $account = new shop86Account($acctData['id']);
          $account->setData($acctData);
          $account_errors = $account->validate();
          
          $sub = new shop86AccountSubscription($planData['id']);
          if($planData['product_id'] != 'spreedly_subscription') {
            $sub->setData($planData);
            $subscription_product = new shop86Product($sub->product_id);
            $sub->subscription_plan_name = $subscription_product->name;
            $sub->feature_level = $subscription_product->feature_level;
            $sub->subscriber_token = '';
          }
          else {
            unset($planData['product_id']);
            $sub->setData($planData);
          }
          $subscription_errors = $sub->validate();
          $errors = array_merge($account_errors, $subscription_errors);

          if(count($errors) == 0) {
            $account->save();
            $sub->save();
            $account->clear();
            $sub->clear();
          }
          else {
            $data['errors'] = $errors;
            $data['plan'] = $sub;
            $data['activeUntil'] = date('m/d/Y', strtotime($sub->activeUntil));
          }
        }
        else {
          // Creating a new account
          $account = new shop86Account();
          $account->setData($acctData);
          $account_errors = $account->validate();
          
          if(count($account_errors) == 0){
            $sub = new shop86AccountSubscription();
            $sub->setData($planData); 
            $subscription_errors = $sub->validate();
            
            if(count($subscription_errors) == 0){
              $account->save();

              $sub->billingFirstName = $account->firstName;
              $sub->billingLastName = $account->lastName;
              $sub->billingInterval = 'Manual';
              $sub->account_id = $account->id;
              $subscription_product = new shop86Product($sub->product_id);
              $sub->subscription_plan_name = $subscription_product->name;
              $sub->feature_level = $subscription_product->feature_level;
              $sub->save();
              $account->clear();
              $data['just_saved'] = true;
            }
            else{
              $data['errors'] = $subscription_errors;
            }
            
          }
          else{
            $data['errors'] = $account_errors;
          }
          
        }

      }

      $data['url'] = shop86Opt::replaceQueryString('page=shop86-accounts');
      $data['account'] = $account;
    }
    
    $view = shop86Opt::getView('admin/accounts.php', $data);
    echo $view;
  }
}