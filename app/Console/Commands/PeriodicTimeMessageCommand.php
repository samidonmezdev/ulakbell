<?php

namespace App\Console\Commands;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Console\Command;

class PeriodicTimeMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:periodic-time-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Her 15 dakikada bir mevcut saati içeren bir mesaj oluşturur';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = User::where('name', 'Personel 2')->value('id');
        if (!$userId) {
            $userId = User::value('id');
        }
        if (!$userId) {
            $this->warn('Kullanıcı bulunamadı, mesaj oluşturulamadı.');
            return self::FAILURE;
        }

        $message = Message::create([
            'user_id' => null,
            'content' => [
                'type' => 'text',
                'text' => 'guk guk! saat ' . now()->format('H:i'),
            ],
        ]);

        $message->load('user');
        
        // Broadcast the message
        broadcast(new MessageSent($message));

        $this->info('Zamanlı mesaj oluşturuldu: ' . $message->content['text']);
        return self::SUCCESS;
    }
}
