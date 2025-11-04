<?php

namespace App\Http\Controllers;

use App\Models\ContentForm;
use App\Models\ContentFormLog;
use App\Models\Advertisement;
use App\Models\Gong;
use App\Models\Presenter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentFormController extends Controller
{
    /**
     * Create or update content form from advertisement or gong
     */
    public function createFromContent($contentType, $contentId)
    {
        $content = null;

        if ($contentType === 'advertisement') {
            $content = Advertisement::findOrFail($contentId);
        } elseif ($contentType === 'gong') {
            $content = Gong::findOrFail($contentId);
        }

        // Calculate word count
        $plainText = strip_tags($content->content ?? $content->contents ?? '');
        $wordCount = str_word_count($plainText);

        // Get content summary (first 500 chars)
        $contentSummary = substr($plainText, 0, 500);

        // Create or update content form
        $form = ContentForm::updateOrCreate(
            [
                'content_type' => $contentType,
                'content_id' => $contentId,
            ],
            [
                'customer_id' => $content->customer_id,
                'title' => $content->title ?? $content->departed_name ?? 'Untitled',
                'content_summary' => $contentSummary,
                'word_count' => $wordCount,
                'source' => 'hand', // Default to hand, can be updated
                'received_date' => $content->issued_date ?? $content->published_date ?? now(),
                'amount' => $content->amount ?? 0,
                'is_paid' => $content->is_paid ?? false,
                'band' => $content->band ?? [],
                'broadcast_start_date' => $content->broadcast_start_date,
                'broadcast_end_date' => $content->broadcast_end_date,
                'broadcast_days' => $content->broadcast_days ?? [],
                'morning_frequency' => $content->morning_frequency ?? 0,
                'lunch_frequency' => $content->lunch_frequency ?? 0,
                'evening_frequency' => $content->evening_frequency ?? 0,
            ]
        );

        return $form;
    }

    /**
     * Record tick action for presenter
     * Real-time tracking of presenter reading actions
     */
    public function tick(Request $request)
    {
        try {
            $validated = $request->validate([
                'content_form_id' => 'required|exists:content_forms,id',
                'time_slot' => 'required|in:morning,lunch,evening',
                'reading_number' => 'nullable|integer|min:1',
            ]);

            $form = ContentForm::findOrFail($validated['content_form_id']);
            $presenter = auth('presenter')->user();
            $timeSlot = $validated['time_slot'];
            $readingNumber = $validated['reading_number'] ?? null;

            if (!$presenter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Presenter not authenticated',
                ], 401);
            }

            // Get current tick count
            $tickCountField = $timeSlot . '_tick_count';
            $currentCount = $form->$tickCountField ?? 0;
            $requiredCount = $form->{$timeSlot . '_frequency'} ?? 0;

            // Check if already completed for this slot
            if ($currentCount >= $requiredCount && $requiredCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'All required readings completed for this time slot',
                ], 422);
            }

            // Increment tick count
            $newCount = $currentCount + 1;

            // Get existing tick times array
            $tickTimesField = $timeSlot . '_tick_times';
            $existingTickTimes = $form->$tickTimesField ?? [];
            if (!is_array($existingTickTimes)) {
                $existingTickTimes = [];
            }

            // Add new tick time to array
            $existingTickTimes[] = now()->toDateTimeString();

            // Get or initialize individual readings tracking
            $readingsField = $timeSlot . '_readings';
            $readings = $form->$readingsField ?? [];
            if (!is_array($readings)) {
                $readings = [];
            }

            // If reading_number is provided, track this specific reading
            if ($readingNumber) {
                $readings[$readingNumber] = [
                    'ticked' => true,
                    'ticked_at' => now()->toDateTimeString(),
                    'presenter_id' => $presenter->id,
                    'presenter_name' => $presenter->name,
                ];
            }

            // Update ContentForm with tick information
            $updateData = [
                $tickCountField => $newCount,
                $timeSlot . '_ticked_at' => now(),
                $tickTimesField => $existingTickTimes, // Store all tick times
                $readingsField => $readings, // Store individual reading status
                'presenter_id' => $presenter->id, // Store presenter ID
                'presenter_shift' => $timeSlot, // Store current shift
            ];

            $form->update($updateData);

            // Log the action with all details
            $log = ContentFormLog::create([
                'content_form_id' => $form->id,
                'presenter_id' => $presenter->id,
                'action' => 'tick',
                'time_slot' => $timeSlot,
                'action_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'reading_number' => $newCount,
                'notes' => "Presenter {$presenter->name} ticked reading #{$newCount} for {$timeSlot} shift",
            ]);

            // Log to activity log
            activity('content_form_tick')
                ->causedBy($presenter)
                ->performedOn($form)
                ->withProperties([
                    'content_form_id' => $form->id,
                    'time_slot' => $timeSlot,
                    'reading_number' => $newCount,
                    'presenter_name' => $presenter->name,
                    'action_at' => now()->toDateTimeString(),
                ])
                ->log("Presenter {$presenter->name} ticked reading #{$newCount} for {$timeSlot}");

            // Refresh form to get updated values
            $form->refresh();

            // Check if all readings are completed
            $totalRequired = $form->morning_frequency + $form->lunch_frequency + $form->evening_frequency;
            $totalCompleted = $form->morning_tick_count + $form->lunch_tick_count + $form->evening_tick_count;

            if ($totalCompleted >= $totalRequired && $totalRequired > 0) {
                $form->update([
                    'is_completed' => true,
                    'completed_at' => now(),
                ]);

                // Mark the content (Advertisement or Gong) as read
                if ($form->content_type === 'advertisement') {
                    Advertisement::where('id', $form->content_id)->update(['is_read' => true]);
                } elseif ($form->content_type === 'gong') {
                    Gong::where('id', $form->content_id)->update(['is_read' => true]);
                }

                // Log the content as read
                activity('content_marked_as_read')
                    ->causedBy($presenter)
                    ->performedOn($form)
                    ->withProperties([
                        'content_type' => $form->content_type,
                        'content_id' => $form->content_id,
                        'content_title' => $form->title,
                        'total_ticks' => $totalCompleted,
                    ])
                    ->log("Content '{$form->title}' marked as read after {$totalCompleted} readings");
            }

            // Get the readings field for this time slot
            $readingsField = $timeSlot . '_readings';
            $individualReadings = $form->$readingsField ?? [];

            return response()->json([
                'success' => true,
                'message' => "Reading #{$newCount} recorded successfully for {$timeSlot}",
                'tick_count' => $newCount,
                'is_completed' => $form->is_completed,
                'progress' => $form->reading_progress,
                'presenter_name' => $presenter->name,
                'log_id' => $log->id,
                'timestamp' => now()->toDateTimeString(),
                'individual_readings' => $individualReadings, // Include individual reading status
                'reading_number' => $readingNumber, // Include the reading number that was ticked
            ]);
        } catch (\Exception $e) {
            \Log::error('ContentForm tick error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'error' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error recording reading: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Record untick action for presenter
     * Real-time tracking of presenter reading removal
     */
    public function untick(Request $request)
    {
        try {
            $validated = $request->validate([
                'content_form_id' => 'required|exists:content_forms,id',
                'time_slot' => 'required|in:morning,lunch,evening',
                'reading_number' => 'nullable|integer|min:1',
            ]);

            $form = ContentForm::findOrFail($validated['content_form_id']);
            $presenter = auth('presenter')->user();
            $timeSlot = $validated['time_slot'];
            $readingNumber = $validated['reading_number'] ?? null;

            if (!$presenter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Presenter not authenticated',
                ], 401);
            }

            // Get current tick count
            $tickCountField = $timeSlot . '_tick_count';
            $currentCount = $form->$tickCountField ?? 0;

            // Can't untick if count is 0
            if ($currentCount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No readings to remove',
                ], 422);
            }

            // Decrement tick count
            $newCount = $currentCount - 1;

            // Get existing tick times array and remove the last one
            $tickTimesField = $timeSlot . '_tick_times';
            $existingTickTimes = $form->$tickTimesField ?? [];
            if (!is_array($existingTickTimes)) {
                $existingTickTimes = [];
            }

            // Remove the last tick time
            if (!empty($existingTickTimes)) {
                array_pop($existingTickTimes);
            }

            // Get individual readings tracking
            $readingsField = $timeSlot . '_readings';
            $readings = $form->$readingsField ?? [];
            if (!is_array($readings)) {
                $readings = [];
            }

            // If reading_number is provided, mark this specific reading as unticked
            if ($readingNumber && isset($readings[$readingNumber])) {
                $readings[$readingNumber] = [
                    'ticked' => false,
                    'unticked_at' => now()->toDateTimeString(),
                    'presenter_id' => $presenter->id,
                    'presenter_name' => $presenter->name,
                ];
            }

            // Update ContentForm
            $form->update([
                $tickCountField => $newCount,
                $tickTimesField => $existingTickTimes, // Update tick times array
                $readingsField => $readings, // Update individual reading status
                'presenter_id' => $presenter->id,
                'presenter_shift' => $timeSlot,
            ]);

            // Log the action with all details
            $log = ContentFormLog::create([
                'content_form_id' => $form->id,
                'presenter_id' => $presenter->id,
                'action' => 'untick',
                'time_slot' => $timeSlot,
                'action_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'reading_number' => $newCount,
                'notes' => "Presenter {$presenter->name} removed reading, now at #{$newCount} for {$timeSlot} shift",
            ]);

            // Log to activity log
            activity('content_form_untick')
                ->causedBy($presenter)
                ->performedOn($form)
                ->withProperties([
                    'content_form_id' => $form->id,
                    'time_slot' => $timeSlot,
                    'reading_number' => $newCount,
                    'presenter_name' => $presenter->name,
                    'action_at' => now()->toDateTimeString(),
                ])
                ->log("Presenter {$presenter->name} removed reading, now at #{$newCount} for {$timeSlot}");

            // Mark as not completed if unticked
            if ($form->is_completed) {
                $form->update([
                    'is_completed' => false,
                    'completed_at' => null,
                ]);
            }

            // Refresh to get updated values
            $form->refresh();

            // Get the readings field for this time slot
            $readingsField = $timeSlot . '_readings';
            $individualReadings = $form->$readingsField ?? [];

            return response()->json([
                'success' => true,
                'message' => "Reading removed successfully for {$timeSlot}",
                'tick_count' => $newCount,
                'is_completed' => $form->is_completed,
                'progress' => $form->reading_progress,
                'presenter_name' => $presenter->name,
                'log_id' => $log->id,
                'timestamp' => now()->toDateTimeString(),
                'individual_readings' => $individualReadings, // Include individual reading status
                'reading_number' => $readingNumber, // Include the reading number that was unticked
            ]);
        } catch (\Exception $e) {
            \Log::error('ContentForm untick error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'error' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error removing reading: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get content form details
     */
    public function show($id)
    {
        $form = ContentForm::with(['logs', 'presenter', 'customer'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $form,
            'logs' => $form->logs()->orderBy('action_at', 'desc')->get(),
        ]);
    }

    /**
     * Get all content forms for presenter
     * Returns all forms (not just ones with presenter_id set)
     * because presenter_id is only set after first tick
     */
    public function getPresenterForms()
    {
        // Get all content forms (not filtered by presenter_id)
        // because presenter_id is only set after the first tick
        $forms = ContentForm::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $forms,
        ]);
    }
}
