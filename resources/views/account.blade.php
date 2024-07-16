@extends('acc')
@section('acc-content')
<section class="custom-acc-section">
    <div class="container">
        <div class="fs-2 fw-bold mt-1">Мой аккаунт</div>
        <div class="fw-bold fs-5 mt-3">Дата создания аккаунта: {{ $user->formatDate1($user->created_at) }} • {{ $user->formatDate2($user->created_at) }}</div>
        <form action="/editAcc" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="mb-3 mt-3 custom-input-avatar">
                <div class="custom-input-block">
                    <div class="avatar-img-block bg-warning">
                        <img src="storage/images/{{ $user->avatar }}" alt="" id="acc-avatar-img">
                    </div>
                    <label for="acc-avatar-input" class="form-label fw-bold" id="acc-avatar-label">{{ $user->avatar }}</label>
                    <input type="file" class="form-control" id="acc-avatar-input" name="avatar">
                </div>
                @error('avatar')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <input type="text" class="form-control fw-bold bg-warning border border-black" id="name" placeholder="Имя" name="name" value="{{ $user->name }}">
                @error('name')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <input type="text" class="form-control fw-bold bg-warning border border-black" id="surname" placeholder="Фамилия" name="surname" value="{{ $user->surname }}">
                @error('surname')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <input type="email" class="form-control fw-bold bg-warning border border-black" id="email" placeholder="Электронная почта" name="email" value="{{ $user->email }}">
                @error('email')
                <div class="form-text">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1" name="subscribe2" {{ $signed ? 'checked' : '' }}>
                <label class="form-check-label ms-2 fw-bold" for="exampleCheck1">Подписаться на рассылку</label>
            </div>
            <div class="mb-3 d-flex">
                <button type="submit" class="btn btn-warning fw-bold custom-slider-button">Редактировать</button>
                <button type="button" class="btn btn-danger fw-bold custom-del-acc-btn ms-2" data-bs-toggle="modal" data-bs-target="#delAccModal"><img src="storage/images/cross.svg" alt=""><span class="ms-2">Удалить аккаунт</span></a>
            </div>
        </form>
    </div>
</section>
@endsection('acc-content')