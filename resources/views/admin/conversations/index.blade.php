@extends('layouts.admin')

@section('title', 'Conversations')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Conversations</h1>
                <p class="text-gray-600 mt-2">All visitor chats in a messaging‑style list.</p>
            </div>
        </div>

        @if ($conversations->count() > 0)
            <div class="bg-white rounded-xl shadow divide-y divide-gray-100">
                @foreach ($conversations as $conversation)
                    <a href="{{ route('admin.chat', $conversation->id) }}" class="block hover:bg-gray-50 transition">
                        @php
                            $lastAt = $conversation->last_message_at
                                ? \Carbon\Carbon::parse($conversation->last_message_at)
                                : null;
                        @endphp
                        <div class="px-5 py-4 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-sm font-semibold text-orange-600">
                                    {{ $conversation->website?->name[0] ?? 'V' }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-semibold text-gray-900 text-sm">
                                            @if ($conversation->visitor)
                                                {{ \Illuminate\Support\Str::limit($conversation->visitor->visitor_token, 18) }}
                                            @else
                                                Unknown visitor
                                            @endif
                                        </h3>
                                        <span
                                            class="text-[11px] px-2 py-0.5 rounded-full 
                                            @if ($conversation->status === 'human') bg-amber-100 text-amber-800
                                            @else
                                                bg-green-100 text-green-800 @endif">
                                            {{ $conversation->status === 'human' ? 'Human' : 'Bot' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        @if ($conversation->website)
                                            {{ $conversation->website->name }}
                                        @else
                                            Unknown website
                                        @endif
                                        · {{ $conversation->messages->count() }} messages
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400">
                                    @if ($lastAt)
                                        {{ $lastAt->diffForHumans() }}
                                    @else
                                        No messages
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-500 text-lg">No conversations yet.</p>
            </div>
        @endif
    </div>
@endsection
