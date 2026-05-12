<?php

namespace App\Http\Controllers\Workers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function index()
    {
        return inertia('Workers/Dashboard');
    }
}
