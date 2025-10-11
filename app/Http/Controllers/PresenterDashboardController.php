<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Advertisement;
use App\Models\Gong;
use App\Models\PresenterReadStatus;
use App\Services\TimeSlotService;
use Carbon\Carbon;

class PresenterDashboardController extends Controller
{
    public function index()
    {
        $presenter = Auth::guard('presenter')->user();
        $today = Carbon::today();

        // Always use real-time for time slot detection
        $now = Carbon::now();
        $currentTimeSlot = TimeSlotService::getCurrentTimeSlot();
        $isActiveHours = TimeSlotService::isActiveHours();

        // Get ALL AM advertisements for today's broadcast
        $allAdvertisements = $this->getAllAMAdvertisements($today, $presenter);

        // Get ALL AM gongs (memorials) for today's broadcast
        $allGongs = $this->getAllAMGongs($today, $presenter);

        // Filter content based on current time slot
        $advertisements = $this->filterContentByTimeSlot($allAdvertisements, $currentTimeSlot);
        $gongs = $this->filterContentByTimeSlot($allGongs, $currentTimeSlot);

        // Sort content to put completed items at the bottom
        $advertisements = $this->sortContentByCompletion($advertisements, $currentTimeSlot);
        $gongs = $this->sortContentByCompletion($gongs, $currentTimeSlot);

        // Calculate unread counts for all time slots (for stats display)
        try {
            $unreadCounts = [
                'morning' => $presenter->getUnreadCountByTimeSlot('morning', $allAdvertisements, $allGongs),
                'lunch' => $presenter->getUnreadCountByTimeSlot('lunch', $allAdvertisements, $allGongs),
                'evening' => $presenter->getUnreadCountByTimeSlot('evening', $allAdvertisements, $allGongs),
            ];
        } catch (\Exception $e) {
            // Fallback to zero counts if method doesn't exist
            $unreadCounts = [
                'morning' => 0,
                'lunch' => 0,
                'evening' => 0,
            ];
        }

        // Store session data for real-time tracking
        session([
            'last_content_check' => $now,
            'current_time_slot' => $currentTimeSlot,
            'last_time_slot_check' => $now,
            'login_time' => session('login_time', $now)
        ]);

        return view('presenter.dashboard', compact(
            'advertisements',
            'gongs',
            'currentTimeSlot',
            'presenter',
            'unreadCounts',
            'isActiveHours'
        ));
    }



    private function getCurrentTimeSlot($hour)
    {
        if ($hour >= 6 && $hour < 8) {
            return 'morning';
        } elseif ($hour >= 12 && $hour < 14) {
            return 'lunch';
        } elseif ($hour >= 17 && $hour < 21.5) {
            return 'evening';
        }

        return 'all'; // Show all content outside specific time slots
    }

    /**
     * Filter content based on current time slot and frequency
     */
    private function filterContentByTimeSlot($content, $timeSlot)
    {
        return $content->filter(function ($item) use ($timeSlot) {
            $frequencyField = $timeSlot . '_frequency';
            return isset($item->$frequencyField) && $item->$frequencyField > 0;
        });
    }

    private function getAllAMAdvertisements($today, $presenter)
    {
        $query = Advertisement::where('broadcast_start_date', '<=', $today)
            ->where('broadcast_end_date', '>=', $today)
            ->where('is_paid', true);

        $advertisements = $query->with('customer', 'adsCategory')->get();

        // Filter for AM band only and add read status
        $amAdvertisements = $advertisements->filter(function ($ad) {
            $band = $ad->band;
            if (is_array($band)) {
                return in_array('AM', $band);
            }
            return $band === 'AM' || str_contains(strtoupper($band), 'AM');
        });

        // Add read status and frequency info to each advertisement
        foreach ($amAdvertisements as $ad) {
            // Add time-slot-specific read counts
            $ad->read_count_morning = $presenter->getReadCount('advertisement', $ad->id, 'morning');
            $ad->read_count_lunch = $presenter->getReadCount('advertisement', $ad->id, 'lunch');
            $ad->read_count_evening = $presenter->getReadCount('advertisement', $ad->id, 'evening');

            // Add individual reading statuses
            $ad->readings_morning = $this->getIndividualReadings($presenter, 'advertisement', $ad->id, 'morning');
            $ad->readings_lunch = $this->getIndividualReadings($presenter, 'advertisement', $ad->id, 'lunch');
            $ad->readings_evening = $this->getIndividualReadings($presenter, 'advertisement', $ad->id, 'evening');

            // Add frequency breakdown for sticky notes
            $ad->morning_freq = $ad->morning_frequency ?? 0;
            $ad->lunch_freq = $ad->lunch_frequency ?? 0;
            $ad->evening_freq = $ad->evening_frequency ?? 0;

            // Add legacy read status for backward compatibility
            $ad->is_read_morning = $ad->read_count_morning > 0;
            $ad->is_read_lunch = $ad->read_count_lunch > 0;
            $ad->is_read_evening = $ad->read_count_evening > 0;

            // Add completion status for sorting
            $ad->is_morning_complete = $ad->read_count_morning >= $ad->morning_freq && $ad->morning_freq > 0;
            $ad->is_lunch_complete = $ad->read_count_lunch >= $ad->lunch_freq && $ad->lunch_freq > 0;
            $ad->is_evening_complete = $ad->read_count_evening >= $ad->evening_freq && $ad->evening_freq > 0;
        }

        return $amAdvertisements;
    }

    private function getAllAMGongs($today, $presenter)
    {
        $query = Gong::where('broadcast_start_date', '<=', $today)
            ->where('broadcast_end_date', '>=', $today);

        $gongs = $query->with('customer')->get();

        // Filter for AM band only and add read status
        $amGongs = $gongs->filter(function ($gong) {
            $band = $gong->band;
            if (is_array($band)) {
                return in_array('AM', $band);
            }
            return $band === 'AM' || str_contains(strtoupper($band), 'AM');
        });

        // Add read status and frequency info to each gong
        foreach ($amGongs as $gong) {
            // Add time-slot-specific read counts
            $gong->read_count_morning = $presenter->getReadCount('gong', $gong->id, 'morning');
            $gong->read_count_lunch = $presenter->getReadCount('gong', $gong->id, 'lunch');
            $gong->read_count_evening = $presenter->getReadCount('gong', $gong->id, 'evening');

            // Add individual reading statuses
            $gong->readings_morning = $this->getIndividualReadings($presenter, 'gong', $gong->id, 'morning');
            $gong->readings_lunch = $this->getIndividualReadings($presenter, 'gong', $gong->id, 'lunch');
            $gong->readings_evening = $this->getIndividualReadings($presenter, 'gong', $gong->id, 'evening');

            // Add frequency breakdown for sticky notes
            $gong->morning_freq = $gong->morning_frequency ?? 0;
            $gong->lunch_freq = $gong->lunch_frequency ?? 0;
            $gong->evening_freq = $gong->evening_frequency ?? 0;

            // Add legacy read status for backward compatibility
            $gong->is_read_morning = $gong->read_count_morning > 0;
            $gong->is_read_lunch = $gong->read_count_lunch > 0;
            $gong->is_read_evening = $gong->read_count_evening > 0;

            // Add completion status for sorting
            $gong->is_morning_complete = $gong->read_count_morning >= $gong->morning_freq && $gong->morning_freq > 0;
            $gong->is_lunch_complete = $gong->read_count_lunch >= $gong->lunch_freq && $gong->lunch_freq > 0;
            $gong->is_evening_complete = $gong->read_count_evening >= $gong->evening_freq && $gong->evening_freq > 0;
        }

        return $amGongs;
    }

    private function sortContentByCompletion($content, $timeSlot)
    {
        return $content->sort(function ($a, $b) use ($timeSlot) {
            $completionField = "is_{$timeSlot}_complete";

            // Get completion status for both items
            $aComplete = $a->$completionField ?? false;
            $bComplete = $b->$completionField ?? false;

            // If completion status is different, incomplete items come first
            if ($aComplete !== $bComplete) {
                return $aComplete ? 1 : -1; // Incomplete (false) comes before complete (true)
            }

            // If both have same completion status, sort by customer name
            $aName = $a->customer->fullname ?? 'Unknown';
            $bName = $b->customer->fullname ?? 'Unknown';

            return strcasecmp($aName, $bName);
        });
    }

    public function markAsRead(Request $request)
    {
        try {
            $presenter = Auth::guard('presenter')->user();

            if (!$presenter) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $request->validate([
                'content_type' => 'required|in:advertisement,gong',
                'content_id' => 'required|integer',
                'time_slot' => 'required|in:morning,lunch,evening',
                'reading_number' => 'required|integer|min:1|max:9',
            ]);

            $readingNumber = $request->reading_number;

            $result = $presenter->markAsRead(
                $request->content_type,
                $request->content_id,
                $request->time_slot,
                $readingNumber
            );

            if ($result === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum readings (9) already reached for this time slot'
                ], 400);
            }

            $readCount = $presenter->getReadCount($request->content_type, $request->content_id, $request->time_slot);
            $individualReadings = $this->getIndividualReadings($presenter, $request->content_type, $request->content_id, $request->time_slot);

            return response()->json([
                'success' => true,
                'message' => 'Marked as read for ' . $request->time_slot . ' slot',
                'read_count' => $readCount,
                'reading_number' => $result->reading_number,
                'individual_readings' => $individualReadings
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in markAsRead: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAsUnread(Request $request)
    {
        try {
            $presenter = Auth::guard('presenter')->user();

            if (!$presenter) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $request->validate([
                'content_type' => 'required|in:advertisement,gong',
                'content_id' => 'required|integer',
                'time_slot' => 'required|in:morning,lunch,evening',
                'reading_number' => 'required|integer|min:1|max:9',
            ]);

            $presenter->markAsUnread(
                $request->content_type,
                $request->content_id,
                $request->time_slot,
                $request->reading_number
            );

            $readCount = $presenter->getReadCount($request->content_type, $request->content_id, $request->time_slot);
            $individualReadings = $this->getIndividualReadings($presenter, $request->content_type, $request->content_id, $request->time_slot);

            return response()->json([
                'success' => true,
                'message' => 'Marked as unread for ' . $request->time_slot . ' slot',
                'read_count' => $readCount,
                'individual_readings' => $individualReadings
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in markAsUnread: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getIndividualReadings($presenter, $contentType, $contentId, $timeSlot)
    {
        $readStatus = $presenter->readStatuses()
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->where('time_slot', $timeSlot)
            ->first();

        if (!$readStatus || !$readStatus->readings_data) {
            // Return false for all 9 possible readings
            $result = [];
            for ($i = 1; $i <= 9; $i++) {
                $result[$i] = false;
            }
            return $result;
        }

        $readingsData = $readStatus->readings_data;
        $result = [];

        for ($i = 1; $i <= 9; $i++) {
            $result[$i] = isset($readingsData[$i]) && $readingsData[$i]['is_read'];
        }

        return $result;
    }

    // Temporary debug method
    public function debugReadings(Request $request)
    {
        $presenter = Auth::guard('presenter')->user();

        $contentType = $request->get('content_type', 'advertisement');
        $contentId = $request->get('content_id', 1);
        $timeSlot = $request->get('time_slot', 'morning');

        $readStatus = $presenter->readStatuses()
            ->where('content_type', $contentType)
            ->where('content_id', $contentId)
            ->where('time_slot', $timeSlot)
            ->first();

        return response()->json([
            'read_status' => $readStatus,
            'readings_data' => $readStatus ? $readStatus->readings_data : null,
            'read_count' => $presenter->getReadCount($contentType, $contentId, $timeSlot),
            'individual_readings' => $this->getIndividualReadings($presenter, $contentType, $contentId, $timeSlot)
        ]);
    }

    /**
     * Check for new content added since last check
     */
    public function checkNewContent(Request $request)
    {
        $presenter = Auth::guard('presenter')->user();
        $currentTimeSlot = TimeSlotService::getCurrentTimeSlot();
        $lastCheck = session('last_content_check', now()->subMinutes(5));

        $today = Carbon::today();

        // Get new advertisements added since last check
        $newAdvertisements = Advertisement::where('broadcast_start_date', '<=', $today)
            ->where('broadcast_end_date', '>=', $today)
            ->where('band', 'AM')
            ->where('created_at', '>', $lastCheck)
            ->get();

        // Get new gongs added since last check
        $newGongs = Gong::where('broadcast_start_date', '<=', $today)
            ->where('broadcast_end_date', '>=', $today)
            ->where('band', 'AM')
            ->where('created_at', '>', $lastCheck)
            ->get();

        // Filter by current time slot
        $newAdvertisements = $this->filterContentByTimeSlot($newAdvertisements, $currentTimeSlot);
        $newGongs = $this->filterContentByTimeSlot($newGongs, $currentTimeSlot);

        // Update last check time
        session(['last_content_check' => now()]);

        return response()->json([
            'has_new_content' => $newAdvertisements->count() > 0 || $newGongs->count() > 0,
            'new_advertisements_count' => $newAdvertisements->count(),
            'new_gongs_count' => $newGongs->count(),
            'current_time_slot' => $currentTimeSlot,
            'time_slot_display' => TimeSlotService::getTimeSlotDisplayName($currentTimeSlot),
            'is_active_hours' => TimeSlotService::isActiveHours(),
            'new_advertisements' => $newAdvertisements->map(function($ad) {
                return [
                    'id' => $ad->id,
                    'customer_name' => $ad->customer->fullname ?? 'Unknown',
                    'category' => $ad->adsCategory->name ?? 'General'
                ];
            }),
            'new_gongs' => $newGongs->map(function($gong) {
                return [
                    'id' => $gong->id,
                    'customer_name' => $gong->customer->fullname ?? 'Unknown'
                ];
            })
        ]);
    }

    /**
     * Get current time slot and time information for real-time updates
     */
    public function getCurrentTimeInfo(Request $request)
    {
        $currentTimeSlot = TimeSlotService::getCurrentTimeSlot();
        $isActiveHours = TimeSlotService::isActiveHours();
        $now = Carbon::now();

        return response()->json([
            'current_time_slot' => $currentTimeSlot,
            'is_active_hours' => $isActiveHours,
            'time_slot_display' => TimeSlotService::getTimeSlotDisplayName($currentTimeSlot),
            'current_time' => $now->format('H:i:s'),
            'current_hour' => $now->hour,
            'current_minute' => $now->minute,
            'time_slot_ranges' => [
                'morning' => TimeSlotService::getTimeSlotRange('morning'),
                'lunch' => TimeSlotService::getTimeSlotRange('lunch'),
                'evening' => TimeSlotService::getTimeSlotRange('evening'),
            ],
            'next_time_slot_change' => $this->getNextTimeSlotChange($now),
            'session_time_slot' => session('current_time_slot'),
            'needs_refresh' => session('current_time_slot') !== $currentTimeSlot
        ]);
    }

    /**
     * Calculate when the next time slot change will occur
     */
    private function getNextTimeSlotChange($now)
    {
        $hour = $now->hour;
        $minute = $now->minute;

        // Calculate minutes until next time slot change
        if ($hour < 5) {
            // Before morning slot - next change at 5:00 AM
            $nextChange = $now->copy()->setTime(5, 0, 0);
        } elseif ($hour < 9) {
            // In morning slot - next change at 9:00 AM (end of morning)
            $nextChange = $now->copy()->setTime(9, 0, 0);
        } elseif ($hour < 11) {
            // Between morning and lunch - next change at 11:00 AM
            $nextChange = $now->copy()->setTime(11, 0, 0);
        } elseif ($hour < 15) {
            // In lunch slot - next change at 3:00 PM (15:00)
            $nextChange = $now->copy()->setTime(15, 0, 0);
        } elseif ($hour < 16) {
            // Between lunch and evening - next change at 4:00 PM (16:00)
            $nextChange = $now->copy()->setTime(16, 0, 0);
        } elseif ($hour < 23) {
            // In evening slot - next change at 11:00 PM (23:00)
            $nextChange = $now->copy()->setTime(23, 0, 0);
        } else {
            // After evening slot - next change at 5:00 AM tomorrow
            $nextChange = $now->copy()->addDay()->setTime(5, 0, 0);
        }

        $minutesUntilChange = $now->diffInMinutes($nextChange);

        return [
            'next_change_time' => $nextChange->format('H:i'),
            'minutes_until_change' => $minutesUntilChange,
            'next_time_slot' => $this->getTimeSlotForHour($nextChange->hour)
        ];
    }

    /**
     * Get time slot for a specific hour
     */
    private function getTimeSlotForHour($hour)
    {
        if ($hour >= 5 && $hour < 9) {
            return 'morning';
        } elseif ($hour >= 11 && $hour < 15) {
            return 'lunch';
        } elseif ($hour >= 16 && $hour < 23) {
            return 'evening';
        }
        return 'off-hours';
    }

    /**
     * Get content for current time slot (AJAX endpoint)
     */
    public function getTimeSlotContent(Request $request)
    {
        $presenter = Auth::guard('presenter')->user();
        $currentTimeSlot = TimeSlotService::getCurrentTimeSlot();
        $today = Carbon::today();

        // Get all content
        $allAdvertisements = $this->getAllAMAdvertisements($today, $presenter);
        $allGongs = $this->getAllAMGongs($today, $presenter);

        // Filter by current time slot
        $advertisements = $this->filterContentByTimeSlot($allAdvertisements, $currentTimeSlot);
        $gongs = $this->filterContentByTimeSlot($allGongs, $currentTimeSlot);

        // Calculate unread counts
        try {
            $unreadCounts = [
                'morning' => $presenter->getUnreadCountByTimeSlot('morning', $allAdvertisements, $allGongs),
                'lunch' => $presenter->getUnreadCountByTimeSlot('lunch', $allAdvertisements, $allGongs),
                'evening' => $presenter->getUnreadCountByTimeSlot('evening', $allAdvertisements, $allGongs),
            ];
        } catch (\Exception $e) {
            $unreadCounts = ['morning' => 0, 'lunch' => 0, 'evening' => 0];
        }

        // Update session
        session(['current_time_slot' => $currentTimeSlot]);

        return response()->json([
            'current_time_slot' => $currentTimeSlot,
            'is_active_hours' => TimeSlotService::isActiveHours(),
            'time_slot_display' => TimeSlotService::getTimeSlotDisplayName($currentTimeSlot),
            'advertisements_count' => $advertisements->count(),
            'gongs_count' => $gongs->count(),
            'unread_counts' => $unreadCounts,
            'total_unread' => array_sum($unreadCounts),
            'advertisements' => $advertisements->map(function($ad) use ($currentTimeSlot) {
                $frequencyField = $currentTimeSlot . '_freq';
                $readCountField = 'read_count_' . $currentTimeSlot;
                return [
                    'id' => $ad->id,
                    'customer_name' => $ad->customer->fullname ?? 'Unknown',
                    'category' => $ad->adsCategory->name ?? 'General',
                    'content' => \Str::limit($ad->plain_content, 100),
                    'frequency' => $ad->$frequencyField ?? 0,
                    'read_count' => $ad->$readCountField ?? 0,
                    'is_read' => ($ad->$readCountField ?? 0) >= min($ad->$frequencyField ?? 0, 9)
                ];
            }),
            'gongs' => $gongs->map(function($gong) use ($currentTimeSlot) {
                $frequencyField = $currentTimeSlot . '_freq';
                $readCountField = 'read_count_' . $currentTimeSlot;
                return [
                    'id' => $gong->id,
                    'customer_name' => $gong->customer->fullname ?? 'Unknown',
                    'frequency' => $gong->$frequencyField ?? 0,
                    'read_count' => $gong->$readCountField ?? 0,
                    'is_read' => ($gong->$readCountField ?? 0) >= min($gong->$frequencyField ?? 0, 9)
                ];
            })
        ]);
    }

    public function reloadSection($sectionType)
    {
        try {
            // Clear any relevant caches
            \Cache::forget('presenter_dashboard_data');

            // You can add specific logic here to refresh data from external sources
            // For now, we'll just return success to trigger a page reload

            $message = '';
            switch ($sectionType) {
                case 'advertisements':
                    $message = 'Advertisements data refreshed successfully';
                    break;
                case 'gongs':
                    $message = 'Memorial services data refreshed successfully';
                    break;
                default:
                    $message = 'Section refreshed successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reload section: ' . $e->getMessage()
            ], 500);
        }
    }
}
