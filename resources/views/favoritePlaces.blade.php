@extends('acc')
@section('acc-content')
<section class="custom-acc-section">
    <div class="container">
        <div class="fs-2 fw-bold mt-1">Избранные места</div>
        @if($places->isEmpty())
        <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
        @endif
        <div class="row row-cols-lg-3">
            @foreach($places as $place)
            <div class="col mt-3">
                <div class="custom-performer-favorite-img-block position-relative border border-2 border-warning rounded-2 p-2" id="place_{{$place->id}}">
                    <img class="custom-performer-img rounded-2 border border-2 border-warning" src="/storage/images/{{$place->image}}" alt="">
                    <a href="/place/{{$place->id}}" class="fw-bold fs-3 ms-3 custom-link-favorite-performer">{{$place->name}}</a>
                    <a href="/favoritePlace/{{$place->id}}" class="custom-performer-unfavorite-a2 rounded-2 ms-3 mt-3">
                        <img alt="" src="/storage/images/{{ $place->isFavorite() ? 'favorite.svg' : 'unfavorite.svg' }}">
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection('acc-content')