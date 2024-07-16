@extends('adminApp')
@section('content')
<section class="mt-3">
    <div class="container">
        <div class="fs-1 fw-bold">Редактирование развлекательного центра</div>
        <form action="/editPlace/{{$place->id}}" class="mt-3" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label for="name" class="form-label fw-bold fs-5">Название</label>
                <input type="text" class="form-control fw-bold bg-warning border border-black" id="name" name="name" required value="{{$place->name}}">
                @error('name')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="address" class="form-label fw-bold fs-5">Адрес</label>
                <input type="text" class="form-control fw-bold bg-warning border border-black" id="address" name="address" required value="{{$place->address}}">
                @error('address')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label fw-bold fs-5">Описание</label>
                <textarea class="form-control bg-warning fw-bold border border-black" id="description" rows="10" name="description" required>{{$place->description}}</textarea>
                @error('description')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="image" class="form-label fw-bold fs-5">Изображение</label>
                <input type="file" class="form-control fw-bold bg-warning border border-black" id="editImgInput" name="image">
                <div id="editImgBlock" class="position-relative">
                    <img id="editImg" class="border border-warning rounded-2 mt-2 custom-edit-img" src="/storage/images/{{$place->image}}" alt="">
                </div>
                @error('image')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="mt-3 me-2 fs-5 btn btn-warning fw-bold custom-slider-button">Редактировать</button>
            <button type="button" data-bs-toggle="modal" data-bs-target="#deletePlaceModal" class="mt-3 fs-5 btn btn-outline-danger fw-bold">Удалить</button>
        </form>
    </div>
</section>
<section class="deletePlaceModal">
    <div class="modal fade" data-bs-backdrop="static" id="deletePlaceModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
            <div class="modal-content bg-warning">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-bold" id="deletePlaceModalLabel">Осторожно, удаление развлекательного центра!</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body custom-modal-body message-modal">
                    <p>При удалении развлекательного центра, потеряется вся информация связанная с местом!</p>
                    <p>Вы действительно хотите удалить место?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-outline-dark fw-bold">Отмена</button>
                    <a href="/deletePlace/{{$place->id}}" class="btn btn-outline-danger fw-bold">Удалить</a>
                </div>
            </div>
        </div>
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
                    <a class="custom-schedule-place fs-5" href="/editPlacePage/{{$place->id}}">{{$place->name}}</a>
                </div>
                <div class="custom-schedule-item4 position-absolute top-50 translate-middle-y">
                    <a class="custom-schedule-btn rounded-2" href="/editEventPage/{{$event->id}}">Подробнее</a>
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
<script>
    let editImgInput = document.getElementById('editImgInput');
    let editImg = document.getElementById('editImg');

    editImgInput.addEventListener('change', function() {
        const file = this.files[0];

        if (file) {
            const reader = new FileReader();

            reader.addEventListener('load', function() {
                editImg.src = this.result;
            });

            reader.readAsDataURL(file);
        }
    });
</script>
@endsection('content')