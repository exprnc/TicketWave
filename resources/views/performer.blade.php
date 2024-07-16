@extends('app')
@section('content')
<section class="mt-5">
    <div class="container">
        <div class="custom-performer-img-block position-relative">
            <img class="custom-performer-img rounded-2 border border-2 border-warning" src="/storage/images/{{ $performer->image }}" alt="">
            <span class="fw-bold fs-2 ms-3">{{ $performer->name }}</span>
            <a href="/favoritePerformer/{{ $performer->id }}" class="custom-performer-unfavorite-a rounded-2 ms-3 mt-3" id="performer_{{$performer->id}}">
                <img alt="" src="/storage/images/{{ $performer->isFavorite() ? 'favorite.svg' : 'unfavorite.svg' }}">
            </a>
        </div>
    </div>
</section>
<section>
    <div class="container">
        <div class="mt-5 fw-bold fs-1 mb-3">Об исполнителе</div>
        <p class="fs-5 w-100">{{ $performer->description }}</p>
    </div>
</section>
<section>
    <div class="container">
        <div class="mt-5 fw-bold fs-1 mb-3">Расписание выступлений</div>
        @if($performer->events->isEmpty())
        <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
        @else
        <div class="custom-schedules-block w-75">
            @foreach($performer->events as $event)
            <div class="custom-schedule-block fw-bold position-relative"
                @if($event->completed == 1)
                style="opacity: 0.5;"
                @endif
            >
                <div class="custom-schedule-item1 position-absolute top-50 translate-middle-y">
                    <div>{{$event->subgenre->genre->name}} • {{$event->subgenre->name}} • {{$event->ageLimit->name}}</div>
                    <div>{{$event->name}}</div>
                </div>
                <div class="custom-schedule-item2 position-absolute top-50 translate-middle-y">
                    <div class="fs-3">{{ $event->formatDate2($event->time) }}</div>
                    <div>{{ $event->formatDate1($event->date) }}</div>
                </div>
                <div class="custom-schedule-item3 position-absolute top-50 translate-middle-y">
                    <a class="custom-schedule-place fs-5" href="/place/{{$event->place->id}}">{{$event->place->name}}</a>
                </div>
                <div class="custom-schedule-item4 position-absolute top-50 translate-middle-y">
                    @if($event->completed == 1)
                    <div class="fw-bold">Событие прошло</div>
                    @else
                    <a class="custom-schedule-btn rounded-2" href="/event/{{$event->id}}">от {{$event->price}} ₽</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
@if($auth)
<section>
    <div class="container">
        <div class="mt-5 fw-bold fs-1 mb-3">Вы смотрели</div>
        @if($watched->isEmpty())
        <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
        @endif
        <div class="custom-row">
            @foreach($watched as $watch) <a href="/event/{{$watch->event->id}}" class="card me-3 mt-3 custom-card-link-watched" style="width: 18rem;">
                <img src="/storage/images/{{$watch->event->image}}" class="card-img-top position-relative" style="height: 10rem;" alt="...">
                @if($watch->event->completed == 1)
                <img src="/storage/images/completed.png" class="card-img-top position-absolute top-0 start-0" style="height: 10rem;" alt="...">
                @endif
                <div class="card-img-overlay">
                    <p class="card-text text-warning">{{$watch->event->subgenre->genre->name}} • {{$watch->event->subgenre->name}} • {{$watch->event->ageLimit->name}}</p>
                </div>
                <div class="card-body">
                    <p class="card-text fw-bold fs-5">{{$watch->event->name}}</p>
                </div>
                </a>
                @endforeach
        </div>
    </div>
</section>
@endif
@endsection('content')