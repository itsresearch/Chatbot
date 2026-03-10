<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UtilityController extends Controller
{
    public function downloadChatFile(Message $message, Request $request)
    {
        if (!$message->file_path || !Storage::disk('local')->exists($message->file_path)) {
            abort(404, 'File not found.');
        }

        $conversation = $message->conversation;

        // Admin access (logged-in and owns the website)
        $user = auth()->user();
        if ($user) {
            $websiteIds = $user->websiteIds();
            if ($conversation && in_array($conversation->website_id, $websiteIds)) {
                return $this->serveFile($message);
            }
        }

        // Visitor access (via visitor_token in query string)
        $visitorToken = $request->query('visitor_token');
        if ($visitorToken && $conversation && $conversation->visitor && $conversation->visitor->visitor_token === $visitorToken) {
            return $this->serveFile($message);
        }

        abort(403, 'Unauthorized.');
    }

    public function switchWebsite(Request $request)
    {
        $websiteId = $request->input('website_id');

        if ($websiteId) {
            $valid = auth()->user()->websites()->where('id', $websiteId)->exists();
            abort_if(!$valid, 403, 'Unauthorized website.');
            session(['active_website_id' => (int) $websiteId]);
        } else {
            session()->forget('active_website_id');
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'active_website_id' => $websiteId]);
        }

        return back();
    }

    public function unreadCount()
    {
        $user = auth()->user();
        $allWebsiteIds = $user->websiteIds();

        $conversations = Conversation::with('website')
            ->whereIn('website_id', $allWebsiteIds)
            ->whereNotNull('visitor_id')
            ->whereHas('visitor')
            ->get();

        $totalUnread = 0;
        $perWebsite = [];

        foreach ($conversations as $conv) {
            $lastMsg = $conv->messages()->orderBy('id', 'desc')->first();
            $isVisitorMsg = $lastMsg && $lastMsg->sender_type === 'visitor';
            $isUnread = $isVisitorMsg
                && $conv->last_message_at
                && (!$conv->admin_viewed_at || $conv->last_message_at > $conv->admin_viewed_at);

            if ($isUnread) {
                $totalUnread++;
                $wid = $conv->website_id;
                if (!isset($perWebsite[$wid])) {
                    $perWebsite[$wid] = ['count' => 0, 'name' => $conv->website->name ?? 'Unknown'];
                }
                $perWebsite[$wid]['count']++;
            }
        }

        return response()->json([
            'total' => $totalUnread,
            'per_website' => $perWebsite,
        ]);
    }

    public function recentNotifications()
    {
        $user = auth()->user();
        $allWebsiteIds = $user->websiteIds();

        $conversations = Conversation::with(['visitor', 'website'])
            ->whereIn('website_id', $allWebsiteIds)
            ->whereNotNull('visitor_id')
            ->whereHas('visitor')
            ->whereRaw('last_message_at > COALESCE(admin_viewed_at, "1970-01-01")')
            ->orderBy('last_message_at', 'desc')
            ->limit(10)
            ->get();

        $items = $conversations->map(function ($conv) {
            $lastMsg = $conv->messages()->orderBy('id', 'desc')->first();
            $isVisitorMsg = $lastMsg && $lastMsg->sender_type === 'visitor';

            if (!$isVisitorMsg) {
                return null;
            }

            return [
                'conversation_id' => $conv->id,
                'website_id' => $conv->website_id,
                'website_name' => $conv->website->name ?? 'Unknown',
                'visitor_label' => $conv->visitor ? 'Visitor ' . substr($conv->visitor->visitor_token, 0, 8) : 'Unknown',
                'message_preview' => Str::limit($lastMsg->message, 60),
                'time_human' => $conv->last_message_at?->diffForHumans(),
                'time_iso' => $conv->last_message_at?->toIso8601String(),
            ];
        })->filter()->values()->toArray();

        return response()->json($items);
    }

    protected function serveFile(Message $message)
    {
        return response()->file(
            storage_path('app/private/' . str_replace('private/', '', $message->file_path)),
            [
                'Content-Type' => $message->file_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . ($message->file_name ?? 'file') . '"',
            ]
        );
    }
}
