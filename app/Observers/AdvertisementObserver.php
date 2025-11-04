<?php

namespace App\Observers;

use App\Models\Advertisement;
use App\Models\ContentForm;
use App\Http\Controllers\ContentFormController;

class AdvertisementObserver
{
    /**
     * Handle the Advertisement "created" event.
     */
    public function created(Advertisement $advertisement): void
    {
        // Create ContentForm entry when advertisement is created
        $controller = new ContentFormController();
        $controller->createFromContent('advertisement', $advertisement->id);
    }

    /**
     * Handle the Advertisement "updated" event.
     */
    public function updated(Advertisement $advertisement): void
    {
        // Update ContentForm when advertisement is updated
        $plainText = strip_tags($advertisement->content ?? '');
        $wordCount = str_word_count($plainText);
        $contentSummary = substr($plainText, 0, 500);

        ContentForm::where('content_type', 'advertisement')
            ->where('content_id', $advertisement->id)
            ->update([
                'title' => $advertisement->title,
                'content_summary' => $contentSummary,
                'word_count' => $wordCount,
                'amount' => $advertisement->amount,
                'is_paid' => $advertisement->is_paid,
                'band' => $advertisement->band,
                'broadcast_start_date' => $advertisement->broadcast_start_date,
                'broadcast_end_date' => $advertisement->broadcast_end_date,
                'broadcast_days' => $advertisement->broadcast_days,
                'morning_frequency' => $advertisement->morning_frequency,
                'lunch_frequency' => $advertisement->lunch_frequency,
                'evening_frequency' => $advertisement->evening_frequency,
            ]);
    }

    /**
     * Handle the Advertisement "deleted" event.
     */
    public function deleted(Advertisement $advertisement): void
    {
        // Delete associated ContentForm when advertisement is deleted
        ContentForm::where('content_type', 'advertisement')
            ->where('content_id', $advertisement->id)
            ->delete();
    }

    /**
     * Handle the Advertisement "restored" event.
     */
    public function restored(Advertisement $advertisement): void
    {
        //
    }

    /**
     * Handle the Advertisement "force deleted" event.
     */
    public function forceDeleted(Advertisement $advertisement): void
    {
        // Force delete associated ContentForm when advertisement is force deleted
        ContentForm::where('content_type', 'advertisement')
            ->where('content_id', $advertisement->id)
            ->forceDelete();
    }
}
