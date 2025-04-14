<?php

namespace App\Jobs;

use App\Models\Courier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class IncreaseViewsJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $courierId;

    public function __construct($courierId) {
        $this->courierId = $courierId;
    }

    public function handle() {
        DB::transaction(function () {
            Courier::where('id', $this->courierId)->increment('views');
        });
    }
}
