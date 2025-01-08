<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnauthorizedAccessController extends Controller
{
    public function index() {
        return view('admin.unauthorized.unauthorized');
    }
}
