<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ChatbotService;
use App\Models\ChatbotSubService;
use Illuminate\Http\Request;

class ChatbotSubServiceController extends Controller
{
    /**
     * Create form with CKEditor.
     */
    public function create(ChatbotService $service)
    {
        $this->authorizeService($service);
        $service->load('category');

        return view('client.chatbot.sub-services.create', compact('service'));
    }

    /**
     * Store new sub-service.
     */
    public function store(Request $request, ChatbotService $service)
    {
        $this->authorizeService($service);

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'detail_content'    => 'nullable|string',
        ]);

        $service->subServices()->create($validated);

        return redirect()
            ->route('client.chatbot.services.show', $service)
            ->with('success', 'Sub-service created successfully.');
    }

    /**
     * Show sub-service detail.
     */
    public function show(ChatbotSubService $subService)
    {
        $this->authorizeSubService($subService);

        $subService->load('service.category.website');

        return view('client.chatbot.sub-services.show', compact('subService'));
    }

    /**
     * Edit form with CKEditor.
     */
    public function edit(ChatbotSubService $subService)
    {
        $this->authorizeSubService($subService);
        $subService->load('service.category');

        return view('client.chatbot.sub-services.edit', compact('subService'));
    }

    /**
     * Update sub-service.
     */
    public function update(Request $request, ChatbotSubService $subService)
    {
        $this->authorizeSubService($subService);

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'detail_content'    => 'nullable|string',
        ]);

        $subService->update($validated);

        return redirect()
            ->route('client.chatbot.services.show', $subService->service_id)
            ->with('success', 'Sub-service updated successfully.');
    }

    /**
     * Delete sub-service.
     */
    public function destroy(ChatbotSubService $subService)
    {
        $this->authorizeSubService($subService);
        $serviceId = $subService->service_id;
        $subService->delete();

        return redirect()
            ->route('client.chatbot.services.show', $serviceId)
            ->with('success', 'Sub-service deleted successfully.');
    }

    /* ── Authorization helpers ───────────────────── */

    protected function authorizeSubService(ChatbotSubService $subService): void
    {
        $subService->loadMissing('service.category');
        abort_if(
            !in_array($subService->service->category->website_id, auth()->user()->websiteIds()),
            403,
            'Unauthorized.'
        );
    }

    protected function authorizeService(ChatbotService $service): void
    {
        $service->loadMissing('category');
        abort_if(
            !in_array($service->category->website_id, auth()->user()->websiteIds()),
            403,
            'Unauthorized.'
        );
    }
}
