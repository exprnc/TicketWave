@extends('acc')
@section('acc-content')
<section>
    <div class="container">
        <div class="fs-2 fw-bold mt-1">Избранные события</div>
        @if($events->isEmpty())
        <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
        @endif
        <div class="row row-cols-lg-3">
            @foreach($events as $event) 
            <div class="col mt-3">
                <div class="card text-bg-warning position-relative" style="width: 26rem;" id="event_{{$event->id}}">
                    <img src="/storage/images/{{ $event->image }}" class="card-img-top" style="height: 14rem;" alt="...">
                    @if($event->completed == 1)
                    <img src="/storage/images/completed.png" class="card-img-top position-absolute top-0 start-0" style="height: 14rem;" alt="...">
                    @endif
                    <div class="custom-card-rating2 position-absolute top-0 end-0 rounded-2 fw-bold fs-6">{{$event->averageRating}}</div>
                    <div class="custom-card-genre-info position-absolute top-0 start-0">
                        <p class="card-text text-warning">{{$event->subgenre->genre->name}} • {{$event->subgenre->name}} • {{$event->ageLimit->name}}</p>
                    </div>
                    <div class="card-body">
                        <div class="custom-card-header">
                            <h5 class="card-title">{{ $event->name }}</h5>
                            <a href="/favoriteEvent/{{$event->id}}"><img alt="" src="/storage/images/{{ $event->isFavorite() ? 'favorite.svg' : 'unfavorite.svg' }}"></a>
                        </div>
                        <div class="custom-card-body">
                            <p class="card-text mb-1 custom-card-p">{{ $event->description }}</p>
                            <div class="custom-card-body-item">
                                <img src="/storage/images/clock.svg" alt="">
                                <span class="fw-bold fs-6">{{ $event->formatDate1($event->date) }} • {{ $event->formatDate2($event->time) }}</span>
                            </div>
                            <div class="custom-card-body-item">
                                <img src="/storage/images/location.svg" alt="">
                                <a class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover fw-bold" href="/place/{{$event->place->id}}">{{ $event->place->name }}</a>
                            </div>
                        </div>
                        <div class="custom-card-footer">
                            <div class="fs-5 fw-bold">от {{ $event->price }} ₽</div>
                            <a href="/event/{{ $event->id }}" class="btn btn-outline-dark fw-bold">Подробнее</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection('acc-content')