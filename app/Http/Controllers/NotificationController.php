<?php

namespace App\Http\Controllers;

use App\Common;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function db_notifications()
    {
        return \Auth::user()->unreadNotifications();
    }

    public function all_notifications()
    {
        return $this->db_notifications()->get()->toArray();
    }

    public function notifications()
    {
        return $this->db_notifications()->limit(5)->get()->toArray();
    }

    public function index()
    {
        $max = config('const.app.max_paginate');
        $notifications = $this->db_notifications()->paginate(Common::maxPagination());
        return Common::withPagination(view('notifications.index', compact('notifications')));
    }
}
