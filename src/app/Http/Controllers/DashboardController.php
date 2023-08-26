<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Package;
use App\Models\Supplier;
use App\Services\DashboardService;
use stdClass;

class DashboardController extends Controller
{
    public function index(){
        return view('dashboard.home',[
            'dashboard' => $this->getStats()
        ]);
    }

    public function getStats(){
        
        $result = new stdClass;

        $result->server_information = $this->serverInformation();
        $result->customers = Customer::count();
        $result->drivers = Customer::count();   
        $result->orders = Order::count();   
        $result->purchases = Purchase::count();   
        $result->packages = Package::count();   
        $result->suppliers = Supplier::count();  
        
        $res = json_decode(json_encode($result), true);

        return $res;
    }

    private function serverInformation(){
        $serverName = request()->server();
        $server = [];

        $server['web_server'] = $serverName['SERVER_SOFTWARE'];
        $server['http_user_agent'] = $serverName['HTTP_USER_AGENT'];
        $server['gateway_interface'] = $serverName['GATEWAY_INTERFACE'];
        $server['server_protocol'] = $serverName['SERVER_PROTOCOL'];
        $server['php_version'] = $serverName['PHP_VERSION'];
        $server['php_url'] = $serverName['PHP_URL'];
        $server['os'] = php_uname('s');
        $server['ar'] = php_uname('m');

        return $server;
    }

    
}
