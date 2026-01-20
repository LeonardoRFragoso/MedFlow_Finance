<?php

namespace App\Events;

use App\Models\Record;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecordValidated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $record;
    public $validationResults;

    public function __construct(Record $record, array $validationResults)
    {
        $this->record = $record;
        $this->validationResults = $validationResults;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('clinic.' . $this->record->clinic_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'record.validated';
    }

    public function broadcastWith(): array
    {
        return [
            'record_id' => $this->record->id,
            'upload_id' => $this->record->upload_id,
            'status' => $this->record->status,
            'is_valid' => $this->validationResults['is_valid'] ?? false,
            'error_count' => count($this->validationResults['errors'] ?? []),
            'validated_at' => now()->toISOString(),
        ];
    }
}
