<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConfirmarReservaMail extends Mailable
{
    use SerializesModels;

    public $reserva;

    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva->load(['huesped', 'detalleReservas.habitacion.tipoHabitacion']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de tu reserva #' . $this->reserva->id_reserva,
            from: new \Illuminate\Mail\Mailables\Address(
                config('mail.from.address'),
                config('mail.from.name') ?? config('app.name')
            )
        );
    }

    public function content(): Content
    {
        $detalle = $this->reserva->detalleReservas->first();

        return new Content(
            view: 'emails.confirmar_reserva',
            with: [
                'nombre'          => $this->reserva->huesped->nombre . ' ' . ($this->reserva->huesped->apellido_paterno ?? ''),
                'fecha_checkin'   => $this->reserva->fecha_checkin->format('d/m/Y'),
                'fecha_checkout'  => $this->reserva->fecha_checkout->format('d/m/Y'),
                'habitacion'      => $detalle->habitacion->numero_habitacion ?? 'Sin especificar',
                'tipo_habitacion' => $detalle->habitacion->tipoHabitacion->nombre ?? 'Sin tipo',
                'total'           => $this->reserva->total,
                'estado'          => ucfirst($this->reserva->estado),
                'id_reserva'      => $this->reserva->id_reserva,
                'logoPath'        => public_path('images/aurorafull.png')    
            ]
        );
    }
}
