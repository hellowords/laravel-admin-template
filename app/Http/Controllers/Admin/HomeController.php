<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index ()
    {
        $users = User::count();
        $orders = '66,342';
        $checking_orders = '7,053';
        $done_orders = '2,000';
        return view('admin.dashbord',compact('users','orders','checking_orders','done_orders'));
    }
}
