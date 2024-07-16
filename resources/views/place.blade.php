@extends('app')
@section('content')
<section class="mt-5">
    <div class="container">
        <div class="custom-performer-img-block position-relative">
            <img class="custom-performer-img rounded-2 border border-2 border-warning" src="/storage/images/{{ $place->image }}" alt="">
            <span class="fw-bold fs-2 ms-3">{{ $place->name }}</span>
            <a href="/favoritePlace/{{ $place->id }}" class="custom-performer-unfavorite-a rounded-2 ms-3 mt-3" id="unFavoriteA" id="place_{{$place->id}}">
                <img id="unFavoriteImg" alt="" src="/storage/images/{{ $place->isFavorite() ? 'favorite.svg' : 'unfavorite.svg' }}">
            </a>
        </div>
    </div>
</section>
<section>
    <div class="container">
        <div class="mt-5 fw-bold fs-1 mb-3">Об развлекательном центре</div>
        <p class="fs-5 w-100">{{ $place->description }}</p>
    </div>
</section>
<section>
    <div class="container">
        <div class="mt-5 fw-bold fs-1 mb-3">Схема</div>
        <div id="scheme" class="mt-3 rounded-2 custom-scheme-block p-5 border border-2 border-warning">
            <div class="custom-spinner-block">
                <div class="spinner-border text-warning" role="status"></div>
            </div>
        </div>
    </div>
    <script>
        var scheme = {
                "seatDirection": "ltr",
                "rows": [
                    [{
                        "seats": 16,
                        "offset": 2
                    }],

                    [{
                        "seats": 18,
                        "offset": 1
                    }],

                    [{
                        "seats": 20
                    }],

                    [{
                        "seats": 20
                    }],

                    [{
                        "seats": 20
                    }],

                    [{

                    }],

                    [{
                        "seats": 4,
                        "offset": 1
                    }, {
                        "seats": 8,
                        "offset": 1,
                    }, {
                        "seats": 4,
                        "offset": 1,
                    }],

                    [{
                        "seats": 3,
                        "offset": 2
                    }, {
                        "seats": 8,
                        "offset": 1,
                    }, {
                        "seats": 3,
                        "offset": 1,
                    }],

                    [{
                        "seats": 2,
                        "offset": 3
                    }, {
                        "seats": 8,
                        "offset": 1,
                    }, {
                        "seats": 2,
                        "offset": 1,
                    }],

                    [{
                        "seats": 1,
                        "offset": 4
                    }, {
                        "seats": 8,
                        "offset": 1,
                    }, {
                        "seats": 1,
                        "offset": 1,
                    }]
                ]
            };

            var plan = new HallPlan({
                el: '#scheme',
                scheme: scheme,
                rowNumbersLeft: true,
                rowNumbersRight: true,
                screenText: '{{$screenText}}',
            });

            plan.render();
    </script>
</section>
<section>
    <div class="container">
        <div class="mt-5 fw-bold fs-1 mb-3">Расписание выступлений</div>
        @if($place->events->isEmpty())
        <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
        @else
        <div class="custom-schedules-block w-75">
            @foreach($place->events as $event) <div class="custom-schedule-block fw-bold position-relative"
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
                    <a class="custom-schedule-place fs-5" href="/place/{{$place->id}}">{{$place->name}}</a>
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
<section class="mt-5">
    <script>
        let request = "{{ $place->address }}";
        var url = "https://geocode-maps.yandex.ru/1.x/?apikey=db574cb8-240b-4c27-a963-969c39774077&geocode=" + request + "&format=json";
        fetch(url)
            .then(response => response.json())
            .then(data => {
                var coordinates = data.response.GeoObjectCollection.featureMember[0].GeoObject.Point.pos.split(" ");
                var latitude = coordinates[1];
                var longitude = coordinates[0];

                initMap(latitude, longitude);
            })
            .catch(error => {
                console.error("Error fetching or parsing data:", error);
            });

        async function initMap(latitude, longitude) {
            await ymaps3.ready;

            const {
                YMap,
                YMapDefaultSchemeLayer,
                YMapDefaultFeaturesLayer,
            } = ymaps3;

            const {
                YMapDefaultMarker
            } = await ymaps3.import('@yandex/ymaps3-markers@0.0.1');

            const map = new YMap(
                document.getElementById('map'), {
                    location: {
                        center: [longitude, latitude],
                        zoom: 15
                    },
                    showScaleInCopyrights: true
                }, [
                    new YMapDefaultSchemeLayer({}),
                    new YMapDefaultFeaturesLayer({})
                ]
            );

            const marker = new YMapDefaultMarker({
                coordinates: [longitude, latitude],
                draggable: true,
                popup: {
                    content: '{{ $place->address }}',
                    position: 'right'
                },
                mapFollowsOnDrag: true
            });
            map.addChild(marker);
        }
    </script>
    <div class="container">
        <div class="mt-5 fw-bold fs-1 mb-3">Адрес</div>
        <div id="map" class="rounded-2 border border-2 border-warning custom-yandex-map position-relative">
            <div class="custom-spinner-block">
                <div class="spinner-border text-warning" role="status"></div>
            </div>
        </div>
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