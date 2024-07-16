@extends('acc')
@section('acc-content')
<section>
    <div class="container">
        <div class="fs-2 fw-bold mt-1">Вы смотрели</div>
        @if($watched2->isEmpty())
        <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
        @endif
        <div class="custom-row">
            @foreach($watched2 as $watch) <a href="/event/{{$watch->event->id}}" class="card me-3 mt-3 custom-card-link-watched position-relative" style="width: 18rem;">
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
@endsection('acc-content')