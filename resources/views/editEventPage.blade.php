@extends('adminApp')
@section('content')
<section>
    <div class="container">
        <div class="fs-1 fw-bold mt-3">Редактирование события</div>
        <form action="/editEvent/{{$event->id}}" class="mt-3" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label for="name" class="form-label fw-bold fs-5">Название</label>
                <input type="text" class="form-control fw-bold bg-warning border border-black" id="name" name="name" value="{{ $event->name }}" required>
                @error('name')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label fw-bold fs-5">Описание</label>
                <textarea class="form-control bg-warning fw-bold border border-black" id="description" rows="10" name="description" required>{{ $event->description }}</textarea>
                @error('description')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="image" class="form-label fw-bold fs-5">Изображение</label>
                <input type="file" class="form-control fw-bold bg-warning border border-black" id="editImgInput" name="image">
                <div id="editImgBlock" class="position-relative">
                    <img id="editImg" class="border border-warning rounded-2 mt-2 custom-edit-img" src="/storage/images/{{$event->image}}" alt="">
                    @if($event->completed == 1)
                    <img src="/storage/images/completed.png" class="rounded-2 position-absolute top-50 start-50 translate-middle custom-edit-event-completed-img" alt="...">
                    @endif
                    <div class="custom-card-rating3 position-absolute top-0 end-0 rounded-2 fw-bold fs-5">{{$event->averageRating}}</div>
                </div>
                @error('image')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="custom-edit-event-footer-block mt-3">
                @if($isHaveSessions)
                <!-- <div class="mb-3 custom-edit-event-width">
                    <label for="dateInput" class="form-label fw-bold fs-5">Дата №1</label>
                    <input type="date" class="form-control fw-bold bg-warning border border-black" id="dateInput" name="date" value="{{ $event->date }}">
                    @error('date')
                    <div class="form-text">{{ $message }}</div>
                    @enderror
                </div> -->
                @else
                <div class="mb-3 custom-edit-event-width">
                    <label for="dateInput" class="form-label fw-bold fs-5">Дата</label>
                    <input type="date" class="form-control fw-bold bg-warning border border-black" id="dateInput" name="date" value="{{ $event->date }}">
                    @error('date')
                    <div class="form-text">{{ $message }}</div>
                    @enderror
                </div>
                @endif
                @if($isHaveSessions)
                <div class="mb-3 custom-min-price-width">
                    <label for="price" class="form-label fw-bold fs-5">Минимальная цена</label>
                    <input type="number" min="10" max="99999" class="form-control fw-bold bg-warning border border-black" id="price" name="price" value="{{ $event->price }}" required>
                    @error('price')
                    <div class="form-text">{{ $message }}</div>
                    @enderror
                </div>
                @else
                <div class="mb-3 custom-edit-event-width">
                    <label for="time" class="form-label fw-bold fs-5">Время</label>
                    <input type="time" class="form-control fw-bold bg-warning border border-black" id="time" name="time" value="{{ $event->time }}">
                    @error('time')
                    <div class="form-text">{{ $message }}</div>
                    @enderror
                </div>
                @endif
                <div class="mb-3 custom-edit-event-width">
                    <label for="age_limit_id" class="form-label fw-bold fs-5">Возраст</label>
                    <select class="form-select bg-warning fw-bold" aria-label="Default select example" id="age_limit_id" name="age_limit_id">
                        @foreach ($age_limits as $age_limit)
                        <option value="{{$age_limit->id}}" {{ $age_limit->name == $event->ageLimit->name ? 'selected' : '' }}>{{$age_limit->name}}</option>
                        @endforeach
                    </select>
                    @error('age_limit_id')
                    <div class="form-text">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 custom-edit-event-width">
                    <label for="subgenre_id" class="form-label fw-bold fs-5">Поджанр</label>
                    <select class="form-select bg-warning fw-bold" aria-label="Default select example" id="subgenre_id" name="subgenre_id">
                        @foreach ($subgenres as $subgenre)
                        <option value="{{$subgenre->id}}" {{ $subgenre->name == $event->subgenre->name ? 'selected' : '' }}>{{$subgenre->name}} [{{$subgenre->genre->name}}]</option>
                        @endforeach
                    </select>
                    @error('subgenre_id')
                    <div class="form-text">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 custom-edit-event-width">
                    <label for="place_id" class="form-label fw-bold fs-5">Место</label>
                    <select class="form-select bg-warning fw-bold" aria-label="Default select example" id="place_id" name="place_id">
                        @foreach ($places as $place)
                        <option value="{{$place->id}}" {{ $place->name == $event->place->name ? 'selected' : '' }}>{{$place->name}}</option>
                        @endforeach
                    </select>
                    @error('place_id')
                    <div class="form-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @if(!$isHaveSessions)
            <div class="mb-3 custom-min-price-width">
                <label for="price" class="form-label fw-bold fs-5">Минимальная цена</label>
                <input type="number" min="10" max="99999" class="form-control fw-bold bg-warning border border-black" id="price" name="price" value="{{ $event->price }}" required>
                @error('price')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            @else
            <!-- <div class="mb-3 custom-min-price-width">
                <label for="dateInput2" class="form-label fw-bold fs-5">Дата №2</label>
                <input type="date" class="form-control fw-bold bg-warning border border-black" id="dateInput2" name="date2" value="{{ $lastDate }}">
                @error('date2')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div> -->
            @endif
            <div class="mb-3 custom-min-performers-width">
                <label for="performers_ids" class="form-label fw-bold fs-5">Исполнители</label>
                <select class="form-select bg-warning border border-black fw-bold custom-min-performers-height" id="performers_ids" multiple aria-label="multiple select example" name="performers[]">
                    @foreach ($performers as $performer)
                    <option value="{{$performer->id}}" {{ in_array($performer->id, $selectedPerformers) ? 'selected' : '' }}>{{$performer->name}}</option>
                    @endforeach
                </select>
            </div>
            <!-- @if($isHaveSessions)
            <div id="sessionsBlock" class="mb-3 custom-edit-event-width">
                @foreach ($firstSessions as $index => $session)
                    <label for="time{{ $index + 1 }}" class="form-label fw-bold fs-5 mt-3 session">Сеанс №{{ $index + 1 }}</label>
                    <input type="time" class="form-control fw-bold bg-warning border border-black session" id="time{{ $index + 1 }}" name="time{{ $index + 1 }}" value="{{ $session->time }}" required>
                @endforeach
                <button type="button" id="sessionAddBtn" class="mt-3 me-2 fs-5 btn btn-warning fw-bold custom-slider-button">
                    <img id="sessionAddImg" src="/storage/images/plus.svg" alt="">
                </button>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const sessionsBlock = document.getElementById('sessionsBlock');
                    let sessionCount = document.querySelectorAll('.session').length / 2;  // sessionLabel и sessionInput оба имеют класс 'session'
                    let sessionAddBtn = document.getElementById('sessionAddBtn');

                    sessionAddBtn.addEventListener('click', function() {
                        addSession();
                    });

                    sessionAddBtn.addEventListener('mouseenter', function() {
                        sessionAddBtn.style.background = '#000000';
                        sessionAddBtn.style.border = '1px solid #000000';
                        document.getElementById('sessionAddImg').src = '/storage/images/plus2.svg';
                    });

                    sessionAddBtn.addEventListener('mouseleave', function() {
                        sessionAddBtn.style.background = '#FFC107';
                        sessionAddBtn.style.border = '1px solid #FFC107';
                        document.getElementById('sessionAddImg').src = '/storage/images/plus.svg';
                    });

                    function addSession() {
                        sessionCount++;

                        const sessionLabel = document.createElement('label');
                        sessionLabel.setAttribute('for', `time${sessionCount}`);
                        sessionLabel.className = 'form-label fw-bold fs-5 mt-3 session';
                        sessionLabel.textContent = `Сеанс №${sessionCount}`;

                        const sessionInput = document.createElement('input');
                        sessionInput.type = 'time';
                        sessionInput.className = 'form-control fw-bold bg-warning border border-black session';
                        sessionInput.id = `time${sessionCount}`;
                        sessionInput.name = `time${sessionCount}`;
                        sessionInput.required = true;

                        sessionsBlock.insertBefore(sessionLabel, sessionAddBtn);
                        sessionsBlock.insertBefore(sessionInput, sessionAddBtn);
                    }
                });
            </script>
            @endif -->
            @if($event->completed == 1)
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="unlock_places" name="unlock_places">
                <label class="form-check-label ms-2 fw-bold" for="exampleCheck1">Разблокировать места</label>
            </div>
            @endif
            <button type="submit" class="mt-3 me-2 fs-5 btn btn-warning fw-bold custom-slider-button">Редактировать</button>
            @if($event->completed == 1)
            <button type="button" data-bs-toggle="modal" data-bs-target="#deleteEventModal" class="mt-3 fs-5 btn btn-outline-danger fw-bold">Удалить</button>
            @else
            <button type="button" data-bs-toggle="modal" data-bs-target="#cancelEventModal" class="mt-3 fs-5 btn btn-outline-danger fw-bold">Отменить</button>
            @endif
        </form>
    </div>
</section>
<section class="cancelEventModal">
    <div class="modal fade" data-bs-backdrop="static" id="cancelEventModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
            <div class="modal-content bg-warning">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-bold" id="cancelEventModalLabel">Осторожно, отмена события!</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body custom-modal-body message-modal">
                    <p>При отмене события происходит возврат денег и удаление билетов, а также событие завершается!</p>
                    <p>Вы действительно хотите отменить событие?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-outline-dark fw-bold">Отмена</button>
                    <a href="/cancelEvent/{{$event->id}}" class="btn btn-outline-danger fw-bold">Отменить</a>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="deleteEventModal">
    <div class="modal fade" data-bs-backdrop="static" id="deleteEventModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
            <div class="modal-content bg-warning">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-bold" id="deleteEventModalLabel">Осторожно, удаление события!</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body custom-modal-body message-modal">
                    <p>При удалении события, потеряется вся информация связанная с событием!</p>
                    <p>Вы действительно хотите удалить событие?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-outline-dark fw-bold">Отмена</button>
                    <a href="/deleteEvent/{{$event->id}}" class="btn btn-outline-danger fw-bold">Удалить</a>
                </div>
            </div>
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
        <div class="mt-5 fw-bold fs-1 mb-3">Сеансы</div>
        <div class="custom-event-sessions-date-block">
            <form action="/filtAdminSessions/{{$event->id}}" method="POST" id="filtAdminSessions">
                @csrf
                <select class="form-select bg-warning fw-bold" aria-label="Default select example" id="date" name="date">
                    @foreach ($dates as $date)
                    <option value="{{ $date }}" {{$date == $secondSessions->first()->formattedDate() ? 'selected' : ''}}>{{ $date }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-warning fw-bold custom-slider-button">Применить</button>
            </form>
        </div>
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
                                screenText: '{{ $screenText }}'
                            });

                            plan_{{$session->id}}.render();
                        </script>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="container">
        <div class="mt-5 fw-bold fs-1 mb-3">Схема</div>
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
                screenText: '{{ $screenText }}'
            });

            plan.render();
        </script>
    </div>
    @endif
</section>
<section>
    <div class="container">
        <div class="mt-5 fw-bold fs-1">Исполнители</div>
        @if($event->performers->isEmpty())
        <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
        @endif
        <div class="custom-row w-100">
            @foreach($event->performers as $performer) <a href="/editPerformerPage/{{ $performer->id }}" class="rounded-2 custom-performer-link mt-3 me-3">
                <img class="rounded-2" src="/storage/images/{{ $performer->image }}" alt="">
                <span class="fw-bold">{{ $performer->name }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
<section>
    <div class="container">
        <div class="mt-5 fw-bold fs-1" id="commentsBlock">Отзывы</div>
        <div class="mt-3">
            <div class="custom-comment-filter-block">
                <div class="fw-bold fs-3">Кол-во отзывов: {{ $commentsCount }}</div>
                <form action="/filtAdminComments/{{$event->id}}" method="POST" id="filtAdminComments">
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
            @if($comments->isEmpty())
            <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
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
                        <a href="/likeComment/{{ $comment->id }}" class="custom-comment-user-img-block"><img alt="" src="/storage/images/like.svg"><span class="fw-bold ms-1">{{$comment->likes->count()}}</span></a>
                        <a href="/dislikeComment/{{ $comment->id }}" class="custom-comment-user-img-block ms-2"><img src="/storage/images/dislike.svg" alt=""><span class="fw-bold ms-1">{{$comment->dislikes->count()}}</span></a>
                    </div>
                    <div class="custom-comment-footer-item">
                        <a href="/deleteComment/{{ $comment->id }}" class="btn btn-outline-dark fw-bold ms-1">Удалить</a>
                    </div>
                </div>
            </div>
            @endforeach
            <!-- <a class="fw-bold custom-more-comment-button" href="#">Показать ещё</a> -->
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
<script>
    const dateInput = document.getElementById('dateInput');

    const currentDate = new Date();
    currentDate.setDate(currentDate.getDate());
    const minDate = currentDate.toISOString().split('T')[0];

    dateInput.setAttribute('min', minDate);

    dateInput.addEventListener('input', function() {
        const selectedDate = new Date(this.value);
        if (selectedDate < currentDate) {
            this.value = minDate;
        }
    });

    // const dateInput2 = document.getElementById('dateInput2');

    // const currentDate2 = new Date();
    // currentDate2.setDate(currentDate2.getDate() + 1);
    // const minDate2 = currentDate2.toISOString().split('T')[0];

    // dateInput2.setAttribute('min', minDate2);

    // dateInput2.addEventListener('input', function() {
    //     const selectedDate2 = new Date(this.value);
    //     if (selectedDate2 < currentDate2) {
    //         this.value = minDate2;
    //     }
    // });
</script>
<script src="/js/jquery-3.7.1.js"></script>
@endsection('content')