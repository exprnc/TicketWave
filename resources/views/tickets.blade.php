@extends('acc')
@section('acc-content')
<section>
    <div class="container">
        <div class="fs-2 fw-bold mt-1">Мои билеты</div>
        @if($tickets->isEmpty())
        <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
        @endif
        @foreach ($tickets as $date => $groupedTickets)
        <div class="fs-3 fw-bold mt-5 custom-group-ticket-title">Куплено {{ $groupedTickets->first()->event->formatDate3($date) }}</div>
        <div class="custom-row">
            @foreach($groupedTickets as $ticket) <div class="custom-ticket-block p-3"
                @if($ticket->completed == 1)
                style="opacity: 0.75 !important"
                @endif
            >
                <a href="/event/{{$ticket->event->id}}" class="fw-bold fs-5 custom-ticket-title">{{$ticket->event->name}}</a>
                <div class="custom-ticket-img">
                    <img src="/storage/images/{{$ticket->event->image}}" alt="">
                </div>
                <div class="custom-card-body-item mt-2">
                    <img src="storage/images/clock.svg" alt="">
                    <span class="fw-bold fs-6">{{ $ticket->isHaveSession() ? $ticket->session()->formattedDate() : $ticket->event->formatDate1($ticket->event->date) }} • {{ $ticket->isHaveSession() ? $ticket->session()->formattedTime() : $ticket->event->formatDate2($ticket->event->time) }}</span>
                </div>
                <div class="custom-card-body-item">
                    <img src="storage/images/location.svg" alt="">
                    <a class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover fw-bold" href="/place/{{$ticket->event->place->id}}">{{$ticket->event->place->name}}</a>
                </div>
                <div class="fw-bold">Зона: {{$ticket->zone}}</div>
                <div class="fw-bold">Ряд: {{$ticket->row}}</div>
                <div class="fw-bold">Место: {{$ticket->seat}}</div>
                <div class="fw-bold">Цена: {{$ticket->price}} ₽</div>
                @if($ticket->completed == 0)
                <button type="button" class="btn btn-outline-dark fw-bold custom-ticket-return" data-bs-toggle="modal" data-bs-target="#ticketReturnModal" onclick="updateReturnLink('{{$ticket->id}}')">Возврат</button>
                @else
                <div class="fw-bold fs-5 custom-ticket-return">Билет устарел</div>
                @endif
                <div class="custom-ticket-header"></div>
                <div class="custom-ticket-footer"></div>
                <div class="custom-ticket-hole-block">
                    <div class="custom-ticket-hole1"></div>
                    <div class="custom-ticket-hole"></div>
                    <div class="custom-ticket-hole"></div>
                    <div class="custom-ticket-hole"></div>
                    <div class="custom-ticket-hole"></div>
                    <div class="custom-ticket-hole2"></div>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</section>
<section class="ticketReturnModal">
    <div class="modal fade" data-bs-backdrop="static" id="ticketReturnModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
            <div class="modal-content bg-warning">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-bold" id="ticketReturnModalLabel">Возврат билета</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body custom-modal-body message-modal">
                    <p>Вы действительно хотите вернуть билет?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-outline-dark fw-bold">Отмена</button>
                    <a href="#" id="ticketReturnBtn" class="btn btn-outline-danger fw-bold">Возврат</a>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function updateReturnLink(ticketId) {
        var ticket = ticketId;
        var url = "/ticketReturn?ticket=" + encodeURIComponent(ticket);
        document.getElementById("ticketReturnBtn").setAttribute("href", url);
    }
</script>
@endsection('acc-content')