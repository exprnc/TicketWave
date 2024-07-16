@extends('adminApp')
@section('content')
<section class="mt-3">
    <div class="container">
        <div class="fs-1 fw-bold">Создание развлекательного центра</div>
        <form action="/createPlace" class="mt-3" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label fw-bold fs-5">Название</label>
                <input type="text" class="form-control fw-bold bg-warning border border-black" id="name" name="name" required>
                @error('name')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="address" class="form-label fw-bold fs-5">Адрес</label>
                <input type="text" class="form-control fw-bold bg-warning border border-black" id="address" name="address" required>
                @error('address')
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
        <div class="fs-2 fw-bold mt-1">Места</div>
        @if($places->isEmpty())
        <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
        @endif
        <div class="row row-cols-lg-3">
            @foreach($places as $place)
            <div class="col mt-3">
                <div class="custom-performer-favorite-img-block position-relative border border-2 border-warning rounded-2 p-2" id="place_{{$place->id}}">
                    <img class="custom-performer-img rounded-2 border border-2 border-warning" src="/storage/images/{{$place->image}}" alt="">
                    <a href="/editPlacePage/{{$place->id}}" class="fw-bold fs-3 ms-3 custom-link-favorite-performer">{{$place->name}}</a>
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