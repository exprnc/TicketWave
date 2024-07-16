@extends('acc')
@section('acc-content')
<section class="custom-acc-section">
    <div class="container">
        <div class="fs-2 fw-bold mt-1">Избранные исполнители</div>
        @if($performers->isEmpty())
        <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
        @endif
        <div class="row row-cols-lg-3">
            @foreach($performers as $performer)
            <div class="col mt-3">
                <div class="custom-performer-favorite-img-block position-relative border border-2 border-warning rounded-2 p-2" id="performer_{{$performer->id}}">
                    <img class="custom-performer-img rounded-2 border border-2 border-warning" src="/storage/images/{{$performer->image}}" alt="">
                    <a href="/performer/{{$performer->id}}" class="fw-bold fs-3 ms-3 custom-link-favorite-performer">{{$performer->name}}</a>
                    <a href="/favoritePerformer/{{$performer->id}}" class="custom-performer-unfavorite-a2 rounded-2 ms-3 mt-3">
                        <img alt="" src="/storage/images/{{ $performer->isFavorite() ? 'favorite.svg' : 'unfavorite.svg' }}">
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection('acc-content')