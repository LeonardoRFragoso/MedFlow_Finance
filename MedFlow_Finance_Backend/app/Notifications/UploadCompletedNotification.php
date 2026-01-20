<?php

namespace App\Notifications;

use App\Models\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UploadCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $upload;

    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'upload_completed',
            'upload_id' => $this->upload->id,
            'filename' => $this->upload->filename,
            'total_rows' => $this->upload->total_rows,
            'valid_rows' => $this->upload->valid_rows,
            'error_rows' => $this->upload->error_rows,
            'success_rate' => $this->upload->getSuccessRate(),
            'message' => "Upload '{$this->upload->filename}' processado com sucesso!",
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'upload_completed',
            'upload_id' => $this->upload->id,
            'filename' => $this->upload->filename,
            'total_rows' => $this->upload->total_rows,
            'valid_rows' => $this->upload->valid_rows,
            'error_rows' => $this->upload->error_rows,
            'success_rate' => $this->upload->getSuccessRate(),
            'message' => "Upload '{$this->upload->filename}' processado com sucesso!",
        ]);
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Upload Processado - MedFlow Finance')
            ->line("Seu upload '{$this->upload->filename}' foi processado com sucesso!")
            ->line("Total de registros: {$this->upload->total_rows}")
            ->line("Registros válidos: {$this->upload->valid_rows}")
            ->line("Registros com erro: {$this->upload->error_rows}")
            ->action('Ver Detalhes', url("/uploads/{$this->upload->id}"))
            ->line('Obrigado por usar o MedFlow Finance!');
    }
}
