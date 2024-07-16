<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Wave</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            width: fit-content;
            height: fit-content;
        }

        .ticket {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
        }

        .ticket-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .ticket-details {
            margin-bottom: 15px;
        }

        .ticket-details div {
            margin-bottom: 5px;
        }

        .ticket-price {
            font-size: 18px;
            font-weight: bold;
        }

        .return-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }

        .return-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    @foreach($tickets as $ticket)
    <div class="ticket">
        <div class="ticket-title">Электронный билет №{{$ticket->id}}</div>
        <div class="ticket-details">
            <div><strong>Событие:</strong> {{$ticket->event->name}}</div>
            <div><strong>Дата и время:</strong>{{ $ticket->isHaveSession() ? $ticket->session()->formattedDate() : $ticket->event->formatDate1($ticket->event->date) }} • {{ $ticket->isHaveSession() ? $ticket->session()->formattedTime() : $ticket->event->formatDate2($ticket->event->time) }}</div>
            <div><strong>Развлекательный центр:</strong> {{$ticket->event->place->name}} • {{$ticket->event->place->address}}</div>
            <div><strong>Зона:</strong> {{$ticket->zone}}, <strong>Ряд:</strong> {{$ticket->row}}, <strong>Место:</strong> {{$ticket->seat}}</div>
            <div class="ticket-price"><strong>Цена:</strong> {{$ticket->price}} ₽</div>
        </div>
    </div>
    @endforeach
</body>

</html>

</html>
