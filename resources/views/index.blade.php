@extends('app')
@section('content')
<section class="mt-3">
    <div class="container">
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="2" aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="3" aria-label="Slide 4"></button>
                <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="4" aria-label="Slide 5"></button>
            </div>
            <div class="carousel-inner rounded-2 border border-2 border-warning">
                <div class="carousel-item active">
                    <img src="/storage/images/{{$firstEvent->image}}" class="d-block w-100 custom-card-img" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                        <h5 class="fw-bold text-warning">{{$firstEvent->name}}</h5>
                        <p class="text-warning">{{$firstEvent->subgenre->genre->name}} • {{$firstEvent->subgenre->name}} • {{$firstEvent->ageLimit->name}}</p>
                        <a class="btn btn-warning fw-bold custom-slider-button" href="/event/{{ $firstEvent->id }}">Подробнее</a>
                    </div>
                </div>
                @foreach($newEvents as $event)
                <div class="carousel-item">
                    <img src="/storage/images/{{ $event->image }}" class="d-block w-100 custom-card-img" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                        <h5 class="fw-bold text-warning">{{$event->name}}</h5>
                        <p class="text-warning">{{$event->subgenre->genre->name}} • {{$event->subgenre->name}} • {{$event->ageLimit->name}}</p>
                        <a class="btn btn-warning fw-bold custom-slider-button" href="/event/{{ $event->id }}">Подробнее</a>
                    </div>
                </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</section>
<section class="mt-5">
    <div class="container">
        <div class="custom-event-filt">
            <div class="fs-1 fw-bold">События</div>
            <form action="/filtIndexEvents" method="POST" id="filtIndexEvents">
                @csrf
                <select class="form-select bg-warning fw-bold custom-event-filt-select" aria-label="Default select example" name="filter">
                    <option value="0" {{ $selectedFilter == 0 ? 'selected' : '' }}>Сначала популярные</option>
                    <option value="1" {{ $selectedFilter == 1 ? 'selected' : '' }}>Сначала новые</option>
                    <option value="2" {{ $selectedFilter == 2 ? 'selected' : '' }}>Сначала старые</option>
                    <option value="3" {{ $selectedFilter == 3 ? 'selected' : '' }}>По рейтингу</option>
                </select>
                <select class="form-select bg-warning fw-bold custom-event-filt-select2" aria-label="Default select example" name="genre_id">
                    <option value="0" {{ $selectedGenre == 0 ? 'selected' : '' }}>Все</option>
                    @foreach($genres as $genre)
                    <option value="{{$genre->id}}" {{ $genre->id == $selectedGenre ? 'selected' : ''}}>{{$genre->name}}</option>
                    @endforeach
                    <option value="6" {{ $selectedGenre == 6 ? 'selected' : '' }}>Завершённые</option>
                </select>
                <button type="submit" class="btn btn-warning fw-bold custom-slider-button">Применить</button>
            </form>
        </div>
        <div class="row row-cols-lg-3">
            @if($events->isEmpty())
            <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
            @endif
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
        <!-- <a class="fw-bold custom-more-button" href="#">Показать ещё</a> -->
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
            @foreach($watched as $watch) <a href="/event/{{$watch->event->id}}" class="card me-3 mt-3 custom-card-link-watched position-relative" style="width: 18rem;">
                <img src="/storage/images/{{$watch->event->image}}" class="card-img-top" style="height: 10rem;" alt="...">
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