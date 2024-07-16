@extends('adminApp')
@section('content')
<section class="mt-3">
    <div class="container">
        <div class="fs-1 fw-bold">Создание исполнителя</div>
        <form action="/createPerformer" class="mt-3" method="POST" enctype="multipart/form-data">
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
            <button type="submit" class="mt-3 me-2 fs-5 btn btn-warning fw-bold custom-slider-button">Создать</button>
        </form>
    </div>
</section>
<section class="mt-5">
    <div class="container">
        <div class="fs-2 fw-bold mt-1">Исполнители</div>
        @if($performers->isEmpty())
        <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
        @endif
        <div class="row row-cols-lg-3">
            @foreach($performers as $performer)
            <div class="col mt-3">
                <div class="custom-performer-favorite-img-block position-relative border border-2 border-warning rounded-2 p-2" id="performer_{{$performer->id}}">
                    <img class="custom-performer-img rounded-2 border border-2 border-warning" src="/storage/images/{{$performer->image}}" alt="">
                    <a href="/editPerformerPage/{{$performer->id}}" class="fw-bold fs-3 ms-3 custom-link-favorite-performer">{{$performer->name}}</a>
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
@endsection('content')