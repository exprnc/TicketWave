@extends('adminApp')
@section('content')
<section class="mt-3">
    <div class="container">
        <div class="fs-1 fw-bold">Создание события</div>
        <form action="/createEvent" class="mt-3" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label fw-bold fs-5">Название</label>
                <input type="text" class="form-control fw-bold bg-warning border border-black" id="name" name="name" required>
                @error('name')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label fw-bold fs-5">Описание</label>
                <textarea class="form-control bg-warning fw-bold border border-black" id="description" rows="10" name="description" required></textarea>
                @error('description')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="image" class="form-label fw-bold fs-5">Изображение</label>
                <input type="file" class="form-control fw-bold bg-warning border border-black" id="editImgInput" name="image" required>
                <div id="editImgBlock" class="position-relative">
                    <img id="editImg" class="border border-warning rounded-2 mt-2 custom-edit-img" src="/storage/images/placeholder.svg" alt="">
                </div>
                @error('image')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="custom-edit-event-footer-block mt-3">
                <div class="mb-3 custom-edit-event-width">
                    <label for="dateInput" class="form-label fw-bold fs-5">Дата</label>
                    <input type="date" class="form-control fw-bold bg-warning border border-black" id="dateInput" name="date" required>
                    @error('date')
                    <div class="form-text">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 custom-edit-event-width" id="timeBlock">
                    <label for="time" class="form-label fw-bold fs-5">Время</label>
                    <input type="time" class="form-control fw-bold bg-warning border border-black" id="time" name="time" required>
                    @error('time')
                    <div class="form-text">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 custom-edit-event-width">
                    <label for="age_limit_id" class="form-label fw-bold fs-5">Возраст</label>
                    <select class="form-select bg-warning fw-bold" aria-label="Default select example" id="age_limit_id" name="age_limit_id" required>
                        @foreach ($age_limits as $age_limit)
                        <option value="{{$age_limit->id}}">{{$age_limit->name}}</option>
                        @endforeach
                    </select>
                    @error('age_limit_id')
                    <div class="form-text">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 custom-edit-event-width">
                    <label for="subgenre_id" class="form-label fw-bold fs-5">Поджанр</label>
                    <select class="form-select bg-warning fw-bold" aria-label="Default select example" id="subgenre_id" name="subgenre_id" required>
                        @foreach ($subgenres as $subgenre)
                        <option value="{{$subgenre->id}}">{{$subgenre->name}} [{{$subgenre->genre->name}}]</option>
                        @endforeach
                    </select>
                    @error('subgenre_id')
                    <div class="form-text">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 custom-edit-event-width">
                    <label for="place_id" class="form-label fw-bold fs-5">Место</label>
                    <select class="form-select bg-warning fw-bold" aria-label="Default select example" id="place_id" name="place_id" required>
                        @foreach ($places as $place)
                        <option value="{{$place->id}}">{{$place->name}}</option>
                        @endforeach
                    </select>
                    @error('place_id')
                    <div class="form-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3 custom-min-price-width" id="priceBlock">
                <label for="price" class="form-label fw-bold fs-5">Минимальная цена</label>
                <input type="number" min="10" max="99999" class="form-control fw-bold bg-warning border border-black" id="price" name="price" required>
                @error('price')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3 custom-min-performers-width">
                <label for="performers_ids" class="form-label fw-bold fs-5">Исполнители</label>
                <select class="form-select bg-warning border border-black fw-bold custom-min-performers-height" id="performers_ids" multiple aria-label="multiple select example" name="performers[]">
                    @foreach ($performers as $performer)
                    <option value="{{$performer->id}}">{{$performer->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="unlock_sessions" name="unlock_sessions">
                <label class="form-check-label ms-2 fw-bold" for="unlock_sessions">Разные сеансы</label>
            </div>
            <div id="sessionsBlock" class="mb-3 custom-edit-event-width"></div>
            <button type="submit" class="mt-3 me-2 fs-5 btn btn-warning fw-bold custom-slider-button">Создать</button>
        </form>
    </div>
</section>
<section class="mt-3">
    <div class="container">
        <div class="custom-event-filt">
            <div class="fs-1 fw-bold">События</div>
            <form action="/filtAdminEvents" method="POST" id="filtAdminEvents" id="filtAdminEvents">
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
                        </div>
                        <div class="custom-card-body">
                            <p class="card-text mb-1 custom-card-p">{{ $event->description }}</p>
                            <div class="custom-card-body-item">
                                <img src="/storage/images/clock.svg" alt="">
                                <span class="fw-bold fs-6">{{ $event->formatDate1($event->date) }} • {{ $event->formatDate2($event->time) }}</span>
                            </div>
                            <div class="custom-card-body-item">
                                <img src="/storage/images/location.svg" alt="">
                                <a class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover fw-bold" href="/editPlacePage/{{$event->place->id}}">{{ $event->place->name }}</a>
                            </div>
                        </div>
                        <div class="custom-card-footer">
                            <div class="fs-5 fw-bold">от {{ $event->price }} ₽</div>
                            <a href="/editEventPage/{{ $event->id }}" class="btn btn-outline-dark fw-bold">Подробнее</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
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
    document.addEventListener('DOMContentLoaded', function() {
        const unlockSessionsCheckbox = document.getElementById('unlock_sessions');
        const sessionsBlock = document.getElementById('sessionsBlock');
        const timeBlock = document.getElementById('timeBlock');
        const priceBlock = document.getElementById('priceBlock');
        const footerBlock = document.querySelector('.custom-edit-event-footer-block');
        const footerBlockContainer = footerBlock.parentNode;  // Контейнер блока footerBlock
        let sessionCount = 0;
        let sessionAddBtn;
        let dateInputBlock;

        unlockSessionsCheckbox.addEventListener('change', function() {
            if (unlockSessionsCheckbox.checked) {
                addSession(); // Добавляем первый сеанс при включении чекбокса
                movePriceInput(); // Перемещаем инпут минимальной цены
                removeTimeInput(); // Удаляем инпут времени
                addDateInput(); // Добавляем второй инпут даты
                updateDateLabel(); // Обновляем метку первого инпута даты
                setMinDateForDateInput2(); // Устанавливаем минимальную дату для второго инпута даты
            } else {
                clearSessions(); // Удаляем все сеансы при отключении чекбокса
                resetPriceInput(); // Возвращаем инпут минимальной цены
                restoreTimeInput(); // Возвращаем инпут времени
                removeDateInput(); // Удаляем второй инпут даты
                resetDateLabel(); // Восстанавливаем метку первого инпута даты
            }
        });

        function clearSessions() {
            while (sessionsBlock.firstChild) {
                sessionsBlock.removeChild(sessionsBlock.firstChild);
            }
            sessionCount = 0;
            sessionAddBtn = null;
        }

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

            if (sessionCount === 1) {
                sessionAddBtn = document.createElement('button');
                sessionAddBtn.type = 'button';
                sessionAddBtn.id = 'sessionAddBtn';
                sessionAddBtn.className = 'mt-3 me-2 fs-5 btn btn-warning fw-bold custom-slider-button';
                sessionAddBtn.innerHTML = '<img id="sessionAddImg" src="/storage/images/plus.svg" alt="">';
                sessionsBlock.appendChild(sessionAddBtn);

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
            }

            sessionsBlock.insertBefore(sessionLabel, sessionAddBtn);
            sessionsBlock.insertBefore(sessionInput, sessionAddBtn);
        }

        function movePriceInput() {
            footerBlock.insertBefore(priceBlock, timeBlock.nextSibling);
        }

        function resetPriceInput() {
            footerBlock.parentNode.insertBefore(priceBlock, footerBlock.nextSibling);
        }

        function removeTimeInput() {
            if (timeBlock.parentNode) {
                timeBlock.parentNode.removeChild(timeBlock);
            }
        }

        function restoreTimeInput() {
            footerBlock.insertBefore(timeBlock, footerBlock.children[1]);
        }

        function addDateInput() {
            dateInputBlock = document.createElement('div');
            dateInputBlock.className = 'mb-3 custom-min-price-width';
            dateInputBlock.innerHTML = `
                <label for="dateInput2" class="form-label fw-bold fs-5">Дата №2</label>
                <input type="date" class="form-control fw-bold bg-warning border border-black" id="dateInput2" name="date2" required>
                @error('date2')
                <div class="form-text">{{ $message }}</div>
                @enderror
            `;
            footerBlockContainer.insertBefore(dateInputBlock, footerBlock.nextSibling);
        }

        function removeDateInput() {
            if (dateInputBlock && dateInputBlock.parentNode) {
                dateInputBlock.parentNode.removeChild(dateInputBlock);
                dateInputBlock = null;
            }
        }

        function updateDateLabel() {
            const dateInputLabel = document.querySelector('label[for="dateInput"]');
            if (dateInputLabel) {
                dateInputLabel.textContent = 'Дата №1';
            }
        }

        function resetDateLabel() {
            const dateInputLabel = document.querySelector('label[for="dateInput"]');
            if (dateInputLabel) {
                dateInputLabel.textContent = 'Дата';
            }
        }

        function setMinDateForDateInput2() {
            const dateInput2 = document.getElementById('dateInput2');
            const currentDate2 = new Date();
            currentDate2.setDate(currentDate2.getDate() + 1);
            const minDate2 = currentDate2.toISOString().split('T')[0];

            dateInput2.setAttribute('min', minDate2);

            dateInput2.addEventListener('input', function() {
                const selectedDate2 = new Date(this.value);
                if (selectedDate2 < currentDate2) {
                    this.value = minDate2;
                }
            });
        }

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
    });
</script>
@endsection