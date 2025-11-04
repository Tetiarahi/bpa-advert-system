<?php

namespace App\Observers;

use App\Models\Gong;
use App\Models\ContentForm;
use App\Http\Controllers\ContentFormController;

class GongObserver
{
    /**
     * Handle the Gong "created" event.
     */
    public function created(Gong $gong): void
    {
        // Create ContentForm entry when gong is created
        $controller = new ContentFormController();
        $controller->createFromContent('gong', $gong->id);
    }

    /**
     * Handle the Gong "updated" event.
     */
    public function updated(Gong $gong): void
    {
        // Update ContentForm when gong is updated
        $plainText = strip_tags($gong->contents ?? '');
        $wordCount = str_word_count($plainText);
        $contentSummary = substr($plainText, 0, 500);

        ContentForm::where('content_type', 'gong')
            ->where('content_id', $gong->id)
            ->update([
                'title' => $gong->departed_name,
                'content_summary' => $contentSummary,
                'word_count' => $wordCount,
                'amount' => $gong->amount,
                'is_paid' => $gong->is_paid,
                'band' => $gong->band,
                'broadcast_start_date' => $gong->broadcast_start_date,
                'broadcast_end_date' => $gong->broadcast_end_date,
                'broadcast_days' => $gong->broadcast_days,
                'morning_frequency' => $gong->morning_frequency,
                'lunch_frequency' => $gong->lunch_frequency,
                'evening_frequency' => $gong->evening_frequency,
            ]);
    }

    /**
     * Handle the Gong "deleted" event.
     */
    public function deleted(Gong $gong): void
    {
        // Delete associated ContentForm when gong is deleted
        ContentForm::where('content_type', 'gong')
            ->where('content_id', $gong->id)
            ->delete();
    }

    /**
     * Handle the Gong "restored" event.
     */
    public function restored(Gong $gong): void
    {
        //
    }

    /**
     * Handle the Gong "force deleted" event.
     */
    public function forceDeleted(Gong $gong): void
    {
        // Force delete associated ContentForm when gong is force deleted
        ContentForm::where('content_type', 'gong')
            ->where('content_id', $gong->id)
            ->forceDelete();
    }
}
