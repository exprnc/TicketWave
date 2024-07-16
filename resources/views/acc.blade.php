@extends('app')
@section('content')
<section class="mt-3">
    <div class="container">
        <nav class="navbar navbar-expand-lg">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="me-2 btn btn-warning fw-bold custom-slider-button" href="/account">Мой аккаунт</a>
                    </li>
                    <li class="nav-item">
                        <a class="me-2 btn btn-warning fw-bold custom-slider-button" href="/tickets">Мои билеты</a>
                    </li>
                    <li class="nav-item">
                        <a class="me-2 btn btn-warning fw-bold custom-slider-button" href="/comments">Мои отзывы</a>
                    </li>
                    <li class="nav-item">
                        <a class="me-2 btn btn-warning fw-bold custom-slider-button" href="/favoriteEvents">Избранные события</a>
                    </li>
                    <li class="nav-item">
                        <a class="me-2 btn btn-warning fw-bold custom-slider-button" href="/favoritePerformers">Избранные исполнители</a>
                    </li>
                    <li class="nav-item">
                        <a class="me-2 btn btn-warning fw-bold custom-slider-button" href="/favoritePlaces">Избранные места</a>
                    </li>
                    <li class="nav-item">
                        <a class="me-2 btn btn-warning fw-bold custom-slider-button" href="/watched">Я смотрел</a>
                    </li>
                    <li class="nav-item">
                        <button type="button" id="accLogoutBtn" class="btn btn-warning fw-bold custom-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
                            <img id="accLogoutImg" src="/storage/images/exit.svg" alt="">
                        </button>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</section>

@yield('acc-content')

@endsection('content')