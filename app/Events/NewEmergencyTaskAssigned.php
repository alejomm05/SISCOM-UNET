<?php



namespace App\Events;



use Illuminate\Broadcasting\Channel;

use Illuminate\Broadcasting\InteractsWithSockets;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use Illuminate\Foundation\Events\Dispatchable;

use Illuminate\Queue\SerializesModels;

use App\Models\Task;
use Illuminate\Broadcasting\PrivateChannel;



class NewEmergencyTaskAssigned implements ShouldBroadcast

{

    use Dispatchable, InteractsWithSockets, SerializesModels;



    public Task $task;



    /**

     * Crea una nueva instancia del evento.

     */

    public function __construct(Task $task)

    {

        $this->task = $task;

    }



    /**

     * El canal o canales a los que se debe transmitir el evento.

     * * Nota: Usamos un canal privado para el guardia específico al que se le asignó la tarea.

     */

    public function broadcastOn(): Channel

    {

        // Transmite al canal privado del usuario asignado

        return new PrivateChannel('user.' . $this->task->assigned_to);

    }

    

    /**

     * El nombre con el que se transmitirá el evento.

     */

    public function broadcastAs(): string

    {

        return 'task.assigned';

    }



    /**

     * Los datos a transmitir con el evento.

     */

    public function broadcastWith(): array

    {

        // Devolvemos la tarea completa para que Flutter la pueda añadir directamente.

        return [

            'task' => $this->task->toArray(),

        ];

    }

}

