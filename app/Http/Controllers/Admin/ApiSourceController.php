<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiSource;
use App\Models\News;
use App\Models\Category;
use App\Services\NewsApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ApiSourceController extends Controller
{
    protected $newsApiService;

    public function __construct(NewsApiService $newsApiService)
    {
        $this->newsApiService = $newsApiService;
    }

    public function index(Request $request)
    {
        // Base query
        $query = ApiSource::query();

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Apply sorting
        if ($request->has('sort') && !empty($request->sort)) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'most_news':
                    $query->orderByDesc('news_count');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        // Get paginated results
        $apiSources = $query->paginate(10)->withQueryString();

        return view('admin.api-sources.index', compact('apiSources'));
    }

    public function create()
    {
        return view('admin.api-sources.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:api_sources',
            'url' => 'required|url',
            'api_key' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'param_keys' => 'nullable|array',
            'param_values' => 'nullable|array',
        ]);

        // Process parameters
        $params = [];
        if ($request->has('param_keys') && $request->has('param_values')) {
            foreach ($request->param_keys as $index => $key) {
                if (!empty($key) && isset($request->param_values[$index])) {
                    $params[$key] = $request->param_values[$index];
                }
            }
        }

        $apiSource = new ApiSource([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'api_key' => $validated['api_key'],
            'status' => $validated['status'],
            'params' => $params,
            'news_count' => 0,
        ]);

        $apiSource->save();

        return redirect()->route('admin.api-sources.index')
            ->with('success', 'Sumber API berhasil ditambahkan.');
    }

    public function edit(ApiSource $apiSource)
    {
        return view('admin.api-sources.edit', compact('apiSource'));
    }

    public function update(Request $request, ApiSource $apiSource)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:api_sources,name,' . $apiSource->id,
            'url' => 'required|url',
            'api_key' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'param_keys' => 'nullable|array',
            'param_values' => 'nullable|array',
        ]);

        // Process parameters
        $params = [];
        if ($request->has('param_keys') && $request->has('param_values')) {
            foreach ($request->param_keys as $index => $key) {
                if (!empty($key) && isset($request->param_values[$index])) {
                    $params[$key] = $request->param_values[$index];
                }
            }
        }

        $apiSource->name = $validated['name'];
        $apiSource->url = $validated['url'];
        $apiSource->api_key = $validated['api_key'];
        $apiSource->status = $validated['status'];
        $apiSource->params = $params;

        $apiSource->save();

        return redirect()->route('admin.api-sources.index')
            ->with('success', 'Sumber API berhasil diperbarui.');
    }

    public function destroy(ApiSource $apiSource)
    {
        // Opsional: Hapus semua berita terkait atau ubah source-nya
        // News::where('source', $apiSource->name)->update(['source' => 'Unknown Source']);

        $apiSource->delete();

        return redirect()->route('admin.api-sources.index')
            ->with('success', 'Sumber API berhasil dihapus.');
    }

    public function refresh(ApiSource $apiSource)
    {
        try {
            $result = $this->newsApiService->fetchFromApi($apiSource);

            if ($result['success']) {
                return redirect()->route('admin.api-sources.index')
                    ->with('success', $result['message']);
            }

            return redirect()->route('admin.api-sources.index')
                ->with('error', $result['message']);
        } catch (\Exception $e) {
            Log::error('API refresh error: ' . $e->getMessage(), [
                'api_source_id' => $apiSource->id,
                'api_name' => $apiSource->name
            ]);

            return redirect()->route('admin.api-sources.index')
                ->with('error', 'Terjadi kesalahan saat refresh API: ' . $e->getMessage());
        }
    }

    /**
     * Refresh all active API sources
     */
    public function refreshAll(Request $request)
    {
        // Get all active API sources
        $activeSources = ApiSource::where('status', 'active')->get();

        $totalProcessed = 0;
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($activeSources as $source) {
            try {
                $totalProcessed++;
                $result = $this->newsApiService->fetchFromApi($source);

                if ($result['success']) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = $source->name . ': ' . $result['message'];
                }
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = $source->name . ': ' . $e->getMessage();

                Log::error('API refresh error during refresh all: ' . $e->getMessage(), [
                    'api_source_id' => $source->id,
                    'api_name' => $source->name
                ]);
            }
        }

        // Prepare message
        if ($successCount > 0 && $errorCount === 0) {
            $message = "Berhasil menyinkronkan {$successCount} sumber API.";
            return redirect()->route('admin.api-sources.index')->with('success', $message);
        } elseif ($successCount > 0 && $errorCount > 0) {
            $message = "Berhasil menyinkronkan {$successCount} sumber API, namun terjadi {$errorCount} kesalahan.";
            return redirect()->route('admin.api-sources.index')
                ->with('warning', $message)
                ->with('error_details', $errors);
        } else {
            $message = "Gagal menyinkronkan semua sumber API. Terjadi {$errorCount} kesalahan.";
            return redirect()->route('admin.api-sources.index')
                ->with('error', $message)
                ->with('error_details', $errors);
        }
    }

    /**
     * Test API connection
     */
    public function testConnection(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'api_key' => 'nullable|string',
            'param_keys' => 'nullable|array',
            'param_values' => 'nullable|array',
        ]);

        // Process parameters
        $params = [];
        if ($request->has('param_keys') && $request->has('param_values')) {
            foreach ($request->param_keys as $index => $key) {
                if (!empty($key) && isset($request->param_values[$index])) {
                    $params[$key] = $request->param_values[$index];
                }
            }
        }

        // Create temporary API source object for testing
        $testSource = new ApiSource([
            'name' => 'Test Connection',
            'url' => $validated['url'],
            'api_key' => $validated['api_key'],
            'params' => $params,
        ]);

        try {
            // Use the NewsApiService to test the connection
            $result = $this->newsApiService->testConnection($testSource);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
