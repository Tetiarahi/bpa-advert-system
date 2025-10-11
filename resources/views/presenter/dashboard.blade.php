<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Radio Presenter Dashboard - BPA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-white rounded-full w-12 h-12 flex items-center justify-center">
                        <i class="fas fa-microphone text-2xl text-blue-600"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">Radio Presenter Dashboard - Real-Time Content</h1>
                        <p class="text-blue-200">Welcome, {{ $presenter->name }} | Live Time-Based Filtering
                            <span class="inline-flex items-center ml-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-1"></span>
                                LIVE
                            </span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Active Hours Indicator -->
                    <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                        <div class="text-sm">Status</div>
                        <div class="font-bold flex items-center">
                            <span class="status-indicator w-2 h-2 rounded-full mr-2 {{ $isActiveHours ? 'bg-green-400' : 'bg-red-400' }}"></span>
                            {{ $isActiveHours ? 'Active Hours' : 'Off Hours' }}
                        </div>
                    </div>

                    <!-- Current Time Slot -->
                    <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                        <div class="text-sm">Current Slot</div>
                        <div class="font-bold capitalize current-time-slot time-slot-transition">
                            @if($currentTimeSlot === 'morning')
                                🌅 Morning (5AM-9AM)
                            @elseif($currentTimeSlot === 'lunch')
                                🍽️ Lunch (11AM-3PM)
                            @elseif($currentTimeSlot === 'evening')
                                🌆 Evening (4PM-11PM)
                            @else
                                🕐 Off-Hours
                            @endif
                        </div>
                    </div>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('presenter.logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Dashboard Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Time-Based Filtering Notice -->
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-clock text-blue-600 text-xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-gray-800">Time-Based Content Filtering</h3>
                        <p class="text-sm text-gray-600">
                            Currently showing <strong>{{ ucfirst($currentTimeSlot) }}</strong> content
                            @if($isActiveHours)
                                <span class="text-green-600 font-medium">(Active Broadcasting Hours)</span>
                            @else
                                <span class="text-orange-600 font-medium">(Outside Broadcasting Hours)</span>
                            @endif
                        <span class="text-xs text-gray-500 ml-2">
                            | Real-time: <span class="current-real-time font-mono">{{ now()->format('g:i:s A') }}</span>
                            <button onclick="checkCurrentTimeSlot()" class="ml-2 px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                Check Now
                            </button>
                        </span>
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Broadcasting Schedule:</div>
                    <div class="text-xs text-gray-400">
                        Morning: 5AM-9AM | Lunch: 11AM-3PM | Evening: 4PM-11PM
                    </div>
                    <div class="text-xs text-blue-600 mt-1">
                        Server Time: <span class="font-mono">{{ now()->format('g:i:s A') }}</span> ({{ now()->timezoneName }}) |
                        Current Hour: <span class="current-hour font-mono">{{ now()->hour }}</span> |
                        Expected Slot: <span class="expected-slot font-semibold">
                            @php
                                $hour = now()->hour;
                                if ($hour >= 5 && $hour < 9) echo 'Morning';
                                elseif ($hour >= 11 && $hour < 15) echo 'Lunch';
                                elseif ($hour >= 16 && $hour < 23) echo 'Evening';
                                else echo 'Off-Hours';
                            @endphp
                        </span>
                    </div>
                    <div class="text-xs text-green-600 mt-1">
                        Client Time: <span class="client-time font-mono"></span> |
                        Client Hour: <span class="client-hour font-mono"></span> |
                        Client Expected: <span class="client-expected font-semibold"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-bullhorn text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Advertisements</p>
                        <p class="text-2xl font-bold text-gray-800">{{ count($advertisements) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-heart text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Memorial Services</p>
                        <p class="text-2xl font-bold text-gray-800">{{ count($gongs) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Unread by Time Slot</p>
                        <div class="space-y-1">
                            <p class="text-lg font-bold text-gray-800">🌅 Morning: {{ $unreadCounts['morning'] ?? 0 }}</p>
                            <p class="text-lg font-bold text-gray-800">🍽️ Lunch: {{ $unreadCounts['lunch'] ?? 0 }}</p>
                            <p class="text-lg font-bold text-gray-800">🌆 Evening: {{ $unreadCounts['evening'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-orange-100 rounded-full p-3 mr-4">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Total Unread</p>
                        <p class="text-2xl font-bold text-gray-800">{{ ($unreadCounts['morning'] ?? 0) + ($unreadCounts['lunch'] ?? 0) + ($unreadCounts['evening'] ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Advertisements Section -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-bullhorn mr-3 text-blue-600"></i>
                        {{ ucfirst($currentTimeSlot) }} Advertisements
                        <span class="ml-2 bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded-full">{{ count($advertisements) }}</span>
                    </h2>
                    <button id="reload-ads-btn" class="reload-btn" title="Reload Advertisements">
                        <i class="fas fa-sync-alt"></i>
                        <span class="ml-2">Reload</span>
                    </button>
                </div>

                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($advertisements as $ad)
                        <!-- Morning Frequency Sticky Note -->
                        @if($ad->morning_freq > 0 && $currentTimeSlot === 'morning')
                        <div class="sticky-note advertisement morning {{ $ad->read_count_morning >= min($ad->morning_freq, 9) ? 'read' : 'unread' }}"
                             data-type="advertisement"
                             data-id="{{ $ad->id }}"
                             data-time-slot="morning"
                             data-read-count="{{ $ad->read_count_morning }}"
                             data-max-readings="{{ min($ad->morning_freq, 9) }}"
                             data-collapsed="false">
                            <div class="sticky-note-header">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm">{{ $ad->customer->fullname }}</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="zoom-controls">
                                            <button class="zoom-btn zoom-out" title="Zoom Out">
                                                <i class="fas fa-search-minus"></i>
                                            </button>
                                            <span class="zoom-level">100%</span>
                                            <button class="zoom-btn zoom-in" title="Zoom In">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                        </div>
                                        <button class="collapse-btn" title="Collapse/Expand">
                                            <i class="fas fa-chevron-up"></i>
                                        </button>
                                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">🌅 Morning: {{ $ad->morning_freq }}x</span>
                                        <div class="reading-progress text-xs font-semibold">
                                            <span class="read-count">{{ $ad->read_count_morning }}</span>/<span class="max-readings">{{ min($ad->morning_freq, 9) }}</span>
                                        </div>
                                        <div class="reading-buttons flex flex-wrap gap-1">
                                            @for($i = 1; $i <= min($ad->morning_freq, 9); $i++)
                                                @php
                                                    $timeSlot = 'morning';
                                                    $times = $ad->{$timeSlot . '_times'} ?? [];
                                                    $time = isset($times[$i-1]) ? $times[$i-1] : '';
                                                @endphp
                                                <button class="reading-btn {{ isset($ad->readings_morning[$i]) && $ad->readings_morning[$i] ? 'read' : 'unread' }}"
                                                        data-reading-number="{{ $i }}"
                                                        title="Reading {{ $i }}{{ $time ? ' at ' . $time : '' }}">
                                                    <div class="text-center">
                                                        <div class="font-bold">{{ $i }}</div>
                                                        @if($time)
                                                            <div class="text-xs">{{ $time }}</div>
                                                        @endif
                                                    </div>
                                                </button>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="sticky-note-content">
                                <h3 class="font-bold mb-2">{{ $ad->title }}</h3>
                                <p class="text-sm mb-3">{{ Str::limit($ad->plain_content, 100) }}</p>

                                <div class="text-xs space-y-1">
                                    <div><strong>Time Slot:</strong> Evening (5PM-9:30PM) - {{ $ad->evening_freq }} times</div>
                                    <div><strong>Duration:</strong> {{ $ad->broadcast_start_date->format('M d') }} - {{ $ad->broadcast_end_date->format('M d') }}</div>
                                </div>
                            </div>

                            <div class="sticky-note-content">
                                <h3 class="font-bold mb-2">{{ $ad->title }}</h3>
                                <p class="text-sm mb-3">{{ Str::limit($ad->plain_content, 100) }}</p>

                                <div class="text-xs space-y-1">
                                    <div><strong>Time Slot:</strong> Morning (6AM-8AM) - {{ $ad->morning_freq }} times</div>
                                    <div><strong>Duration:</strong> {{ $ad->broadcast_start_date->format('M d') }} - {{ $ad->broadcast_end_date->format('M d') }}</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Lunch Frequency Sticky Note -->
                        @if($ad->lunch_freq > 0 && $currentTimeSlot === 'lunch')
                        <div class="sticky-note advertisement lunch {{ $ad->read_count_lunch >= min($ad->lunch_freq, 9) ? 'read' : 'unread' }}"
                             data-type="advertisement"
                             data-id="{{ $ad->id }}"
                             data-time-slot="lunch"
                             data-read-count="{{ $ad->read_count_lunch }}"
                             data-max-readings="{{ min($ad->lunch_freq, 9) }}"
                             data-collapsed="false">
                            <div class="sticky-note-header">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm">{{ $ad->customer->fullname }}</span>
                                    <div class="flex items-center space-x-2">
                                        <button class="collapse-btn" title="Collapse/Expand">
                                            <i class="fas fa-chevron-up"></i>
                                        </button>
                                        <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">🍽️ Lunch: {{ $ad->lunch_freq }}x</span>
                                        <div class="reading-progress text-xs font-semibold">
                                            <span class="read-count">{{ $ad->read_count_lunch }}</span>/<span class="max-readings">{{ min($ad->lunch_freq, 9) }}</span>
                                        </div>
                                        <div class="reading-buttons flex flex-wrap gap-1">
                                            @for($i = 1; $i <= min($ad->lunch_freq, 9); $i++)
                                                @php
                                                    $timeSlot = 'lunch';
                                                    $times = $ad->{$timeSlot . '_times'} ?? [];
                                                    $time = $times[$i-1] ?? '';
                                                @endphp
                                                <button class="reading-btn {{ isset($ad->readings_lunch[$i]) && $ad->readings_lunch[$i] ? 'read' : 'unread' }}"
                                                        data-reading-number="{{ $i }}"
                                                        title="Reading {{ $i }}{{ $time ? ' at ' . $time : '' }}">
                                                    <div class="text-center">
                                                        <div class="font-bold">{{ $i }}</div>
                                                        @if($time)
                                                            <div class="text-xs">{{ $time }}</div>
                                                        @endif
                                                    </div>
                                                </button>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="sticky-note-content">
                                <h3 class="font-bold mb-2">{{ $ad->title }}</h3>
                                <p class="text-sm mb-3">{{ Str::limit($ad->plain_content, 100) }}</p>

                                <div class="text-xs space-y-1">
                                    <div><strong>Time Slot:</strong> Lunch (12PM-2PM) - {{ $ad->lunch_freq }} times</div>
                                    <div><strong>Duration:</strong> {{ $ad->broadcast_start_date->format('M d') }} - {{ $ad->broadcast_end_date->format('M d') }}</div>
                                </div>
                            </div>

                            <div class="sticky-note-content">
                                <h3 class="font-bold mb-2">{{ $ad->title }}</h3>
                                <p class="text-sm mb-3">{{ Str::limit($ad->content, 100) }}</p>

                                <div class="text-xs space-y-1">
                                    <div><strong>Time Slot:</strong> Lunch (12PM-2PM) - {{ $ad->lunch_freq }} times</div>
                                    <div><strong>Duration:</strong> {{ $ad->broadcast_start_date->format('M d') }} - {{ $ad->broadcast_end_date->format('M d') }}</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Evening Frequency Sticky Note -->
                        @if($ad->evening_freq > 0 && $currentTimeSlot === 'evening')
                        <div class="sticky-note advertisement evening {{ $ad->read_count_evening >= min($ad->evening_freq, 9) ? 'read' : 'unread' }}"
                             data-type="advertisement"
                             data-id="{{ $ad->id }}"
                             data-time-slot="evening"
                             data-read-count="{{ $ad->read_count_evening }}"
                             data-max-readings="{{ min($ad->evening_freq, 9) }}"
                             data-collapsed="false">
                            <div class="sticky-note-header">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm">{{ $ad->customer->fullname }}</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">🌆 Evening: {{ $ad->evening_freq }}x</span>
                                        <div class="reading-progress text-xs font-semibold">
                                            <span class="read-count">{{ $ad->read_count_evening }}</span>/<span class="max-readings">{{ min($ad->evening_freq, 9) }}</span>
                                        </div>
                                        <div class="reading-buttons flex flex-wrap gap-1">
                                            @for($i = 1; $i <= min($ad->evening_freq, 9); $i++)
                                                @php
                                                    $timeSlot = 'evening';
                                                    $times = $ad->{$timeSlot . '_times'} ?? [];
                                                    $time = $times[$i-1] ?? '';
                                                @endphp
                                                <button class="reading-btn {{ isset($ad->readings_evening[$i]) && $ad->readings_evening[$i] ? 'read' : 'unread' }}"
                                                        data-reading-number="{{ $i }}"
                                                        title="Reading {{ $i }}{{ $time ? ' at ' . $time : '' }}">
                                                    <div class="text-center">
                                                        <div class="font-bold">{{ $i }}</div>
                                                        @if($time)
                                                            <div class="text-xs">{{ $time }}</div>
                                                        @endif
                                                    </div>
                                                </button>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs opacity-75">{{ $ad->adsCategory->name ?? 'General' }} - AM Band</div>
                            </div>

                            <div class="sticky-note-content">
                                <h3 class="font-bold mb-2">{{ $ad->title }}</h3>
                                <p class="text-sm mb-3">{{ Str::limit($ad->content, 100) }}</p>

                                <div class="text-xs space-y-1">
                                    <div><strong>Time Slot:</strong> Evening (5PM-9:30PM) - {{ $ad->evening_freq }} times</div>
                                    <div><strong>Duration:</strong> {{ $ad->broadcast_start_date->format('M d') }} - {{ $ad->broadcast_end_date->format('M d') }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-bullhorn text-4xl mb-4 opacity-50"></i>
                            <p>No AM advertisements scheduled for today</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Memorial Services Section -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-heart mr-3 text-purple-600"></i>
                        {{ ucfirst($currentTimeSlot) }} Memorial Services
                        <span class="ml-2 bg-purple-100 text-purple-800 text-sm px-2 py-1 rounded-full">{{ count($gongs) }}</span>
                    </h2>
                    <button id="reload-gongs-btn" class="reload-btn" title="Reload Memorial Services">
                        <i class="fas fa-sync-alt"></i>
                        <span class="ml-2">Reload</span>
                    </button>
                </div>

                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($gongs as $gong)
                        <!-- Morning Frequency Sticky Note -->
                        @if($gong->morning_freq > 0 && $currentTimeSlot === 'morning')
                        <div class="sticky-note memorial morning {{ $gong->read_count_morning >= min($gong->morning_freq, 9) ? 'read' : 'unread' }}"
                             data-type="gong"
                             data-id="{{ $gong->id }}"
                             data-time-slot="morning"
                             data-read-count="{{ $gong->read_count_morning }}"
                             data-max-readings="{{ min($gong->morning_freq, 9) }}">
                            <div class="sticky-note-header">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm">{{ $gong->customer->fullname }}</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">🌅 Morning: {{ $gong->morning_freq }}x</span>
                                        <div class="reading-progress text-xs font-semibold">
                                            <span class="read-count">{{ $gong->read_count_morning }}</span>/<span class="max-readings">{{ min($gong->morning_freq, 9) }}</span>
                                        </div>
                                        <div class="reading-buttons flex flex-wrap gap-1">
                                            @for($i = 1; $i <= min($gong->morning_freq, 9); $i++)
                                                @php
                                                    $timeSlot = 'morning';
                                                    $times = $gong->{$timeSlot . '_times'} ?? [];
                                                    $time = $times[$i-1] ?? '';
                                                @endphp
                                                <button class="reading-btn {{ isset($gong->readings_morning[$i]) && $gong->readings_morning[$i] ? 'read' : 'unread' }}"
                                                        data-reading-number="{{ $i }}"
                                                        title="Reading {{ $i }}{{ $time ? ' at ' . $time : '' }}">
                                                    <div class="text-center">
                                                        <div class="font-bold">{{ $i }}</div>
                                                        @if($time)
                                                            <div class="text-xs">{{ $time }}</div>
                                                        @endif
                                                    </div>
                                                </button>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs opacity-75">Memorial Service - AM Band</div>
                            </div>

                            <div class="sticky-note-content">
                                <h3 class="font-bold mb-2">{{ $gong->departed_name }}</h3>
                                <p class="text-sm mb-3">{{ Str::limit($gong->plain_contents, 100) }}</p>

                                <div class="text-xs space-y-1">
                                    @if($gong->song_title)
                                        <div><strong>Song:</strong> {{ $gong->song_title }}</div>
                                    @endif
                                    <div><strong>Time Slot:</strong> Morning (6AM-8AM) - {{ $gong->morning_freq }} times</div>
                                    <div><strong>Duration:</strong> {{ $gong->broadcast_start_date->format('M d') }} - {{ $gong->broadcast_end_date->format('M d') }}</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Lunch Frequency Sticky Note -->
                        @if($gong->lunch_freq > 0 && $currentTimeSlot === 'lunch')
                        <div class="sticky-note memorial lunch {{ $gong->read_count_lunch >= min($gong->lunch_freq, 9) ? 'read' : 'unread' }}"
                             data-type="gong"
                             data-id="{{ $gong->id }}"
                             data-time-slot="lunch"
                             data-read-count="{{ $gong->read_count_lunch }}"
                             data-max-readings="{{ min($gong->lunch_freq, 9) }}">
                            <div class="sticky-note-header">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm">{{ $gong->customer->fullname }}</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">🍽️ Lunch: {{ $gong->lunch_freq }}x</span>
                                        <div class="reading-progress text-xs font-semibold">
                                            <span class="read-count">{{ $gong->read_count_lunch }}</span>/<span class="max-readings">{{ min($gong->lunch_freq, 9) }}</span>
                                        </div>
                                        <div class="reading-buttons flex flex-wrap gap-1">
                                            @for($i = 1; $i <= min($gong->lunch_freq, 9); $i++)
                                                @php
                                                    $timeSlot = 'lunch';
                                                    $times = $gong->{$timeSlot . '_times'} ?? [];
                                                    $time = $times[$i-1] ?? '';
                                                @endphp
                                                <button class="reading-btn {{ isset($gong->readings_lunch[$i]) && $gong->readings_lunch[$i] ? 'read' : 'unread' }}"
                                                        data-reading-number="{{ $i }}"
                                                        title="Reading {{ $i }}{{ $time ? ' at ' . $time : '' }}">
                                                    <div class="text-center">
                                                        <div class="font-bold">{{ $i }}</div>
                                                        @if($time)
                                                            <div class="text-xs">{{ $time }}</div>
                                                        @endif
                                                    </div>
                                                </button>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs opacity-75">Memorial Service - AM Band</div>
                            </div>

                            <div class="sticky-note-content">
                                <h3 class="font-bold mb-2">{{ $gong->departed_name }}</h3>
                                <p class="text-sm mb-3">{{ Str::limit($gong->plain_contents, 100) }}</p>

                                <div class="text-xs space-y-1">
                                    @if($gong->song_title)
                                        <div><strong>Song:</strong> {{ $gong->song_title }}</div>
                                    @endif
                                    <div><strong>Time Slot:</strong> Lunch (12PM-2PM) - {{ $gong->lunch_freq }} times</div>
                                    <div><strong>Duration:</strong> {{ $gong->broadcast_start_date->format('M d') }} - {{ $gong->broadcast_end_date->format('M d') }}</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Evening Frequency Sticky Note -->
                        @if($gong->evening_freq > 0 && $currentTimeSlot === 'evening')
                        <div class="sticky-note memorial evening {{ $gong->read_count_evening >= min($gong->evening_freq, 9) ? 'read' : 'unread' }}"
                             data-type="gong"
                             data-id="{{ $gong->id }}"
                             data-time-slot="evening"
                             data-read-count="{{ $gong->read_count_evening }}"
                             data-max-readings="{{ min($gong->evening_freq, 9) }}">
                            <div class="sticky-note-header">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm">{{ $gong->customer->fullname }}</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">🌆 Evening: {{ $gong->evening_freq }}x</span>
                                        <div class="reading-progress text-xs font-semibold">
                                            <span class="read-count">{{ $gong->read_count_evening }}</span>/<span class="max-readings">{{ min($gong->evening_freq, 9) }}</span>
                                        </div>
                                        <div class="reading-buttons flex flex-wrap gap-1">
                                            @for($i = 1; $i <= min($gong->evening_freq, 9); $i++)
                                                @php
                                                    $timeSlot = 'evening';
                                                    $times = $gong->{$timeSlot . '_times'} ?? [];
                                                    $time = $times[$i-1] ?? '';
                                                @endphp
                                                <button class="reading-btn {{ isset($gong->readings_evening[$i]) && $gong->readings_evening[$i] ? 'read' : 'unread' }}"
                                                        data-reading-number="{{ $i }}"
                                                        title="Reading {{ $i }}{{ $time ? ' at ' . $time : '' }}">
                                                    <div class="text-center">
                                                        <div class="font-bold">{{ $i }}</div>
                                                        @if($time)
                                                            <div class="text-xs">{{ $time }}</div>
                                                        @endif
                                                    </div>
                                                </button>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs opacity-75">Memorial Service - AM Band</div>
                            </div>

                            <div class="sticky-note-content">
                                <h3 class="font-bold mb-2">{{ $gong->departed_name }}</h3>
                                <p class="text-sm mb-3">{{ Str::limit($gong->plain_contents, 100) }}</p>

                                <div class="text-xs space-y-1">
                                    @if($gong->song_title)
                                        <div><strong>Song:</strong> {{ $gong->song_title }}</div>
                                    @endif
                                    <div><strong>Time Slot:</strong> Evening (5PM-9:30PM) - {{ $gong->evening_freq }} times</div>
                                    <div><strong>Duration:</strong> {{ $gong->broadcast_start_date->format('M d') }} - {{ $gong->broadcast_end_date->format('M d') }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-heart text-4xl mb-4 opacity-50"></i>
                            <p>No AM memorial services scheduled for today</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <!-- Custom Styles -->
    <style>
        .sticky-note {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .sticky-note.memorial {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            border-left-color: #8b5cf6;
        }

        .sticky-note.read {
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            border-left-color: #ef4444;
            opacity: 0.8;
        }

        .sticky-note.memorial.read {
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            border-left-color: #ef4444;
        }

        .sticky-note:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .sticky-note::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 0 20px 20px 0;
            border-color: transparent rgba(0, 0, 0, 0.1) transparent transparent;
        }

        .sticky-note-header {
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .toggle-read-btn {
            background: rgba(255, 255, 255, 0.8);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .toggle-read-btn:hover {
            background: rgba(255, 255, 255, 1);
            transform: scale(1.1);
        }

        .reading-btn {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid #d1d5db;
            border-radius: 6px;
            min-width: 32px;
            min-height: 32px;
            width: auto;
            height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 2px;
        }

        .reading-btn.read {
            background: #10b981;
            color: white;
            border-color: #059669;
        }

        .reading-btn.unread {
            background: #f3f4f6;
            color: #6b7280;
            border-color: #d1d5db;
        }

        .reading-btn:hover {
            transform: scale(1.1);
        }

        .reading-btn.read:hover {
            background: #059669;
        }

        .reading-btn.unread:hover {
            background: #e5e7eb;
        }

        .reading-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        .sticky-note.read {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 2px solid #dc2626;
            box-shadow: 0 4px 6px rgba(220, 38, 38, 0.1);
        }

        .sticky-note.read::after {
            content: "✓ COMPLETED";
            position: absolute;
            top: 5px;
            right: 5px;
            background: #dc2626;
            color: white;
            font-size: 8px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 10px;
            z-index: 10;
        }

        /* Collapse functionality */
        .collapse-btn {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #d1d5db;
            border-radius: 6px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            color: #374151;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .collapse-btn:hover {
            background: #f9fafb;
            border-color: #9ca3af;
            transform: scale(1.15);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Enhanced visibility when collapsed */
        .sticky-note.collapsed .collapse-btn {
            background: #3b82f6;
            border-color: #2563eb;
            color: white;
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
            animation: pulse-blue 2s infinite;
        }

        .sticky-note.collapsed .collapse-btn:hover {
            background: #2563eb;
            border-color: #1d4ed8;
            transform: scale(1.2);
        }

        @keyframes pulse-blue {
            0%, 100% {
                box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
            }
            50% {
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.5);
            }
        }

        .sticky-note.collapsed {
            height: auto;
            min-height: 50px;
            max-height: 50px;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .sticky-note.collapsed .sticky-note-content {
            max-height: 0;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .sticky-note .sticky-note-content {
            max-height: 500px;
            opacity: 1;
            transform: translateY(0);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .sticky-note.collapsed .collapse-btn i {
            transform: rotate(180deg);
        }

        .sticky-note-header {
            flex-shrink: 0;
            position: relative;
            z-index: 2;
            background: inherit;
            border-radius: inherit;
        }

        /* Ensure smooth vertical collapse */
        .sticky-note {
            display: flex;
            flex-direction: column;
            transition: max-height 0.4s ease, box-shadow 0.3s ease;
        }

        .sticky-note.collapsed {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sticky-note:not(.collapsed) {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Smooth content transitions */
        .sticky-note-content > * {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .sticky-note.collapsed .sticky-note-content > * {
            opacity: 0;
            transform: translateY(-5px);
        }

        /* Reload button styling */
        .reload-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            border-radius: 8px;
            color: white;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        }

        .reload-btn:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
        }

        .reload-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        }

        .reload-btn i {
            transition: transform 0.3s ease;
        }

        .reload-btn:hover i {
            transform: rotate(180deg);
        }

        .reload-btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .reload-btn.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Zoom controls styling */
        .zoom-controls {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 2px;
            gap: 2px;
        }

        .zoom-btn {
            background: transparent;
            border: none;
            width: 24px;
            height: 24px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 12px;
            color: #6b7280;
        }

        .zoom-btn:hover {
            background: #f3f4f6;
            color: #374151;
            transform: scale(1.1);
        }

        .zoom-btn:active {
            transform: scale(0.95);
        }

        .zoom-level {
            font-size: 10px;
            font-weight: bold;
            color: #374151;
            min-width: 35px;
            text-align: center;
            padding: 0 4px;
        }

        /* Zoom states for sticky notes */
        .sticky-note {
            transform-origin: top left;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .sticky-note.zoomed {
            z-index: 10;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* Zoom levels */
        .sticky-note[data-zoom="75"] {
            transform: scale(0.75);
        }

        .sticky-note[data-zoom="90"] {
            transform: scale(0.9);
        }

        .sticky-note[data-zoom="100"] {
            transform: scale(1);
        }

        .sticky-note[data-zoom="110"] {
            transform: scale(1.1);
        }

        .sticky-note[data-zoom="125"] {
            transform: scale(1.25);
        }

        .sticky-note[data-zoom="150"] {
            transform: scale(1.5);
        }

        .sticky-note[data-zoom="175"] {
            transform: scale(1.75);
        }

        .sticky-note[data-zoom="200"] {
            transform: scale(2);
        }

        .reading-progress {
            color: #374151;
            background: rgba(255, 255, 255, 0.9);
            padding: 2px 6px;
            border-radius: 4px;
        }

        /* Real-time elements */
        .real-time-clock {
            animation: pulse 2s infinite;
        }

        .next-change-countdown {
            animation: slideInDown 0.5s ease-out;
        }

        .time-slot-change-notification {
            animation: slideInRight 0.5s ease-out;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Time slot transition effects */
        .time-slot-transition {
            transition: all 0.3s ease-in-out;
        }

        .status-indicator {
            transition: background-color 0.3s ease;
        }

        .unread {
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
            50% { box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.3), 0 0 0 4px rgba(34, 197, 94, 0.1); }
            100% { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        }
    </style>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set up CSRF token for AJAX requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Handle read/unread toggle
            document.querySelectorAll('.toggle-read-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const stickyNote = this.closest('.sticky-note');
                    const contentType = stickyNote.dataset.type;
                    const contentId = stickyNote.dataset.id;
                    const timeSlot = stickyNote.dataset.timeSlot;
                    const isCurrentlyRead = stickyNote.classList.contains('read');

                    // Toggle read status
                    const action = isCurrentlyRead ? 'unread' : 'read';
                    const url = `/presenter/mark-as-${action}`;

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            content_type: contentType,
                            content_id: contentId,
                            time_slot: timeSlot
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update UI
                            if (isCurrentlyRead) {
                                stickyNote.classList.remove('read');
                                stickyNote.classList.add('unread');
                                this.innerHTML = '<i class="fas fa-eye"></i>';
                                this.title = 'Mark as Read';
                            } else {
                                stickyNote.classList.remove('unread');
                                stickyNote.classList.add('read');
                                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                                this.title = 'Mark as Unread';
                            }

                            // Update stats
                            updateStats();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to update read status. Please try again.');
                    });
                });
            });

            // Handle individual reading button clicks
            document.querySelectorAll('.reading-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const stickyNote = this.closest('.sticky-note');
                    const contentType = stickyNote.dataset.type;
                    const contentId = stickyNote.dataset.id;
                    const timeSlot = stickyNote.dataset.timeSlot;
                    const readingNumber = parseInt(this.dataset.readingNumber);
                    const maxReadings = parseInt(stickyNote.dataset.maxReadings);
                    const isCurrentlyRead = this.classList.contains('read');

                    // Check if this is a sequential click
                    if (!isCurrentlyRead) {
                        // For marking as read, check if previous buttons are read
                        const allButtons = stickyNote.querySelectorAll('.reading-btn');
                        let canClick = true;

                        // Check if all previous buttons are read
                        for (let i = 1; i < readingNumber; i++) {
                            const prevButton = Array.from(allButtons).find(btn =>
                                parseInt(btn.dataset.readingNumber) === i
                            );
                            if (prevButton && !prevButton.classList.contains('read')) {
                                canClick = false;
                                break;
                            }
                        }

                        if (!canClick) {
                            alert(`Please click reading buttons in sequence. Click reading ${readingNumber - 1} first.`);
                            return;
                        }
                    }

                    // Toggle read status for this specific reading
                    const action = isCurrentlyRead ? 'unread' : 'read';
                    const url = `/presenter/mark-as-${action}`;

                    const requestBody = {
                        content_type: contentType,
                        content_id: contentId,
                        time_slot: timeSlot,
                        reading_number: readingNumber
                    };

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(requestBody)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('API Response:', data);
                            console.log('Individual readings:', data.individual_readings);
                            updateReadingDisplay(stickyNote, data.read_count, data.individual_readings);

                            // Debug logging
                            console.log('Completion check:', {
                                readCount: data.read_count,
                                maxReadings: maxReadings,
                                isComplete: data.read_count >= maxReadings,
                                readingNumber: readingNumber
                            });

                            // Check if all readings are complete and update card color
                            if (data.read_count >= maxReadings) {
                                console.log('Marking card as complete');
                                stickyNote.classList.remove('unread');
                                stickyNote.classList.add('read');
                                stickyNote.style.backgroundColor = '#fee2e2'; // Light red background
                                stickyNote.style.borderColor = '#dc2626'; // Red border
                            } else {
                                console.log('Card not yet complete');
                                stickyNote.classList.remove('read');
                                stickyNote.classList.add('unread');
                                stickyNote.style.backgroundColor = ''; // Reset background
                                stickyNote.style.borderColor = ''; // Reset border
                            }
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while updating the read status.');
                    });
                });
            });

            function updateReadingDisplay(stickyNote, readCount, individualReadings) {
                const maxReadings = parseInt(stickyNote.dataset.maxReadings);

                console.log('updateReadingDisplay called with:', {
                    readCount: readCount,
                    individualReadings: individualReadings,
                    maxReadings: maxReadings
                });

                // Update read count display
                const readCountSpan = stickyNote.querySelector('.read-count');
                if (readCountSpan) {
                    readCountSpan.textContent = readCount;
                }

                // Update reading buttons using individual readings data
                const readingButtons = stickyNote.querySelectorAll('.reading-btn');
                readingButtons.forEach((btn) => {
                    const readingNum = parseInt(btn.dataset.readingNumber);
                    const isRead = individualReadings && individualReadings[readingNum];

                    console.log(`Button ${readingNum}: isRead = ${isRead}`);

                    if (isRead) {
                        btn.classList.remove('unread');
                        btn.classList.add('read');
                    } else {
                        btn.classList.remove('read');
                        btn.classList.add('unread');
                    }

                    // Enable/disable buttons based on sequential logic
                    updateButtonAvailability(btn, readingNum, individualReadings);
                });

                // Update overall card appearance
                updateCardAppearance(stickyNote, readCount, maxReadings);

                // Update sticky note overall status
                if (readCount >= maxReadings) {
                    stickyNote.classList.remove('unread');
                    stickyNote.classList.add('read');
                } else {
                    stickyNote.classList.remove('read');
                    stickyNote.classList.add('unread');
                }

                // Update data attribute
                stickyNote.dataset.readCount = readCount;

                // Update stats
                updateStats();
            }

            function updateButtonAvailability(button, readingNum, individualReadings) {
                const stickyNote = button.closest('.sticky-note');
                const allButtons = stickyNote.querySelectorAll('.reading-btn');

                // Check if this button should be clickable
                let isClickable = true;

                // If button is not read, check if all previous buttons are read
                if (!button.classList.contains('read')) {
                    for (let i = 1; i < readingNum; i++) {
                        const prevButton = Array.from(allButtons).find(btn =>
                            parseInt(btn.dataset.readingNumber) === i
                        );
                        if (prevButton && !prevButton.classList.contains('read')) {
                            isClickable = false;
                            break;
                        }
                    }
                }

                // Update button appearance based on availability
                if (isClickable || button.classList.contains('read')) {
                    button.style.opacity = '1';
                    button.style.cursor = 'pointer';
                    button.disabled = false;
                } else {
                    button.style.opacity = '0.5';
                    button.style.cursor = 'not-allowed';
                    button.disabled = true;
                }
            }

            function updateCardAppearance(stickyNote, readCount, maxReadings) {
                if (readCount >= maxReadings) {
                    // All readings complete - make card red
                    stickyNote.classList.remove('unread');
                    stickyNote.classList.add('read');
                    stickyNote.style.backgroundColor = '#fee2e2'; // Light red background
                    stickyNote.style.borderColor = '#dc2626'; // Red border
                    stickyNote.style.borderWidth = '2px';

                    // Move completed card to bottom
                    moveCardToBottom(stickyNote);
                } else {
                    // Not all readings complete - reset to default
                    stickyNote.classList.remove('read');
                    stickyNote.classList.add('unread');
                    stickyNote.style.backgroundColor = ''; // Reset background
                    stickyNote.style.borderColor = ''; // Reset border
                    stickyNote.style.borderWidth = '';
                }
            }

            function moveCardToBottom(stickyNote) {
                const container = stickyNote.parentElement;

                // Add a smooth transition
                stickyNote.style.transition = 'all 0.5s ease';

                // Remove the card from its current position
                const cardClone = stickyNote.cloneNode(true);

                // Add animation class
                cardClone.style.opacity = '0.7';
                cardClone.style.transform = 'scale(0.95)';

                // Remove original card
                stickyNote.remove();

                // Append to bottom
                container.appendChild(cardClone);

                // Animate back to normal
                setTimeout(() => {
                    cardClone.style.opacity = '1';
                    cardClone.style.transform = 'scale(1)';

                    // Re-attach event listeners to the new card
                    attachEventListenersToCard(cardClone);
                }, 100);

                // Remove transition after animation
                setTimeout(() => {
                    cardClone.style.transition = '';
                }, 600);
            }

            function attachEventListenersToCard(card) {
                // Initialize collapse functionality for the moved card
                if (!card.dataset.collapsed) {
                    card.dataset.collapsed = 'false';
                }

                // Add zoom controls if they don't exist
                if (!card.querySelector('.zoom-controls')) {
                    const header = card.querySelector('.sticky-note-header .flex.items-center.justify-between > div:last-child');
                    if (header) {
                        const zoomControls = document.createElement('div');
                        zoomControls.className = 'zoom-controls';
                        zoomControls.innerHTML = `
                            <button class="zoom-btn zoom-out" title="Zoom Out">
                                <i class="fas fa-search-minus"></i>
                            </button>
                            <span class="zoom-level">100%</span>
                            <button class="zoom-btn zoom-in" title="Zoom In">
                                <i class="fas fa-search-plus"></i>
                            </button>
                        `;
                        header.insertBefore(zoomControls, header.firstChild);

                        // Add zoom event listeners
                        const zoomInBtn = zoomControls.querySelector('.zoom-in');
                        const zoomOutBtn = zoomControls.querySelector('.zoom-out');

                        zoomInBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            zoomIn(card);
                        });

                        zoomOutBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            zoomOut(card);
                        });
                    }
                }

                // Add collapse button if it doesn't exist
                if (!card.querySelector('.collapse-btn')) {
                    const header = card.querySelector('.sticky-note-header .flex.items-center.justify-between > div:last-child');
                    if (header) {
                        const collapseBtn = document.createElement('button');
                        collapseBtn.className = 'collapse-btn';
                        collapseBtn.title = 'Collapse/Expand';
                        collapseBtn.innerHTML = '<i class="fas fa-chevron-up"></i>';
                        header.insertBefore(collapseBtn, header.firstChild);

                        // Add event listener to the new collapse button
                        collapseBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const stickyNote = this.closest('.sticky-note');
                            const isCollapsed = stickyNote.dataset.collapsed === 'true';

                            if (isCollapsed) {
                                expandCard(stickyNote, this);
                            } else {
                                collapseCard(stickyNote, this);
                            }
                        });
                    }
                }

                // Re-attach reading button event listeners
                const readingButtons = card.querySelectorAll('.reading-btn');
                readingButtons.forEach(button => {
                    // Remove any existing listeners by cloning the button
                    const newButton = button.cloneNode(true);
                    button.parentNode.replaceChild(newButton, button);

                    // Add the click event listener
                    newButton.addEventListener('click', function() {
                        const stickyNote = this.closest('.sticky-note');
                        const contentType = stickyNote.dataset.type;
                        const contentId = stickyNote.dataset.id;
                        const timeSlot = stickyNote.dataset.timeSlot;
                        const readingNumber = parseInt(this.dataset.readingNumber);
                        const maxReadings = parseInt(stickyNote.dataset.maxReadings);
                        const isCurrentlyRead = this.classList.contains('read');

                        // Check if this is a sequential click
                        if (!isCurrentlyRead) {
                            // For marking as read, check if previous buttons are read
                            const allButtons = stickyNote.querySelectorAll('.reading-btn');
                            let canClick = true;

                            // Check if all previous buttons are read
                            for (let i = 1; i < readingNumber; i++) {
                                const prevButton = Array.from(allButtons).find(btn =>
                                    parseInt(btn.dataset.readingNumber) === i
                                );
                                if (prevButton && !prevButton.classList.contains('read')) {
                                    canClick = false;
                                    break;
                                }
                            }

                            if (!canClick) {
                                alert(`Please click reading buttons in sequence. Click reading ${readingNumber - 1} first.`);
                                return;
                            }
                        }

                        // Toggle read status for this specific reading
                        const action = isCurrentlyRead ? 'unread' : 'read';
                        const url = `/presenter/mark-as-${action}`;

                        const requestBody = {
                            content_type: contentType,
                            content_id: contentId,
                            time_slot: timeSlot,
                            reading_number: readingNumber
                        };

                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(requestBody)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateReadingDisplay(stickyNote, data.read_count, data.individual_readings);

                                // Check if all readings are complete and update card color
                                if (data.read_count >= maxReadings) {
                                    stickyNote.classList.remove('unread');
                                    stickyNote.classList.add('read');
                                    stickyNote.style.backgroundColor = '#fee2e2';
                                    stickyNote.style.borderColor = '#dc2626';
                                    moveCardToBottom(stickyNote);
                                } else {
                                    stickyNote.classList.remove('read');
                                    stickyNote.classList.add('unread');
                                    stickyNote.style.backgroundColor = '';
                                    stickyNote.style.borderColor = '';
                                }
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while updating the read status.');
                        });
                    });
                });
            }

            function updateStats() {
                // Recalculate and update stats
                const totalRead = document.querySelectorAll('.sticky-note.read').length;
                const totalUnread = document.querySelectorAll('.sticky-note.unread').length;

                // Update stat cards (you can enhance this to update the actual numbers)
                location.reload(); // Simple reload for now, can be optimized
            }

            // Initialize button states on page load
            initializeButtonStates();

            // Initialize collapse functionality
            initializeCollapseButtons();

            // Initialize reload buttons
            initializeReloadButtons();

            // Initialize zoom controls
            initializeZoomControls();

            function initializeCollapseButtons() {
                // First, add collapse buttons to all sticky notes that don't have them
                document.querySelectorAll('.sticky-note').forEach(stickyNote => {
                    // Set collapsed attribute if not set
                    if (!stickyNote.dataset.collapsed) {
                        stickyNote.dataset.collapsed = 'false';
                    }

                    // Check if this sticky note already has a collapse button
                    if (!stickyNote.querySelector('.collapse-btn')) {
                        const header = stickyNote.querySelector('.sticky-note-header .flex.items-center.justify-between > div:last-child');
                        if (header) {
                            // Create collapse button
                            const collapseBtn = document.createElement('button');
                            collapseBtn.className = 'collapse-btn';
                            collapseBtn.title = 'Collapse/Expand';
                            collapseBtn.innerHTML = '<i class="fas fa-chevron-up"></i>';

                            // Insert at the beginning of the header controls
                            header.insertBefore(collapseBtn, header.firstChild);
                        }
                    }

                    // Wrap content in sticky-note-content if not already wrapped
                    const content = stickyNote.querySelector('.text-xs.opacity-75');
                    if (content && !content.closest('.sticky-note-content')) {
                        // Find all content after the header
                        const header = stickyNote.querySelector('.sticky-note-header');
                        if (header) {
                            const contentWrapper = document.createElement('div');
                            contentWrapper.className = 'sticky-note-content';

                            // Move all elements after header into content wrapper
                            let nextElement = header.nextElementSibling;
                            while (nextElement) {
                                const elementToMove = nextElement;
                                nextElement = nextElement.nextElementSibling;
                                contentWrapper.appendChild(elementToMove);
                            }

                            // Add the content wrapper after the header
                            header.parentNode.appendChild(contentWrapper);
                        }
                    }
                });

                // Add event listeners to all collapse buttons
                document.querySelectorAll('.collapse-btn').forEach(button => {
                    // Remove existing listeners to avoid duplicates
                    button.replaceWith(button.cloneNode(true));
                });

                document.querySelectorAll('.collapse-btn').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.stopPropagation(); // Prevent event bubbling

                        const stickyNote = this.closest('.sticky-note');
                        const isCollapsed = stickyNote.dataset.collapsed === 'true';

                        if (isCollapsed) {
                            // Expand vertically
                            expandCard(stickyNote, this);
                        } else {
                            // Collapse vertically
                            collapseCard(stickyNote, this);
                        }
                    });
                });
            }

            function collapseCard(stickyNote, button) {
                const content = stickyNote.querySelector('.sticky-note-content');
                if (!content) return;

                // Get the current height for smooth animation
                const currentHeight = content.scrollHeight;
                content.style.maxHeight = currentHeight + 'px';

                // Force reflow
                content.offsetHeight;

                // Animate to collapsed state
                content.style.maxHeight = '0px';
                content.style.opacity = '0';
                content.style.transform = 'translateY(-10px)';

                // Update button and card state
                stickyNote.classList.add('collapsed');
                stickyNote.dataset.collapsed = 'true';
                button.querySelector('i').classList.remove('fa-chevron-up');
                button.querySelector('i').classList.add('fa-chevron-down');
                button.title = 'Expand';

                // Set final collapsed height after animation
                setTimeout(() => {
                    if (stickyNote.classList.contains('collapsed')) {
                        stickyNote.style.maxHeight = '50px';
                    }
                }, 300);
            }

            function expandCard(stickyNote, button) {
                const content = stickyNote.querySelector('.sticky-note-content');
                if (!content) return;

                // Remove collapsed state first
                stickyNote.classList.remove('collapsed');
                stickyNote.dataset.collapsed = 'false';
                stickyNote.style.maxHeight = '';

                // Get the target height
                content.style.maxHeight = 'none';
                const targetHeight = content.scrollHeight;
                content.style.maxHeight = '0px';

                // Force reflow
                content.offsetHeight;

                // Animate to expanded state
                content.style.maxHeight = targetHeight + 'px';
                content.style.opacity = '1';
                content.style.transform = 'translateY(0)';

                // Update button state
                button.querySelector('i').classList.remove('fa-chevron-down');
                button.querySelector('i').classList.add('fa-chevron-up');
                button.title = 'Collapse';

                // Remove max-height after animation completes
                setTimeout(() => {
                    if (!stickyNote.classList.contains('collapsed')) {
                        content.style.maxHeight = '';
                    }
                }, 300);
            }

            function initializeReloadButtons() {
                // Reload Advertisements button
                const reloadAdsBtn = document.getElementById('reload-ads-btn');
                if (reloadAdsBtn) {
                    reloadAdsBtn.addEventListener('click', function() {
                        reloadSection('advertisements', this);
                    });
                }

                // Reload Gongs button
                const reloadGongsBtn = document.getElementById('reload-gongs-btn');
                if (reloadGongsBtn) {
                    reloadGongsBtn.addEventListener('click', function() {
                        reloadSection('gongs', this);
                    });
                }
            }

            function reloadSection(sectionType, button) {
                // Add loading state
                button.classList.add('loading');
                button.disabled = true;

                // Show loading feedback
                const originalText = button.querySelector('span').textContent;
                button.querySelector('span').textContent = 'Loading...';

                // Make AJAX request to reload the specific section
                fetch(`/presenter/reload-section/${sectionType}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showNotification(`${sectionType.charAt(0).toUpperCase() + sectionType.slice(1)} reloaded successfully!`, 'success');

                        // Reload the page to show updated data
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showNotification(`Failed to reload ${sectionType}`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(`Error reloading ${sectionType}`, 'error');
                })
                .finally(() => {
                    // Remove loading state
                    button.classList.remove('loading');
                    button.disabled = false;
                    button.querySelector('span').textContent = originalText;
                });
            }

            function showNotification(message, type = 'info') {
                // Create notification element
                const notification = document.createElement('div');
                notification.className = `notification notification-${type}`;
                notification.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>
                        <span>${message}</span>
                    </div>
                `;

                // Add notification styles
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 12px 20px;
                    border-radius: 8px;
                    color: white;
                    font-weight: 600;
                    z-index: 9999;
                    transform: translateX(100%);
                    transition: transform 0.3s ease;
                    background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                `;

                document.body.appendChild(notification);

                // Animate in
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                }, 100);

                // Remove after 3 seconds
                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }

            function initializeZoomControls() {
                // Add zoom controls to all sticky notes that don't have them
                document.querySelectorAll('.sticky-note').forEach(stickyNote => {
                    // Set initial zoom level
                    if (!stickyNote.dataset.zoom) {
                        stickyNote.dataset.zoom = '100';
                    }

                    // Check if this sticky note already has zoom controls
                    if (!stickyNote.querySelector('.zoom-controls')) {
                        const header = stickyNote.querySelector('.sticky-note-header .flex.items-center.justify-between > div:last-child');
                        if (header) {
                            // Create zoom controls
                            const zoomControls = document.createElement('div');
                            zoomControls.className = 'zoom-controls';
                            zoomControls.innerHTML = `
                                <button class="zoom-btn zoom-out" title="Zoom Out">
                                    <i class="fas fa-search-minus"></i>
                                </button>
                                <span class="zoom-level">100%</span>
                                <button class="zoom-btn zoom-in" title="Zoom In">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                            `;

                            // Insert at the beginning of the header controls
                            header.insertBefore(zoomControls, header.firstChild);
                        }
                    }
                });

                // Add event listeners to all zoom buttons
                document.querySelectorAll('.zoom-btn').forEach(button => {
                    // Remove existing listeners to avoid duplicates
                    button.replaceWith(button.cloneNode(true));
                });

                document.querySelectorAll('.zoom-btn').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.stopPropagation();

                        const stickyNote = this.closest('.sticky-note');
                        const isZoomIn = this.classList.contains('zoom-in');
                        const isZoomOut = this.classList.contains('zoom-out');

                        if (isZoomIn) {
                            zoomIn(stickyNote);
                        } else if (isZoomOut) {
                            zoomOut(stickyNote);
                        }
                    });
                });
            }

            function zoomIn(stickyNote) {
                const currentZoom = parseInt(stickyNote.dataset.zoom || '100');
                const zoomLevels = [75, 90, 100, 110, 125, 150, 175, 200];
                const currentIndex = zoomLevels.indexOf(currentZoom);

                if (currentIndex < zoomLevels.length - 1) {
                    const newZoom = zoomLevels[currentIndex + 1];
                    setZoom(stickyNote, newZoom);
                }
            }

            function zoomOut(stickyNote) {
                const currentZoom = parseInt(stickyNote.dataset.zoom || '100');
                const zoomLevels = [75, 90, 100, 110, 125, 150, 175, 200];
                const currentIndex = zoomLevels.indexOf(currentZoom);

                if (currentIndex > 0) {
                    const newZoom = zoomLevels[currentIndex - 1];
                    setZoom(stickyNote, newZoom);
                }
            }

            function setZoom(stickyNote, zoomLevel) {
                // Update zoom level
                stickyNote.dataset.zoom = zoomLevel.toString();
                stickyNote.setAttribute('data-zoom', zoomLevel.toString());

                // Update zoom level display
                const zoomLevelSpan = stickyNote.querySelector('.zoom-level');
                if (zoomLevelSpan) {
                    zoomLevelSpan.textContent = zoomLevel + '%';
                }

                // Add/remove zoomed class for enhanced styling
                if (zoomLevel !== 100) {
                    stickyNote.classList.add('zoomed');
                } else {
                    stickyNote.classList.remove('zoomed');
                }

                // Update button states
                const zoomInBtn = stickyNote.querySelector('.zoom-in');
                const zoomOutBtn = stickyNote.querySelector('.zoom-out');

                if (zoomInBtn && zoomOutBtn) {
                    // Disable zoom in at max level (200%)
                    if (zoomLevel >= 200) {
                        zoomInBtn.style.opacity = '0.5';
                        zoomInBtn.style.cursor = 'not-allowed';
                        zoomInBtn.disabled = true;
                    } else {
                        zoomInBtn.style.opacity = '1';
                        zoomInBtn.style.cursor = 'pointer';
                        zoomInBtn.disabled = false;
                    }

                    // Disable zoom out at min level (75%)
                    if (zoomLevel <= 75) {
                        zoomOutBtn.style.opacity = '0.5';
                        zoomOutBtn.style.cursor = 'not-allowed';
                        zoomOutBtn.disabled = true;
                    } else {
                        zoomOutBtn.style.opacity = '1';
                        zoomOutBtn.style.cursor = 'pointer';
                        zoomOutBtn.disabled = false;
                    }
                }
            }

            function initializeButtonStates() {
                document.querySelectorAll('.sticky-note').forEach(stickyNote => {
                    const readCount = parseInt(stickyNote.dataset.readCount);
                    const maxReadings = parseInt(stickyNote.dataset.maxReadings);
                    const buttons = stickyNote.querySelectorAll('.reading-btn');

                    // Update each button's availability
                    buttons.forEach(button => {
                        const readingNum = parseInt(button.dataset.readingNumber);
                        updateButtonAvailability(button, readingNum, null);
                    });

                    // Update card appearance
                    updateCardAppearance(stickyNote, readCount, maxReadings);
                });
            }

            // Request notification permission on page load
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }

            // Function to check for new content
            function checkForNewContent() {
                fetch('/presenter/check-new-content', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.has_new_content) {
                        showNewContentNotification(data);
                    }

                    // Update time slot indicator if changed
                    updateTimeSlotIndicator(data.current_time_slot, data.is_active_hours);
                })
                .catch(error => {
                    console.error('Error checking for new content:', error);
                });
            }

            // Function to show new content notification
            function showNewContentNotification(data) {
                const message = `New content available for ${data.time_slot_display}! ` +
                    `${data.new_advertisements_count} new ads, ${data.new_gongs_count} new gongs.`;

                // Browser notification
                if ('Notification' in window && Notification.permission === 'granted') {
                    const notification = new Notification('New Content Available', {
                        body: message,
                        icon: '/favicon.ico',
                        badge: '/favicon.ico'
                    });

                    notification.onclick = function() {
                        window.focus();
                        notification.close();
                        // Optionally reload the page to show new content
                        if (confirm('New content is available. Reload the page to see it?')) {
                            location.reload();
                        }
                    };
                }

                // In-page notification
                showInPageNotification(message, 'info');
            }

            // Function to show in-page notifications
            function showInPageNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
                    type === 'info' ? 'bg-blue-500 text-white' :
                    type === 'success' ? 'bg-green-500 text-white' :
                    'bg-red-500 text-white'
                }`;
                notification.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;

                document.body.appendChild(notification);

                // Auto-remove after 10 seconds
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 10000);
            }

            // Function to update time slot indicator
            function updateTimeSlotIndicator(currentTimeSlot, isActiveHours) {
                // Update the time slot display in the header
                const timeSlotElement = document.querySelector('.current-time-slot');
                if (timeSlotElement) {
                    timeSlotElement.textContent = currentTimeSlot.charAt(0).toUpperCase() + currentTimeSlot.slice(1);
                }

                // Update active hours indicator
                const statusIndicator = document.querySelector('.status-indicator');
                if (statusIndicator) {
                    statusIndicator.className = `w-2 h-2 rounded-full mr-2 ${isActiveHours ? 'bg-green-400' : 'bg-red-400'}`;
                }
            }

            // Real-time time slot monitoring
            let currentTimeSlot = '{{ $currentTimeSlot }}';
            let lastTimeSlotCheck = Date.now();

            // Detect client timezone
            const clientTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            console.log('Client timezone:', clientTimezone);
            console.log('Client time:', new Date().toLocaleString());
            console.log('Server timezone from PHP:', '{{ now()->timezoneName }}');
            console.log('Server time from PHP:', '{{ now()->format("Y-m-d H:i:s T") }}');

            // Function to check current time and time slot
            function checkCurrentTimeSlot() {
                fetch('/presenter/current-time-info', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update current time display
                    updateCurrentTimeDisplay(data.current_time);

                    // Always update time slot info (even if no change)
                    updateTimeSlotInfo(data);

                    // Check if time slot has changed
                    if (data.current_time_slot !== currentTimeSlot) {
                        handleTimeSlotChange(data);
                    } else {
                        // Update current time slot variable even if no change
                        currentTimeSlot = data.current_time_slot;
                    }

                    // Update next change countdown
                    updateNextChangeCountdown(data.next_time_slot_change);
                })
                .catch(error => {
                    console.error('Error checking current time slot:', error);
                });
            }

            // Function to handle time slot changes
            function handleTimeSlotChange(data) {
                const oldTimeSlot = currentTimeSlot;
                currentTimeSlot = data.current_time_slot;

                // Update session storage
                sessionStorage.setItem('current_time_slot', currentTimeSlot);

                // Show time slot change notification
                const message = `Time slot changed from ${oldTimeSlot} to ${currentTimeSlot}. Content will update automatically.`;

                // Browser notification
                if ('Notification' in window && Notification.permission === 'granted') {
                    const notification = new Notification('Time Slot Changed', {
                        body: message,
                        icon: '/favicon.ico'
                    });

                    notification.onclick = function() {
                        window.focus();
                        notification.close();
                    };
                }

                // In-page notification
                showTimeSlotChangeNotification(message, oldTimeSlot, currentTimeSlot);

                // Update content immediately without full page refresh
                updateContentForNewTimeSlot(data);

                // Also refresh after 3 seconds as fallback
                setTimeout(() => {
                    location.reload();
                }, 3000);
            }

            // Function to update content for new time slot without full refresh
            function updateContentForNewTimeSlot(data) {
                // Update section headers
                updateSectionHeaders(data.current_time_slot);

                // Update filtering notice
                updateFilteringNotice(data);

                // Fetch and update content
                fetchTimeSlotContent(data.current_time_slot);
            }

            // Function to update section headers
            function updateSectionHeaders(timeSlot) {
                const adHeader = document.querySelector('h2:contains("Advertisements")');
                const gongHeader = document.querySelector('h2:contains("Memorial Services")');

                if (adHeader) {
                    const timeSlotName = timeSlot.charAt(0).toUpperCase() + timeSlot.slice(1);
                    adHeader.innerHTML = adHeader.innerHTML.replace(/(Morning|Lunch|Evening) Advertisements/, `${timeSlotName} Advertisements`);
                }

                if (gongHeader) {
                    const timeSlotName = timeSlot.charAt(0).toUpperCase() + timeSlot.slice(1);
                    gongHeader.innerHTML = gongHeader.innerHTML.replace(/(Morning|Lunch|Evening) Memorial Services/, `${timeSlotName} Memorial Services`);
                }
            }

            // Function to update filtering notice
            function updateFilteringNotice(data) {
                const filteringNotice = document.querySelector('.bg-gradient-to-r.from-blue-50');
                if (filteringNotice) {
                    const timeSlotName = data.current_time_slot.charAt(0).toUpperCase() + data.current_time_slot.slice(1);
                    const statusText = data.is_active_hours ?
                        '<span class="text-green-600 font-medium">(Active Broadcasting Hours)</span>' :
                        '<span class="text-orange-600 font-medium">(Outside Broadcasting Hours)</span>';

                    filteringNotice.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-clock text-blue-600 text-xl mr-3"></i>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Time-Based Content Filtering</h3>
                                    <p class="text-sm text-gray-600">
                                        Currently showing <strong>${timeSlotName}</strong> content
                                        ${statusText}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Broadcasting Schedule:</div>
                                <div class="text-xs text-gray-400">
                                    Morning: 5AM-9AM | Lunch: 11AM-3PM | Evening: 4PM-11PM
                                </div>
                            </div>
                        </div>
                    `;
                }
            }

            // Function to fetch content for specific time slot
            function fetchTimeSlotContent(timeSlot) {
                fetch('/presenter/time-slot-content', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update content counts in stats cards
                    updateStatsCards(data);

                    // Update content sections (this would require more complex DOM manipulation)
                    // For now, we'll rely on the page refresh
                })
                .catch(error => {
                    console.error('Error fetching time slot content:', error);
                });
            }

            // Function to update stats cards
            function updateStatsCards(data) {
                // Update advertisement count
                const adCountElement = document.querySelector('.text-2xl.font-bold.text-gray-800');
                if (adCountElement && adCountElement.textContent.match(/^\d+$/)) {
                    adCountElement.textContent = data.advertisements_count;
                }

                // Update unread counts
                if (data.unread_counts) {
                    const morningCount = document.querySelector('.text-lg.font-bold.text-gray-800:contains("Morning:")');
                    const lunchCount = document.querySelector('.text-lg.font-bold.text-gray-800:contains("Lunch:")');
                    const eveningCount = document.querySelector('.text-lg.font-bold.text-gray-800:contains("Evening:")');

                    if (morningCount) morningCount.textContent = `🌅 Morning: ${data.unread_counts.morning}`;
                    if (lunchCount) lunchCount.textContent = `🍽️ Lunch: ${data.unread_counts.lunch}`;
                    if (eveningCount) eveningCount.textContent = `🌆 Evening: ${data.unread_counts.evening}`;
                }
            }

            // Function to show time slot change notification
            function showTimeSlotChangeNotification(message, oldTimeSlot, newTimeSlot) {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 z-50 p-6 rounded-lg shadow-lg max-w-md bg-gradient-to-r from-blue-500 to-purple-600 text-white';
                notification.innerHTML = `
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-lg">🕐 Time Slot Changed</h4>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <p class="text-sm mb-3">${message}</p>
                    <div class="flex justify-between items-center text-xs">
                        <span>From: <strong>${oldTimeSlot}</strong> → To: <strong>${newTimeSlot}</strong></span>
                        <span class="countdown">Refreshing in 3s...</span>
                    </div>
                `;

                document.body.appendChild(notification);

                // Countdown timer
                let countdown = 3;
                const countdownElement = notification.querySelector('.countdown');
                const countdownInterval = setInterval(() => {
                    countdown--;
                    if (countdown > 0) {
                        countdownElement.textContent = `Refreshing in ${countdown}s...`;
                    } else {
                        clearInterval(countdownInterval);
                        countdownElement.textContent = 'Refreshing now...';
                    }
                }, 1000);
            }

            // Function to update current time display
            function updateCurrentTimeDisplay(currentTime) {
                // Add a real-time clock to the header if it doesn't exist
                let clockElement = document.querySelector('.real-time-clock');
                if (!clockElement) {
                    clockElement = document.createElement('div');
                    clockElement.className = 'real-time-clock bg-white bg-opacity-20 rounded-lg px-4 py-2';
                    clockElement.innerHTML = `
                        <div class="text-sm">Current Time</div>
                        <div class="font-bold time-display">${currentTime}</div>
                    `;

                    // Insert after the time slot indicator
                    const timeSlotElement = document.querySelector('.current-time-slot').closest('.bg-white');
                    timeSlotElement.parentNode.insertBefore(clockElement, timeSlotElement.nextSibling);
                } else {
                    clockElement.querySelector('.time-display').textContent = currentTime;
                }
            }

            // Function to update time slot information
            function updateTimeSlotInfo(data) {
                console.log('Updating time slot info:', data);

                // Update active hours indicator
                const statusIndicator = document.querySelector('.status-indicator');
                if (statusIndicator) {
                    statusIndicator.className = `status-indicator w-2 h-2 rounded-full mr-2 ${data.is_active_hours ? 'bg-green-400' : 'bg-red-400'}`;
                }

                // Update time slot display using the new function
                updateTimeSlotDisplay(data.current_time_slot);

                // Update current time slot variable
                currentTimeSlot = data.current_time_slot;
            }

            // Function to update next change countdown
            function updateNextChangeCountdown(nextChange) {
                let countdownElement = document.querySelector('.next-change-countdown');
                if (!countdownElement && nextChange.minutes_until_change < 60) {
                    // Only show countdown if change is within 1 hour
                    countdownElement = document.createElement('div');
                    countdownElement.className = 'next-change-countdown bg-yellow-100 border border-yellow-300 rounded-lg p-3 mb-4';
                    countdownElement.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-clock text-yellow-600 mr-2"></i>
                            <span class="text-sm text-yellow-800">
                                Next time slot change in <strong class="countdown-minutes">${nextChange.minutes_until_change}</strong> minutes
                                (${nextChange.next_change_time})
                            </span>
                        </div>
                    `;

                    // Insert after the time-based filtering notice
                    const filteringNotice = document.querySelector('.bg-gradient-to-r.from-blue-50');
                    filteringNotice.parentNode.insertBefore(countdownElement, filteringNotice.nextSibling);
                } else if (countdownElement) {
                    const minutesElement = countdownElement.querySelector('.countdown-minutes');
                    if (minutesElement) {
                        minutesElement.textContent = nextChange.minutes_until_change;
                    }

                    // Remove countdown if more than 1 hour away
                    if (nextChange.minutes_until_change >= 60) {
                        countdownElement.remove();
                    }
                }
            }

            // Function to calculate current time slot based on client time
            function calculateCurrentTimeSlot() {
                const now = new Date();
                const hour = now.getHours();

                if (hour >= 5 && hour < 9) {
                    return 'morning';
                } else if (hour >= 11 && hour < 15) {
                    return 'lunch';
                } else if (hour >= 16 && hour < 23) {
                    return 'evening';
                } else {
                    return 'off-hours';
                }
            }

            // Function to update real-time clock and time slot
            function updateRealTimeClock() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('en-US', {
                    hour12: true,
                    hour: 'numeric',
                    minute: '2-digit',
                    second: '2-digit'
                });

                const realTimeElement = document.querySelector('.current-real-time');
                if (realTimeElement) {
                    realTimeElement.textContent = timeString;
                }

                // Update current hour display
                const currentHourElement = document.querySelector('.current-hour');
                if (currentHourElement) {
                    currentHourElement.textContent = now.getHours();
                }

                // Update expected slot display
                const expectedSlotElement = document.querySelector('.expected-slot');
                if (expectedSlotElement) {
                    const expectedSlot = calculateCurrentTimeSlot();
                    expectedSlotElement.textContent = expectedSlot.charAt(0).toUpperCase() + expectedSlot.slice(1);
                }

                // Update client-side time information
                const clientTimeElement = document.querySelector('.client-time');
                if (clientTimeElement) {
                    clientTimeElement.textContent = timeString;
                }

                const clientHourElement = document.querySelector('.client-hour');
                if (clientHourElement) {
                    clientHourElement.textContent = now.getHours();
                }

                const clientExpectedElement = document.querySelector('.client-expected');
                if (clientExpectedElement) {
                    const clientExpected = calculateCurrentTimeSlot();
                    clientExpectedElement.textContent = clientExpected.charAt(0).toUpperCase() + clientExpected.slice(1);
                }

                // Calculate current time slot based on client time
                const clientTimeSlot = calculateCurrentTimeSlot();

                // Update time slot display if different
                if (clientTimeSlot !== currentTimeSlot && clientTimeSlot !== 'off-hours') {
                    console.log('Client-side time slot change detected:', currentTimeSlot, '->', clientTimeSlot);

                    // Update the display immediately
                    updateTimeSlotDisplay(clientTimeSlot);

                    // Trigger server check
                    checkCurrentTimeSlot();
                }
            }

            // Function to update time slot display
            function updateTimeSlotDisplay(timeSlot) {
                const timeSlotElement = document.querySelector('.current-time-slot');
                if (timeSlotElement) {
                    let timeSlotDisplay = '';
                    switch(timeSlot) {
                        case 'morning':
                            timeSlotDisplay = '🌅 Morning (5AM-9AM)';
                            break;
                        case 'lunch':
                            timeSlotDisplay = '🍽️ Lunch (11AM-3PM)';
                            break;
                        case 'evening':
                            timeSlotDisplay = '🌆 Evening (4PM-11PM)';
                            break;
                        default:
                            timeSlotDisplay = '🕐 Off-Hours';
                    }
                    timeSlotElement.innerHTML = timeSlotDisplay;
                    console.log('Updated time slot display to:', timeSlotDisplay);
                }
            }

            // Start real-time monitoring
            // Update real-time clock every second
            setInterval(updateRealTimeClock, 1000);

            // Check time slot every 10 seconds for more responsive updates
            setInterval(checkCurrentTimeSlot, 10000);

            // Start checking for new content every 30 seconds
            setInterval(checkForNewContent, 30000);

            // Initial updates
            updateRealTimeClock();
            setTimeout(checkCurrentTimeSlot, 1000); // Check immediately after 1 second
            setTimeout(checkForNewContent, 5000);
        });
    </script>
</body>
</html>
