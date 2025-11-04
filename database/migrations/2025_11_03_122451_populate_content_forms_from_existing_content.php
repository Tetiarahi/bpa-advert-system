<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Advertisement;
use App\Models\Gong;
use App\Models\ContentForm;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Populate ContentForms from existing Advertisements
        Advertisement::all()->each(function ($advertisement) {
            $plainText = strip_tags($advertisement->content ?? '');
            $wordCount = str_word_count($plainText);
            $contentSummary = substr($plainText, 0, 500);

            ContentForm::updateOrCreate(
                [
                    'content_type' => 'advertisement',
                    'content_id' => $advertisement->id,
                ],
                [
                    'customer_id' => $advertisement->customer_id,
                    'title' => $advertisement->title,
                    'content_summary' => $contentSummary,
                    'word_count' => $wordCount,
                    'source' => 'hand',
                    'received_date' => $advertisement->issued_date ?? now(),
                    'amount' => $advertisement->amount ?? 0,
                    'is_paid' => $advertisement->is_paid ?? false,
                    'band' => $advertisement->band ?? [],
                    'broadcast_start_date' => $advertisement->broadcast_start_date,
                    'broadcast_end_date' => $advertisement->broadcast_end_date,
                    'broadcast_days' => $advertisement->broadcast_days ?? [],
                    'morning_frequency' => $advertisement->morning_frequency ?? 0,
                    'lunch_frequency' => $advertisement->lunch_frequency ?? 0,
                    'evening_frequency' => $advertisement->evening_frequency ?? 0,
                ]
            );
        });

        // Populate ContentForms from existing Gongs
        Gong::all()->each(function ($gong) {
            $plainText = strip_tags($gong->contents ?? '');
            $wordCount = str_word_count($plainText);
            $contentSummary = substr($plainText, 0, 500);

            ContentForm::updateOrCreate(
                [
                    'content_type' => 'gong',
                    'content_id' => $gong->id,
                ],
                [
                    'customer_id' => $gong->customer_id,
                    'title' => $gong->departed_name,
                    'content_summary' => $contentSummary,
                    'word_count' => $wordCount,
                    'source' => 'hand',
                    'received_date' => $gong->published_date ?? now(),
                    'amount' => $gong->amount ?? 0,
                    'is_paid' => $gong->is_paid ?? false,
                    'band' => $gong->band ?? [],
                    'broadcast_start_date' => $gong->broadcast_start_date,
                    'broadcast_end_date' => $gong->broadcast_end_date,
                    'broadcast_days' => $gong->broadcast_days ?? [],
                    'morning_frequency' => $gong->morning_frequency ?? 0,
                    'lunch_frequency' => $gong->lunch_frequency ?? 0,
                    'evening_frequency' => $gong->evening_frequency ?? 0,
                ]
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete all ContentForms
        ContentForm::truncate();
    }
};
