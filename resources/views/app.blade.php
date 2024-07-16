<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/hall-plan-master/dist/hall-plan.css">
    <link rel="shortcut icon" type="image/x-icon" href="/storage/images/logo2.svg">
    <script src="/hall-plan-master/dist/hall-plan.js"></script>
    <script crossorigin src="https://cdn.jsdelivr.net/npm/@babel/standalone@7/babel.min.js"></script>
    <script src="https://api-maps.yandex.ru/v3/?apikey=db574cb8-240b-4c27-a963-969c39774077&lang=ru_RU"></script>
    <title>Ticket Wave</title>
</head>

<body>
    <header>
        <div class="container">
            <nav class="navbar navbar-expand-lg">
                <a class="navbar-brand d-flex" href="/">
                    <img class="logo" src="/storage/images/logo.svg" alt="">
                    &nbsp;
                    <span class="fw-bold">Ticket Wave</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link fw-bold" href="/about">О компании</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-bold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Жанры</a>
                            <ul class="dropdown-menu bg-warning">
                                @foreach($genres as $genre)
                                <li><a class="dropdown-item fw-bold" href="/genre/{{$genre->id}}">{{$genre->name}}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                    <div class="position-relative custom-search-block">
                        <form action="/search" method="POST" class="d-flex custom-search-width" role="search">
                            @csrf
                            <input class="form-control fw-bold" type="search" placeholder="Поиск по афише" aria-label="Search" name="keywords" id="searchInput">
                        </form>
                        <div id="searchResults"></div>
                    </div>
                    @auth
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <button type="button" class="me-2 btn btn-outline-dark fw-bold custom-wallet-btn" data-bs-toggle="modal" data-bs-target="#walletModal">
                                <img src="/storage/images/wallet.svg" alt="">
                                @if($wallet->balance > 0)
                                <span class="ms-1">{{$wallet->balance}} ₽</span>
                                @else
                                <span class="ms-1">Пополнить</span>
                                @endif
                            </button>
                        </li>
                    </ul>
                    @endauth
                    <ul class="navbar-nav ms-auto">
                        @guest
                        <li class="nav-item">
                            <button type="button" class="btn btn-warning fw-bold custom-link" data-bs-toggle="modal" data-bs-target="#authModal">
                                <img src="/storage/images/enter.svg" alt="">
                            </button>
                        </li>
                        @endguest
                        @auth
                        <li class="nav-item custom-header-avatar-li">
                            <a href="/account"><img src="/storage/images/{{ $user->avatar }}" alt=""></a>
                        </li>
                        @endauth
                    </ul>
                </div>
            </nav>
        </div>
    </header>
    <main>

        <section class="reg">
            <div class="modal fade" data-bs-backdrop="static" id="regModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
                    <div class="modal-content bg-warning">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5 fw-bold" id="exampleModalLabel">Регистрация</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                        </div>
                        <form action="/reg" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body custom-modal-body">
                                <div class="mb-3 custom-input-avatar">
                                    <div class="custom-input-block">
                                        <div class="avatar-img-block">
                                            <img src="/storage/images/avatar.svg" alt="" id="avatar-img">
                                        </div>
                                        <label for="avatar-input" class="form-label fw-bold" id="avatar-label">Не выбран файл</label>
                                        <input type="file" class="form-control" id="avatar-input" name="avatar">
                                    </div>
                                    @error('avatar')
                                    <div class="form-text">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control fw-bold" id="name" placeholder="Имя" name="name">
                                    @error('name')
                                    <div class="form-text">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control fw-bold" id="surname" placeholder="Фамилия" name="surname">
                                    @error('surname')
                                    <div class="form-text">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <input type="email" class="form-control fw-bold" id="email" placeholder="Электронная почта" name="email">
                                    @error('email')
                                    <div class="form-text">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <input type="password" class="form-control fw-bold" id="password" placeholder="Пароль" name="password">
                                    @error('password')
                                    <div class="form-text">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <input type="password" class="form-control fw-bold" id="password_confirmation" placeholder="Повторите пароль" name="password_confirmation">
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1" checked name="subscribe">
                                    <label class="form-check-label ms-2 fw-bold" for="exampleCheck1">Подписаться на рассылку</label>
                                </div>
                            </div>
                            <div class="modal-footer custom-modal-footer">
                                <a class="link-dark fw-bold" href="" data-bs-toggle="modal" data-bs-target="#authModal">Уже есть аккаунт? Авторизуйтесь</a>
                                <button type="submit" class="btn btn-outline-dark fw-bold">Войти</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="auth">
            <div class="modal fade" data-bs-backdrop="static" id="authModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
                    <div class="modal-content bg-warning">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5 fw-bold" id="exampleModalLabel">Авторизация</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                        </div>
                        <form action="/auth" method="POST">
                            @csrf
                            <div class="modal-body custom-modal-body">
                                <div class="mb-3">
                                    <input type="email" class="form-control fw-bold" id="authEmail" placeholder="Электронная почта" name="authEmail">
                                    @error('authEmail')
                                    <div class="form-text">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <input type="password" class="form-control fw-bold" id="authPassword" placeholder="Пароль" name="authPassword">
                                    @error('authPassword')
                                    <div class="form-text">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer custom-modal-footer">
                                <a class="link-dark fw-bold" href="" data-bs-toggle="modal" data-bs-target="#regModal">Нет аккаунта? Зарегистрируйтесь</a>
                                <button type="submit" class="btn btn-outline-dark fw-bold">Войти</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        @auth
        <section class="wallet">
            <div class="modal fade" data-bs-backdrop="static" id="walletModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
                    <div class="modal-content bg-warning">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5 fw-bold" id="exampleModalLabel">Пополнение баланса</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                        </div>
                        <form action="/topUpBalance" method="POST">
                            @csrf
                            <div class="modal-body custom-modal-body">
                                <div class="mb-3">
                                    <label for="balance" class="form-label fw-bold">У вас сейчас на балансе: {{$wallet->balance}} ₽</label>
                                    <input type="number" min="10" max="99999" class="form-control fw-bold" id="balance" placeholder="Введите сумму для пополнения" name="balance" required>
                                </div>
                            </div>
                            <div class="modal-footer custom-modal-footer">
                                <button type="submit" class="btn btn-outline-dark fw-bold">Пополнить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        @endauth

        <section class="messageModal">
            <div class="modal fade" data-bs-backdrop="static" id="messageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
                    <div class="modal-content bg-warning">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5 fw-bold" id="messageModalLabel">Заголовок модального окна</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body custom-modal-body message-modal">
                            <p>Тело модального окна</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-bs-dismiss="modal" class="btn btn-outline-dark fw-bold">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="logoutModal">
            <div class="modal fade" data-bs-backdrop="static" id="logoutModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
                    <div class="modal-content bg-warning">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5 fw-bold" id="logoutModalLabel">Выход из аккаунта</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body custom-modal-body message-modal">
                            <p>Вы действительно хотите выйти из аккаунта?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-bs-dismiss="modal" class="btn btn-outline-dark fw-bold">Отмена</button>
                            <a href="/logout" class="btn btn-outline-dark fw-bold">Выйти</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="delAccModal">
            <div class="modal fade" data-bs-backdrop="static" id="delAccModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
                    <div class="modal-content bg-warning">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5 fw-bold" id="delAccModalLabel">Осторожно, удаление аккаунта!</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body custom-modal-body message-modal">
                            <p>При удалении аккаунта вся информация об аккаунте будет безвозвратно удалена!</p>
                            <p>Вы действительно хотите удалить аккаунт?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-bs-dismiss="modal" class="btn btn-outline-dark fw-bold">Отмена</button>
                            <a href="/deleteAccount" class="btn btn-outline-danger fw-bold">Удалить</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @yield('content')

    </main>
    <footer id="footer" class="mt-5 pt-5 pb-3">
        <div class="container">
            <div class="custom-footer-item">
                <a class="d-flex fs-5" href="/">
                    <img class="logo" src="/storage/images/logo.svg" alt="">
                    &nbsp;
                    <span class="fw-bold">Ticket Wave</span>
                </a>
                <p class="mt-2">Добро пожаловать!<br>Ticket Wave - ваш надежный онлайн-партнер в мире развлечений! Наша миссия - сделать ваше времяпровождение еще более увлекательным и доступным.</p>
            </div>
            <div class="custom-footer-item">
                <div class="fw-bold fs-5 mb-2">Афиша</div>
                <ul class="custom-footer-ul">
                    <li class="fw-bold custom-footer-li"><a href="/">Главная</a></li>
                    <li class="fw-bold custom-footer-li"><a href="/about">О компании</a></li>
                    <li class="dropdown fw-bold dropend">
                        <a class="nav-link dropdown-toggle fw-bold custom-footer-a" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Жанры</a>
                        <ul class="dropdown-menu bg-warning">
                            @foreach($genres as $genre)
                            <li><a class="dropdown-item fw-bold" href="/genre/{{$genre->id}}">{{ $genre->name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    @auth
                    <li class="fw-bold custom-footer-li"><a href="/account">Мой аккаунт</a></li>
                    @endauth
                    @guest
                    <li class="fw-bold custom-footer-li"><a href="" data-bs-toggle="modal" data-bs-target="#authModal">Авторизация</a></li>
                    <li class="fw-bold custom-footer-li"><a href="" data-bs-toggle="modal" data-bs-target="#regModal">Регистрация</a></li>
                    @endguest
                </ul>
            </div>
            <div class="custom-footer-item">
                <div class="fw-bold fs-5 mb-2">Рассылка</div>
                <p>Подпишитесь на нашу рассылку, чтобы получать электронный билет на почту.</p>
                <form action="/subscribe" method="POST" class="mt-3">
                    @csrf
                    <div class="custom-footer-form-item-block">
                        <input type="email" class="form-control fw-bold" id="newsEmail" placeholder="Электронная почта" name="newsEmail" value="{{ Auth::user() ? $user->email : '' }}">
                        @error('newsEmail')
                        <div class="form-text">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-outline-dark fw-bold">Подписаться</button>
                </form>
                <div class="mt-3 custom-footer-messengers">
                    <a href="https://vk.com/"><img src="/storage/images/vk.svg" alt=""></a>
                    <a href="https://web.telegram.org/"><img src="/storage/images/telegram.svg" alt=""></a>
                </div>
            </div>
        </div>
        <div class="mt-5 custom-footer-rights">©2024 Ticket Wave, Все права защищены.</div>
    </footer>
    <script src="/js/jquery-3.7.1.js"></script>
    <script src="/js/bootstrap.bundle.js"></script>
    <script src="/js/custom-avatar-input.js"></script>
    <script src="/js/custom-avatar-input2.js"></script>
    <script src="/js/accLogoutHover.js"></script>
    @if(session('messageModal'))
    <script>
        let modalTitle = "{{ session('messageModal')['title'] }}";
        let modalMessage = "{{ session('messageModal')['message'] }}";
        let modalScroll = "{{ session('messageModal')['scrollToElement'] }}";

        document.getElementById('messageModalLabel').innerText = modalTitle;
        document.querySelector('.message-modal p').innerText = modalMessage;

        let modal = new bootstrap.Modal(document.getElementById('messageModal'));
        modal.show();

        if (modalScroll) {
            var elementOffset = $(modalScroll).offset().top;
            var windowHeight = $(window).height();
            var scrollPosition = elementOffset - (windowHeight / 2);
            $('html, body').animate({
                scrollTop: scrollPosition
            }, 100);
        }
    </script>
    @endif
    @if(isset($anchor))
    <script>
        console.log('{{ $anchor }}');
        let anchor = "{{ $anchor }}";
        var elementOffset = $(anchor).offset().top;
        var windowHeight = $(window).height();
        var scrollPosition = elementOffset - (windowHeight / 2);
        $('html, body').animate({
            scrollTop: scrollPosition
        }, 100);
    </script>
    @endif
    @if(session('showRegOrAuth'))
    <script>
        let modalType = "{{ session('showRegOrAuth')['type'] }}";
        if (modalType == 'reg') {
            let modal = new bootstrap.Modal(document.getElementById('regModal'));
            modal.show();
        } else {
            let modal = new bootstrap.Modal(document.getElementById('authModal'));
            modal.show();
        }
    </script>
    @endif
    <script>
        var range2 = document.getElementById('customRange4');
        var rangeValue2 = document.getElementById('rangeValue2');

        function updateRangeValue2() {
            rangeValue2.innerHTML = range2.value;
        }

        range2.addEventListener('input', updateRangeValue2);
        updateRangeValue2();
    </script>
    <script>
        if (document.body.clientHeight < 1300) {
            document.getElementById('footer').style = 'margin-top: 30% !important;';
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#searchInput').on('keyup', function(event) {
                var keywords = $(this).val();
                if (event.which === 13) {
                    event.preventDefault();
                }

                if (keywords.trim() === '') {
                    $('#searchResults').html('');
                    return;
                }

                $.ajax({
                    url: '/search',
                    method: 'GET',
                    data: {
                        keywords: keywords
                    },
                    success: function(response) {
                        $('#searchResults').html(response.output);
                    }
                });
            });
        });
    </script>
</body>

</html>