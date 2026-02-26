<?php

namespace App\Events;

use App\Models\Pesan\Pesan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Pesan\Pesan  $message
     * @return void
     */
    public function __construct(Pesan $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('messages.' . $this->message->penerima_id),
            new PrivateChannel('messages.' . $this->message->pengirim_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'isi' => $this->message->isi,
            'tgl_pesan' => $this->message->tgl_pesan,
            'created_at' => $this->message->created_at,
            'pengirim' => [
                'id' => $this->message->pengirim->id,
                'nama_lengkap' => $this->message->pengirim->nama_lengkap,
                'foto' => $this->message->pengirim->foto,
            ],
            'penerima' => [
                'id' => $this->message->penerima->id,
                'nama_lengkap' => $this->message->penerima->nama_lengkap,
                'foto' => $this->message->penerima->foto,
            ],
            'jenis' => [
                'id' => $this->message->jenis->id,
                'nama' => $this->message->jenis->nama,
            ],
        ];
    }
}
