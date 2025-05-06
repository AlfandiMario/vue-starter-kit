<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeviceController extends Controller
{
    /**
     * Display a listing of devices.
     * This method can be accessed by anyone with 'view devices' permission.
     */
    public function index()
    {
        $devices = Device::all();
        
        return Inertia::render('Devices/Index', [
            'devices' => $devices,
            'canControl' => auth()->user()->hasPermissionTo('control devices')
        ]);
    }

    /**
     * Show the form for creating a new device.
     * Only users with 'control devices' permission can create devices.
     */
    public function create()
    {
        return Inertia::render('Devices/Create');
    }

    /**
     * Store a newly created device in storage.
     * Only users with 'control devices' permission can create devices.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        // Set initial status to 'online' when creating a device
        $validated['status'] = 'online';
        
        // Create the device in the database
        Device::create($validated);
        
        return redirect()->route('devices.index')
            ->with('success', 'Device created successfully.');
    }

    /**
     * Display the specified device.
     * This method can be accessed by anyone with 'view devices' permission.
     */
    public function show(Device $id)
    {
        // Format device data for the frontend
        $device = [
            'id' => $id->id,
            'name' => $id->name,
            'status' => $id->status,
            'type' => $id->type,
            'details' => [
                'manufacturer' => $id->manufacturer,
                'model' => $id->model,
                'firmware' => $id->firmware_version,
                'location' => $id->location,
            ]
        ];
        
        return Inertia::render('Devices/Show', [
            'device' => $device,
            'canControl' => auth()->user()->hasPermissionTo('control devices')
        ]);
    }

    /**
     * Show the form for editing the specified device.
     * Only users with 'control devices' permission can edit devices.
     */
    public function edit(Device $device)
    {
        return Inertia::render('Devices/Edit', [
            'device' => $device
        ]);
    }

    /**
     * Update the specified device in storage.
     * Only users with 'control devices' permission can update devices.
     */
    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'status' => 'nullable|string|in:online,offline',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'firmware_version' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);
        
        $device->update($validated);
        
        return redirect()->route('devices.index')
            ->with('success', 'Device updated successfully.');
    }

    /**
     * Remove the specified device from storage.
     * Only users with 'control devices' permission can delete devices.
     */
    public function destroy(Device $device)
    {
        $device->delete();
        
        return redirect()->route('devices.index')
            ->with('success', 'Device deleted successfully.');
    }

    /**
     * Control a device (turn on/off, adjust settings).
     * Only users with 'control devices' permission can control devices.
     */
    public function control(Request $request, Device $id)
    {
        $validated = $request->validate([
            'action' => 'required|string|in:on,off,adjust',
            'settings' => 'nullable|array',
        ]);

        // Update device status based on action
        if ($validated['action'] === 'on') {
            $id->status = 'online';
            $id->save();
        } elseif ($validated['action'] === 'off') {
            $id->status = 'offline';
            $id->save();
        } elseif ($validated['action'] === 'adjust' && isset($validated['settings'])) {
            // Store settings in the settings JSON column
            $id->settings = $validated['settings'];
            $id->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => "Device {$id->id} action {$validated['action']} performed successfully"
        ]);
    }
}
