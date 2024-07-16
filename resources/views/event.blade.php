@extends('app')
@section('content')
</section>
@if($hasComment)
<section class="editComment">
    <div class="modal fade" data-bs-backdrop="static" id="editComment" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
            <div class="modal-content bg-warning">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-bold" id="exampleModalLabel">Редактирование отзыва</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <form action="/editComment" id="editCommentForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-5">
                            <textarea class="form-control bg-warning fw-bold border border-2 border-dark fs-5" id="editCommentText" rows="5" name="comment"></textarea>
                            @error('comment')
                            <div class="form-text">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <div class="position-relative custom-range-block2">
                                <div class="range-value2 fs-4 mb-3 fw-bold text-center w-100" id="rangeValue2">6</div>
                                <img src="/storage/images/star2.svg" alt="">
                                <input type="range" class="form-range custom-range-style" min="1" max="10" step="1" id="customRange4" value="1" name="rating">
                                <input type="hidden" name="commentId" value="{{ $userComment->id }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-dark fw-bold custom-slider-button">Редактировать</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endif
<section class="mt-3">
    <div class="container">
        <div class="card rounded-2 border border-2 border-warning position-relative">
            <img src="/storage/images/{{ $event->image }}" class="card-img custom-card-img" alt="...">
            @if($event->completed == 1)
            <img src="/storage/images/completed.png" class="card-img custom-card-img position-absolute top-0 start-0" alt="...">
            @endif
            <div class="position-absolute bottom-0 start-0 w-100 p-5">
                <div class="card-text fw-bold text-warning fs-5">{{$event->subgenre->genre->name}} • {{$event->subgenre->name}} • {{$event->ageLimit->name}}</div>
                <div class="card-text fs-1 mt-1 fw-bold text-warning">{{$event->name}}</div>
                <a class="card-text fs-5 fw-bold text-warning link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" href="/place/{{ $event->place->id }}">{{$event->place->name}}</a>
                <div class="custom-buy-favorite-block mt-4">
                    <a class="btn btn-warning fw-bold custom-slider-button custom-ticket-buy-btn" href="#scheme">Купить билеты</a>
                    <a class="custom-unfavorite-a rounded-2 ms-2" href="/favoriteEvent/{{ $event->id }}" id="event_{{$event->id}}">
                        <img alt="" src="/storage/images/{{ $event->isFavorite() ? 'favorite.svg' : 'unfavorite.svg' }}">
                    </a>
                </div>
            </div>
            <div class="custom-card-rating position-absolute top-0 end-0 mt-5 me-5 rounded-2 fw-bold fs-5">{{$event->averageRating}}</div>
        </div>
    </div>
</section>
<section>
    <style>
        .hall-plan__seat--state_reserved {
            background-color: #c3c3c3;
            border-color: #c3c3c3;
            border-radius: 50%;
        }

        .hall-plan--clickable .hall-plan__seat--state_reserved:hover {
            background-color: #dedede;
        }

        .hall-plan--selectable .hall-plan__seat--state_reserved.hall-plan__seat--active {
            background-color: #dedede;
            border-color: #b0b0b0;
        }

        .hall-plan__seat--state_blue {
            background-color: #6990e9;
            border-color: #6990e9;
        }

        .hall-plan__seat--state_reserved {
            background-color: #c3c3c3;
            border-color: #c3c3c3;
            pointer-events: none;
        }

        .hall-plan--clickable .hall-plan__seat--state_blue:hover {
            background-color: #8facff;
        }
    </style>
    @if($isHaveSessions)
        <div class="container">
            @if($event->completed == 1)
            <div class="fs-4 text-center fw-bold w-100 mt-5">Событие завершено</div>
            <div class="custom-event-sessions-date-block mt-3">
                <form action="/filtUserSessions/{{$event->id}}" method="POST" id="filtUserSessions">
                    @csrf
                    <select class="form-select bg-warning fw-bold" aria-label="Default select example" id="date" name="date">
                        @foreach ($dates as $date)
                        <option value="{{ $date }}" {{$date == $secondSessions->first()->formattedDate() ? 'selected' : ''}}>{{ $date }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-warning fw-bold custom-slider-button">Применить</button>
                </form>
            </div>
            @else
            <div class="custom-event-sessions-date-block mt-5">
                <form action="/filtUserSessions/{{$event->id}}" method="POST" id="filtUserSessions">
                    @csrf
                    <select class="form-select bg-warning fw-bold" aria-label="Default select example" id="date" name="date">
                        @foreach ($dates as $date)
                        <option value="{{ $date }}" {{$date == $secondSessions->first()->formattedDate() ? 'selected' : ''}}>{{ $date }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-warning fw-bold custom-slider-button">Применить</button>
                </form>
            </div>
            @endif
            <div class="accordion mt-3" id="scheme">
                @foreach ($secondSessions as $session)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$session->id}}" aria-expanded="false" aria-controls="collapse{{$session->id}}">
                            {{$session->formattedTime()}}
                        </button>
                    </h2>
                    <div id="collapse{{$session->id}}" class="accordion-collapse collapse" data-bs-parent="#scheme">
                        <div class="accordion-body">
                            <div id="scheme_{{$session->id}}" class="mt-3 rounded-2 custom-scheme-block p-5 border border-2 border-warning">
                                <div class="custom-spinner-block">
                                    <div class="spinner-border text-warning" role="status"></div>
                                </div>
                            </div>
                            <script>
                                var scheme_{{$session->id}} = {
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

                                    ],
                                    "seatStates": {
                                        "reserved": {{$session->jsReservedSeats()}}
                                    }
                                };

                                var selectedSeats_{{$session->id}} = [];
                                var selectedSeatsInfo_{{$session->id}} = [];

                                var plan_{{$session->id}} = new HallPlan({
                                    el: '#scheme_{{$session->id}}',
                                    scheme: scheme_{{$session->id}},
                                    rowNumbersLeft: true,
                                    rowNumbersRight: true,
                                    screenText: '{{ $screenText }}',
                                    @auth
                                    @if($event->completed == 0)
                                    onSeatClick: function(seatData, mouseEvent) {
                                        onSeatClick_{{$session->id}}(seatData, mouseEvent);
                                    },
                                    @endif
                                    @endauth
                                });

                                plan_{{$session->id}}.render();

                                function onSeatClick_{{$session->id}}(seatData, mouseEvent) {
                                    var row = seatData.row;
                                    var seat = seatData.seat;
                                    var seatIndex = selectedSeats_{{$session->id}}.findIndex(item => item.row === row && item.seat === seat);
                                    var zone = getZone_{{$session->id}}(row, seat);
                                    var price = getPrice_{{$session->id}}(row, seat);

                                    if (seatIndex === -1) {
                                        if (selectedSeats_{{$session->id}}.length < 6) {
                                            selectedSeats_{{$session->id}}.push({
                                                zone: getZone_{{$session->id}}(row, seat),
                                                row: row,
                                                seat: seat,
                                                price: getPrice_{{$session->id}}(row, seat)
                                            });
                                            plan_{{$session->id}}.toggleSeatState(row, seat, 'blue');
                                            console.log(getPrice_{{$session->id}}(row, seat));
                                            console.log(getZone_{{$session->id}}(row, seat));

                                            var ticketBlock = createTicketBlock_{{$session->id}}(zone, row, seat, price);
                                            document.getElementById('custom-scheme-row_{{$session->id}}').appendChild(ticketBlock);
                                        } else {
                                            showMessageModal_{{$session->id}}('Ошибка', 'За один заказ можно купить только 6 билетов.');
                                            console.log('За один заказ можно купить только 6 билетов.');
                                        }
                                    } else {
                                        selectedSeats_{{$session->id}}.splice(seatIndex, 1);
                                        plan_{{$session->id}}.unsetSeatState(row, seat, 'blue');
                                        console.log('Место отменено');

                                        var ticketToRemove = document.getElementById('custom-scheme-row_{{$session->id}}').querySelector(`.col:nth-child(${seatIndex + 1})`);
                                        ticketToRemove.remove();
                                    }

                                    var totalPrice = calculateTotalPrice_{{$session->id}}();
                                    document.getElementById('custom-scheme-footer_{{$session->id}}').querySelector('.fs-4').textContent = 'Сумма: ' + totalPrice + ' ₽';

                                    console.log("Выбранные места:", selectedSeats_{{$session->id}});
                                    updateBuyLink_{{$session->id}}(selectedSeats_{{$session->id}});
                                }

                                function showMessageModal_{{$session->id}}(title, message) {
                                    document.getElementById('messageModalLabel').innerText = title;
                                    document.querySelector('.message-modal p').innerText = message;
                                    let modal = new bootstrap.Modal(document.getElementById('messageModal'));
                                    modal.show();
                                }

                                function createTicketBlock_{{$session->id}}(zone, row, seat, price) {
                                    var ticketBlock = document.createElement('div');
                                    ticketBlock.className = 'col';

                                    var ticketContent = `
                                    <div class="custom-scheme-footer-ticket">
                                        <div class="custom-scheme-footer-ticket-text fw-bold">Зона: ${zone}</div>
                                        <div class="custom-scheme-footer-ticket-text fw-bold">Ряд: ${row}</div>
                                        <div class="custom-scheme-footer-ticket-text fw-bold">Место: ${seat}</div>
                                        <div class="fw-bold mt-1 fs-5">${price} ₽</div>
                                    </div>`;

                                    ticketBlock.innerHTML = ticketContent;

                                    return ticketBlock;
                                }

                                function calculateTotalPrice_{{$session->id}}() {
                                    var total = 0;
                                    selectedSeats_{{$session->id}}.forEach(function(seat) {
                                        var price = getPrice_{{$session->id}}(seat.row, seat.seat);
                                        total += price;
                                    });
                                    return total;
                                }

                                function getPrice_{{$session->id}}(row, seat) {
                                    var price = {{$event->price}};
                                    if (row <= 5) {
                                        return price;
                                    } else if (row == 7 && seat >= 5 && seat <= 12) {
                                        return price + 250;
                                    } else if (row == 8 && seat >= 4 && seat <= 11) {
                                        return price + 250;
                                    } else if (row == 9 && seat >= 3 && seat <= 10) {
                                        return price + 250;
                                    } else if (row == 10 && seat >= 2 && seat <= 9) {
                                        return price + 250;
                                    } else {
                                        return price + 500;
                                    }
                                }

                                function getZone_{{$session->id}}(row, seat) {
                                    if (row <= 5) {
                                        return "Нижний зал";
                                    } else if (row == 7 && seat >= 1 && seat <= 4) {
                                        return "Левый балкон";
                                    } else if (row == 8 && seat >= 1 && seat <= 3) {
                                        return "Левый балкон";
                                    } else if (row == 9 && seat >= 1 && seat <= 2) {
                                        return "Левый балкон";
                                    } else if (row == 10 && seat == 1) {
                                        return "Левый балкон";
                                    } else if (row == 7 && seat >= 13 && seat <= 16) {
                                        return "Правый балкон";
                                    } else if (row == 8 && seat >= 12 && seat <= 14) {
                                        return "Правый балкон";
                                    } else if (row == 9 && seat >= 11 && seat <= 12) {
                                        return "Правый балкон";
                                    } else if (row == 10 && seat == 10) {
                                        return "Правый балкон";
                                    } else {
                                        return "Центральный балкон";
                                    }
                                }
                            </script>
                            <div class="custom-scheme-footer2" id="custom-scheme-footer_{{$session->id}}">
                                <div class="row row-cols-1 row-cols-lg-6 custom-scheme-row" id="custom-scheme-row_{{$session->id}}"></div>
                                <div class="custom-scheme-footer-summ-block">
                                    <div class="fs-4 fw-bold mb-1">Сумма: 0 ₽</div>
                                    <a id="buyTicketsBtn_{{$session->id}}" class="btn btn-outline-dark fw-bold border border-2 border-dark fs-5 custom-scheme-footer-button" href="/createTickets">Купить</a>
                                </div>
                            </div>
                            <script>
                                function updateBuyLink_{{$session->id}}(selectedSeats) {
                                    var event = "{{$event->id}}";
                                    var sessionId = "{{ $session->id }}";
                                    var selectedSeatsJSON = JSON.stringify(selectedSeats); // Преобразование массива объектов в строку JSON
                                    var url = "/createTickets?event=" + encodeURIComponent(event) + "&sessionId=" + encodeURIComponent(sessionId) + "&selectedSeats=" + encodeURIComponent(selectedSeatsJSON);
                                    document.getElementById("buyTicketsBtn_{{$session->id}}").setAttribute("href", url);
                                }
                            </script>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="container">
            @if($event->completed == 1)
            <div class="fs-4 text-center fw-bold w-100 mt-5">Событие завершено</div>
            @else
            <div class="fs-4 text-center fw-bold w-100 mt-5">{{$event->formatDate1($event->date)}} • {{$event->formatDate2($event->time)}}</div>
            @endif
            <div id="scheme" class="mt-3 rounded-2 custom-scheme-block p-5 border border-2 border-warning">
                <div class="custom-spinner-block">
                    <div class="spinner-border text-warning" role="status"></div>
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

                    ],
                    "seatStates": {
                        "reserved": {{$jsReservedSeats}}
                    }
                };

                var selectedSeats = [];
                var selectedSeatsInfo = [];

                var plan = new HallPlan({
                    el: '#scheme',
                    scheme: scheme,
                    rowNumbersLeft: true,
                    rowNumbersRight: true,
                    screenText: '{{ $screenText }}',
                    @auth
                    @if($event->completed == 0)
                    onSeatClick: function(seatData, mouseEvent) {
                        onSeatClick(seatData, mouseEvent);
                    },
                    @endif
                    @endauth
                });

                plan.render();

                function onSeatClick(seatData, mouseEvent) {
                    var row = seatData.row;
                    var seat = seatData.seat;
                    var seatIndex = selectedSeats.findIndex(item => item.row === row && item.seat === seat);
                    var zone = getZone(row, seat);
                    var price = getPrice(row, seat);

                    if (seatIndex === -1) {
                        if (selectedSeats.length < 6) {
                            selectedSeats.push({
                                zone: getZone(row, seat),
                                row: row,
                                seat: seat,
                                price: getPrice(row, seat)
                            });
                            plan.toggleSeatState(row, seat, 'blue');
                            console.log(getPrice(row, seat));
                            console.log(getZone(row, seat));

                            var ticketBlock = createTicketBlock(zone, row, seat, price);
                            document.querySelector('.custom-scheme-row').appendChild(ticketBlock);
                        } else {
                            showMessageModal('Ошибка', 'За один заказ можно купить только 6 билетов.');
                            console.log('За один заказ можно купить только 6 билетов.');
                        }
                    } else {
                        selectedSeats.splice(seatIndex, 1);
                        plan.unsetSeatState(row, seat, 'blue');
                        console.log('Место отменено');

                        var ticketToRemove = document.querySelector(`.custom-scheme-row .col:nth-child(${seatIndex + 1})`);
                        ticketToRemove.remove();
                    }

                    var totalPrice = calculateTotalPrice();
                    document.querySelector('.custom-scheme-footer .fs-4').textContent = 'Сумма: ' + totalPrice + ' ₽';

                    console.log("Выбранные места:", selectedSeats);
                    updateBuyLink(selectedSeats);
                }

                function showMessageModal(title, message) {
                    document.getElementById('messageModalLabel').innerText = title;
                    document.querySelector('.message-modal p').innerText = message;
                    let modal = new bootstrap.Modal(document.getElementById('messageModal'));
                    modal.show();
                }

                function createTicketBlock(zone, row, seat, price) {
                    var ticketBlock = document.createElement('div');
                    ticketBlock.className = 'col';

                    var ticketContent = `
                    <div class="custom-scheme-footer-ticket">
                        <div class="custom-scheme-footer-ticket-text fw-bold">Зона: ${zone}</div>
                        <div class="custom-scheme-footer-ticket-text fw-bold">Ряд: ${row}</div>
                        <div class="custom-scheme-footer-ticket-text fw-bold">Место: ${seat}</div>
                        <div class="fw-bold mt-1 fs-5">${price} ₽</div>
                    </div>`;

                    ticketBlock.innerHTML = ticketContent;

                    return ticketBlock;
                }

                function calculateTotalPrice() {
                    var total = 0;
                    selectedSeats.forEach(function(seat) {
                        var price = getPrice(seat.row, seat.seat);
                        total += price;
                    });
                    return total;
                }

                function getPrice(row, seat) {
                    var price = {{$event->price}};
                    if (row <= 5) {
                        return price;
                    } else if (row == 7 && seat >= 5 && seat <= 12) {
                        return price + 250;
                    } else if (row == 8 && seat >= 4 && seat <= 11) {
                        return price + 250;
                    } else if (row == 9 && seat >= 3 && seat <= 10) {
                        return price + 250;
                    } else if (row == 10 && seat >= 2 && seat <= 9) {
                        return price + 250;
                    } else {
                        return price + 500;
                    }
                }

                function getZone(row, seat) {
                    if (row <= 5) {
                        return "Нижний зал";
                    } else if (row == 7 && seat >= 1 && seat <= 4) {
                        return "Левый балкон";
                    } else if (row == 8 && seat >= 1 && seat <= 3) {
                        return "Левый балкон";
                    } else if (row == 9 && seat >= 1 && seat <= 2) {
                        return "Левый балкон";
                    } else if (row == 10 && seat == 1) {
                        return "Левый балкон";
                    } else if (row == 7 && seat >= 13 && seat <= 16) {
                        return "Правый балкон";
                    } else if (row == 8 && seat >= 12 && seat <= 14) {
                        return "Правый балкон";
                    } else if (row == 9 && seat >= 11 && seat <= 12) {
                        return "Правый балкон";
                    } else if (row == 10 && seat == 10) {
                        return "Правый балкон";
                    } else {
                        return "Центральный балкон";
                    }
                }
            </script>
            <div class="custom-scheme-footer">
                <div class="row row-cols-1 row-cols-lg-6 custom-scheme-row"></div>
                <div>
                    <div class="fs-4 fw-bold mb-1">Сумма: 0 ₽</div>
                    <a id="buyTicketsBtn" class="btn btn-outline-dark fw-bold border border-2 border-dark fs-5 custom-scheme-footer-button" href="/createTickets">Купить</a>
                </div>
            </div>
            <script>
                function updateBuyLink(selectedSeats) {
                    var event = "{{$event->id}}";
                    var selectedSeatsJSON = JSON.stringify(selectedSeats); // Преобразование массива объектов в строку JSON
                    var url = "/createTickets?event=" + encodeURIComponent(event) + "&selectedSeats=" + encodeURIComponent(selectedSeatsJSON);
                    document.getElementById("buyTicketsBtn").setAttribute("href", url);
                }
            </script>
        </div>
    @endif
</section>
<section>
    <div class="container">
        <div class="mt-5 fw-bold fs-1 mb-3">О событии</div>
        <p class="fs-5 w-100">{{ $event->description }}</p>
    </div>
</section>
<section>
    <div class="container">
        <div class="mt-5 fw-bold fs-1">Исполнители</div>
        @if($event->performers->isEmpty())
        <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
        @endif
        <div class="custom-row w-100">
            @foreach($event->performers as $performer) <a href="/performer/{{ $performer->id }}" class="rounded-2 custom-performer-link mt-3 me-3">
                <img class="rounded-2" src="/storage/images/{{ $performer->image }}" alt="">
                <span class="fw-bold">{{ $performer->name }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
<section class="mt-5">
    <script>
        let request = "{{ $event->place->address }}";
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
                    content: '{{ $event->place->address }}',
                    position: 'right'
                },
                mapFollowsOnDrag: true
            });
            map.addChild(marker);
        }
    </script>
    <div class="container">
        <div class="mt-5 fw-bold fs-1 mb-3">Адрес</div>
        <div id="map" class="position-relative rounded-2 border border-2 border-warning custom-yandex-map position-relative">
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
<section>
    <div class="container">
        <div class="mt-5 fw-bold fs-1" id="commentsBlock">Отзывы</div>
        @if(!$hasComment)
        <form class="w-50 mt-3 mb-5" method="POST" action="/createComment/{{ $event->id }}" id="createComment">
            @csrf
            <div class="mb-3">
                <label for="comment" class="form-label fs-5 fw-bold">Ваш комментарий</label>
                <textarea class="form-control bg-warning fw-bold border border-2 border-dark fs-5" id="comment" rows="5" name="comment"></textarea>
                @error('comment')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="customRange3" class="form-label fs-5 fw-bold">Ваша оценка</label>
                <div class="position-relative custom-range-block">
                    <div class="range-value fs-4 mb-3 fw-bold text-center w-100" id="rangeValue">6</div>
                    <img src="/storage/images/star.svg" alt="">
                    <input type="range" class="form-range text-warning" min="1" max="10" step="1" id="customRange3" value="1" name="rating">
                </div>
            </div>
            <button type="submit" class="btn btn-warning fw-bold custom-slider-button">Оценить</button>
        </form>
        @endif
        <div>
            <div class="custom-comment-filter-block">
                <div class="fw-bold fs-3">Кол-во отзывов: {{ $commentsCount }}</div>
                <form action="/filtComments/{{$event->id}}" method="POST" id="filtComments">
                @csrf
                    <select class="form-select bg-warning fw-bold" aria-label="Default select example" name="filter">
                        <option value="0" {{ $selectedFilter == 0 ? 'selected' : '' }}>Сначала популярные</option>
                        <option value="1" {{ $selectedFilter == 1 ? 'selected' : '' }}>Сначала новые</option>
                        <option value="2" {{ $selectedFilter == 2 ? 'selected' : '' }}>Сначала старые</option>
                        <option value="3" {{ $selectedFilter == 3 ? 'selected' : '' }}>По рейтингу</option>
                    </select>
                    <button type="submit" class="btn btn-warning fw-bold custom-slider-button">Применить</button>
                </form>
            </div>
            @if($comments->isEmpty() && !$hasComment)
            <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
            @endif
            @if($hasComment)
            <div class="comment mt-3 bg-warning rounded-2 w-50 p-3 border border-1 border-dark position-relative" id="comment_{{$userComment->id}}">
                <div class="custom-comment-user-img"><img class="rounded-2 custom-comment-user-img-img" src="/storage/images/{{ $userComment->user->avatar }}" alt=""><span class="ms-2 fw-bold">{{$userComment->user->name}} {{$userComment->user->surname}}</span><span class="ms-2">
                        @if($userComment->created_at == $userComment->updated_at)
                        {{ $userComment->formatDate1($userComment->created_at) }} {{ $userComment->formatDate2($userComment->created_at) }}
                        @else
                        {{ $userComment->formatDate1($userComment->updated_at) }} {{ $userComment->formatDate2($userComment->updated_at) }} (изменено)
                        @endif
                    </span></div>
                <div class="custom-comment-rating-text position-absolute top-0 start-100 translate-middle fw-bold">{{$userComment->rating}}</div>
                <img class="custom-comment-rating-img position-absolute top-0 start-100 translate-middle" src="/storage/images/star2.svg" alt="">
                <p class="mt-2">{{$userComment->comment}}</p>
                <div class="d-flex custom-comment-footer-block">
                    <div class="custom-comment-user-img">
                    </div>
                    <div class="custom-comment-footer-item">
                        <button type="button" class="btn btn-outline-dark fw-bold" onclick="fillModal('{{ $userComment->comment }}', '{{ $userComment->rating }}')" data-bs-toggle="modal" data-bs-target="#editComment">Изменить</button>
                        <a href="/deleteComment/{{ $userComment->id }}" type="submit" class="btn btn-outline-dark fw-bold ms-1">Удалить</a>
                    </div>
                </div>
            </div>
            @endif
            @foreach($comments as $comment)
            <div class="comment mt-3 bg-warning rounded-2 w-50 p-3 border border-1 border-dark position-relative" id="comment_{{$comment->id}}">
                <div class="custom-comment-user-img"><img class="rounded-2" src="/storage/images/{{ $comment->user->avatar }}" alt=""><span class="ms-2 fw-bold">{{$comment->user->name}} {{$comment->user->surname}}</span><span class="ms-2">
                        @if($comment->created_at == $comment->updated_at)
                        {{ $comment->formatDate1($comment->created_at) }} {{ $comment->formatDate2($comment->created_at) }}
                        @else
                        {{ $comment->formatDate1($comment->updated_at) }} {{ $comment->formatDate2($comment->updated_at) }} (изменено)
                        @endif
                    </span></div>
                <div class="custom-comment-rating-text position-absolute top-0 start-100 translate-middle fw-bold">{{$comment->rating}}</div>
                <img class="custom-comment-rating-img position-absolute top-0 start-100 translate-middle" src="/storage/images/star2.svg" alt="">
                <p class="mt-2">{{$comment->comment}}</p>
                <div class="d-flex custom-comment-footer-block">
                    <div class="custom-comment-user-img">
                        <a href="/likeComment/{{ $comment->id }}" class="custom-comment-user-img-block"><img alt="" src="/storage/images/{{ $comment->isLikedByUser() ? 'like2.svg' : 'like.svg' }}"><span class="fw-bold ms-1">{{$comment->likes->count()}}</span></a>
                        <a href="/dislikeComment/{{ $comment->id }}" class="custom-comment-user-img-block ms-2"><img src="/storage/images/{{ $comment->isDislikedByUser() ? 'dislike2.svg' : 'dislike.svg' }}" alt=""><span class="fw-bold ms-1">{{$comment->dislikes->count()}}</span></a>
                    </div>
                </div>
            </div>
            @endforeach
            <!-- <a class="fw-bold custom-more-comment-button" href="#">Показать ещё</a> -->
        </div>
    </div>
    <script>
        var range = document.getElementById('customRange3');
        var rangeValue = document.getElementById('rangeValue');
    
        function updateRangeValue() {
            rangeValue.innerHTML = range.value;
        }
    
        range.addEventListener('input', updateRangeValue);
        updateRangeValue();
    </script>
    <script>
        function fillModal(commentText, rating) {
            document.getElementById('editCommentText').value = commentText;
            document.getElementById('customRange4').value = rating;
            document.getElementById('rangeValue2').innerText = rating;
        }
    </script>
@endsection