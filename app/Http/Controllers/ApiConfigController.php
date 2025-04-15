<?php

namespace App\Http\Controllers;

use App\Models\ApiConfiguration;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ApiConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $configs = ApiConfiguration::all();

        return view('admin.api.index', [
            'configs' => $configs
        ]);
    }

    public function create()
    {
        return view('admin.api.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'api_key' => 'required|string',
            'base_url' => 'required|url',
            'is_active' => 'boolean',
            'parameters' => 'nullable|json'
        ]);

        ApiConfiguration::create($validated);

        return redirect()->route('admin.api.index')
            ->with('success', 'Konfigurasi API berhasil dibuat');
    }

    public function edit(ApiConfiguration $api)
    {
        return view('admin.api.edit', [
            'config' => $api
        ]);
    }

    public function update(Request $request, ApiConfiguration $api)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'api_key' => 'required|string',
            'base_url' => 'required|url',
            'is_active' => 'boolean',
            'parameters' => 'nullable|json'
        ]);

        $api->update($validated);

        return redirect()->route('admin.api.index')
            ->with('success', 'Konfigurasi API berhasil diperbarui');
    }

    public function destroy(ApiConfiguration $api)
    {
        $api->delete();

        return redirect()->route('admin.api.index')
            ->with('success', 'Konfigurasi API berhasil dihapus');
    }
}
