@extends('adminApp')
@section('content')
<section class="mt-3">
    <div class="container">
        <div class="fs-1 fw-bold">Редактирование исполнителя</div>
        <form action="/editPerformer/{{$performer->id}}" class="mt-3" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label for="name" class="form-label fw-bold fs-5">Название</label>
                <input type="text" class="form-control fw-bold bg-warning border border-black" id="name" name="name" required value="{{$performer->name}}">
                @error('name')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label fw-bold fs-5">Описание</label>
                <textarea class="form-control bg-warning fw-bold border border-black" id="description" rows="10" name="description" required>{{$performer->description}}</textarea>
                @error('description')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="image" class="form-label fw-bold fs-5">Изображение</label>
                <input type="file" class="form-control fw-bold bg-warning border border-black" id="editImgInput" name="image">
                <div id="editImgBlock" class="position-relative">
                    <img id="editImg" class="border border-warning rounded-2 mt-2 custom-edit-img" src="/storage/images/{{$performer->image}}" alt="">
                </div>
                @error('image')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="mt-3 me-2 fs-5 btn btn-warning fw-bold custom-slider-button">Редактировать</button>
            <button type="button" data-bs-toggle="modal" data-bs-target="#deletePerformerModal" class="mt-3 fs-5 btn btn-outline-danger fw-bold">Удалить</button>
        </form>
    </div>
</section>
<section class="deletePerformerModal">
    <div class="modal fade" data-bs-backdrop="static" id="deletePerformerModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
            <div class="modal-content bg-warning">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-bold" id="deletePerformerModalLabel">Осторожно, удаление исполнителя!</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body custom-modal-body message-modal">
                    <p>При удалении исполнителя, потеряется вся информация связанная с исполнителем!</p>
                    <p>Вы действительно хотите удалить исполнителя?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-outline-dark fw-bold">Отмена</button>
                    <a href="/deletePerformer/{{$performer->id}}" class="btn btn-outline-danger fw-bold">Удалить</a>
                </div>
            </div>
        </div>
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
                    <a class="custom-schedule-place fs-5" href="/editPlacePage/{{$event->place->id}}">{{$event->place->name}}</a>
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