<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ElectricalController extends Controller
{
    /**
     * Display the electrical system overview page.
     */
    public function index()
    {
        return Inertia::render('electrical/Index');
    }

    /**
     * Display the monitor page.
     */
    public function monitor()
    {
        return Inertia::render('electrical/Monitor');
    }

    /**
     * Display the device control page.
     */
    public function control()
    {
        // In a real application, you would fetch devices from the database
        // and organize them by room/location
        
        return Inertia::render('electrical/Control');
    }

    /**
     * Display the stats page.
     */
    public function stats()
    {
        return Inertia::render('electrical/Stats');
    }
    
    /**
     * Display the energy consumption index (IKE) page.
     */
    public function ike()
    {
        return Inertia::render('electrical/Ike');
    }
}
