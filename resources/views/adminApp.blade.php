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
                <a class="navbar-brand d-flex" href="/admin">
                    <img class="logo" src="/storage/images/logo.svg" alt="">
                    &nbsp;
                    <span class="fw-bold">Ticket Wave [Админ]</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link fw-bold" href="/admin">События</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bold" href="/adminPerformers">Исполнители</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bold" href="/adminPlaces">Места</a>
                        </li>
                    </ul>
                    <div class="position-relative custom-search-block">
                        <form action="/searchAdmin" method="POST" class="d-flex custom-search-width" role="search">
                            @csrf
                            <input class="form-control fw-bold" type="search" placeholder="Поиск по афише" aria-label="Search" name="keywords" id="searchInput">
                        </form>
                        <div id="searchResults"></div>
                    </div>
                    <ul class="navbar-nav ms-auto">
                        @guest
                        <li class="nav-item">
                            <button type="button" class="btn btn-warning fw-bold custom-link" data-bs-toggle="modal" data-bs-target="#authModal">
                                <img src="/storage/images/enter.svg" alt="">
                            </button>
                        </li>
                        @endguest
                        @auth
                        <li class="nav-item">
                            <button type="button" id="accLogoutBtn" class="btn btn-warning fw-bold custom-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <img id="accLogoutImg" src="/storage/images/exit.svg" alt="">
                            </button>
                        </li>
                        @endauth
                    </ul>
                </div>
            </nav>
        </div>
    </header>
    <main>

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

        @yield('content')

    </main>
    <footer id="footer" class="mt-5 pt-5 pb-3">
        <div class="container">
            <div class="custom-footer-item">
                <a class="d-flex fs-5" href="/admin">
                    <img class="logo" src="/storage/images/logo.svg" alt="">
                    &nbsp;
                    <span class="fw-bold">Ticket Wave [Админ]</span>
                </a>
                <p class="mt-2">Добро пожаловать!<br>Ticket Wave - ваш надежный онлайн-партнер в мире развлечений! Наша миссия - сделать ваше времяпровождение еще более увлекательным и доступным.</p>
            </div>
            <div class="custom-footer-item">
                <div class="fw-bold fs-5 mb-2">Афиша</div>
                <ul class="custom-footer-ul">
                    <li class="fw-bold custom-footer-li">
                        <a href="/admin">События</a>
                    </li>
                    <li class="fw-bold custom-footer-li">
                        <a href="/adminPerformers">Исполнители</a>
                    </li>
                    <li class="fw-bold custom-footer-li">
                        <a href="/adminPlaces">Места</a>
                    </li>
                </ul>
            </div>
            <div class="custom-footer-item">
                <div class="custom-footer-item-fake"></div>
            </div>
        </div>
        <div class="mt-5 custom-footer-rights">©2024 Ticket Wave, Все права защищены.</div>
    </footer>
    <script src="/js/jquery-3.7.1.js"></script>
    <script src="/js/bootstrap.bundle.js"></script>
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
                    url: '/searchAdmin',
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