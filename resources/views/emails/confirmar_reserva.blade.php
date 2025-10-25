@extends('emails.layout')

@section('title')
    ¡Hola {{ $nombre }}!
@endsection

@section('content')
    <p>Tu reserva ha sido confirmada exitosamente.</p>

    <p style="margin: 20px 0;">
        <strong>Reserva N°:</strong> {{ $id_reserva }}<br>
        <strong>Estado:</strong> {{ $estado }}<br>
        <strong>Habitación:</strong> {{ $habitacion }} ({{ $tipo_habitacion }})<br>
        <strong>Check-in:</strong> {{ $fecha_checkin }}<br>
        <strong>Check-out:</strong> {{ $fecha_checkout }}<br>
        <strong>Total pagado:</strong> ${{ number_format($total, 2) }}
    </p>

    <p>Gracias por confiar en <strong>Aurora Hotel</strong>. Esperamos que disfrutes tu estadía.</p>
@endsection

@section('footer')
    Este correo fue generado automáticamente. Por favor, no respondas a este mensaje.<br>
    <small>Si tienes alguna duda, contáctanos a través de nuestros canales oficiales.</small>
@endsection