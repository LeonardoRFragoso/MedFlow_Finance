<?php

namespace App\Events;

use App\Models\Upload;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $upload;

    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('clinic.' . $this->upload->clinic_id),
            new PrivateChannel('user.' . $this->upload->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'upload.processed';
    }

    public function broadcastWith(): array
    {
        return [
            'upload_id' => $this->upload->id,
            'filename' => $this->upload->filename,
            'status' => $this->upload->status,
            'total_rows' => $this->upload->total_rows,
            'valid_rows' => $this->upload->valid_rows,
            'error_rows' => $this->upload->error_rows,
            'processed_at' => now()->toISOString(),
        ];
    }
}
