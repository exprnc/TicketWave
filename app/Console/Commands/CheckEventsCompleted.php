<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Session;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckEventsCompleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:check-completed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверяет и отмечает события как завершенные, если текущее время равно или больше даты и времени события.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $events = Event::where('completed', 0)->get();

        foreach ($events as $event) {
            $sessions = Session::where('event_id', $event->id)->orderBy('date', 'desc')->orderBy('time', 'desc')->get();
            if ($sessions->isNotEmpty()) {
                $session = $sessions->first();
                $eventDateTime = Carbon::parse($session->date . ' ' . $session->time);
                if (Carbon::now() >= $eventDateTime) {
                    $event->update(['completed' => 1]);
                    $tickets = Ticket::where('event_id', $event->id)->update(['completed' => 1]);
                    $this->info('Event ' . $event->id . ' completed.');
                }
            }else{
                $eventDateTime = Carbon::parse($event->date . ' ' . $event->time);
                if (Carbon::now() >= $eventDateTime) {
                    $event->update(['completed' => 1]);
                    $tickets = Ticket::where('event_id', $event->id)->update(['completed' => 1]);
                    $this->info('Event ' . $event->id . ' completed.');
                }
            }
        }

        $this->info('Events checked and completed.');
        
        return 0;
    }
}
