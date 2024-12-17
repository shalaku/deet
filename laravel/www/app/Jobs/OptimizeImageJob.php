<?php

namespace App\Jobs;

use App\Services\ImageOptimizationService; // Ensure this use statement matches your actual namespace
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OptimizeImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;

    /**
     * Create a new job instance.
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     */
    public function handle(ImageOptimizationService $imageOptimization): void
    {
        $imageOptimization->optimizeImageById($this->id);
    }
}
