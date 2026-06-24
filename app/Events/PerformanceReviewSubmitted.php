<?php

namespace App\Events;

use App\Models\PerformanceReview;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PerformanceReviewSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PerformanceReview $review;

    public function __construct(PerformanceReview $review)
    {
        $this->review = $review;
    }
}