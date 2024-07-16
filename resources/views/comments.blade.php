@extends('acc')
@section('acc-content')
<section>
    <div class="container">
        <div class="fs-2 fw-bold mt-1" id="commentsBlock">Мои отзывы</div>
        @if($comments->isEmpty())
        <div class="mt-3 fs-2 mb-3">Здесь пока ничего нет.</div>
        @endif
        @foreach($comments as $comment)
        <div class="comment mt-3 bg-warning rounded-2 w-50 p-3 border border-1 border-dark position-relative" id="comment_{{$comment->id}}">
            <a href="/event/{{$comment->event->id}}" class="fw-bold mb-3 fs-5 custom-comment-event-title">{{$comment->event->name}}</a>
            <div class="custom-comment-user-img"><img class="rounded-2 custom-comment-user-img-img" src="/storage/images/{{ $comment->user->avatar }}" alt=""><span class="ms-2 fw-bold">{{$comment->user->name}} {{$comment->user->surname}}</span><span class="ms-2">
                    @if($comment->created_at == $comment->updated_at)
                    {{ $comment->formatDate1($comment->created_at) }} {{ $comment->formatDate2($comment->created_at) }}
                    @else
                    {{ $comment->formatDate1($comment->updated_at) }} {{ $comment->formatDate2($comment->updated_at) }} (изменено)
                    @endif
                </span></div>
            <div class="custom-comment-rating-text position-absolute top-0 start-100 translate-middle fw-bold">{{$comment->rating}}</div>
            <img class="custom-comment-rating-img position-absolute top-0 start-100 translate-middle" src="/storage/images/star2.svg" alt="">
            <p class="mt-2">{{$comment->comment}}</p>
            <div class="d-flex custom-comment-footer-block">
                <div class="custom-comment-user-img">
                    <!-- <a class="custom-comment-user-img-block"><img src="/storage/images/like2.svg" alt=""><span class="fw-bold ms-1">{{$comment->likes->count()}}</span></a>
                        <a class="custom-comment-user-img-block ms-2"><img src="/storage/images/dislike.svg" alt=""><span class="fw-bold ms-1">{{$comment->dislikes->count()}}</span></a> -->
                </div>
                <div class="custom-comment-footer-item">
                    <button type="button" class="btn btn-outline-dark fw-bold" onclick="fillModal('{{ $comment->comment }}', '{{ $comment->rating }}')" data-bs-toggle="modal" data-bs-target="#editComment">Изменить</button>
                    <a href="/deleteComment/{{ $comment->id }}" type="submit" class="btn btn-outline-dark fw-bold ms-1">Удалить</a>
                </div>
            </div>
        </div>
        <section class="editComment">
            <div class="modal fade" data-bs-backdrop="static" id="editComment" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered custom-modal-dialog">
                    <div class="modal-content bg-warning">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5 fw-bold" id="exampleModalLabel">Редактирование отзыва</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                        </div>
                        <form action="/editComment" id="editCommentForm" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-body">
                                <div class="mb-5">
                                    <textarea class="form-control bg-warning fw-bold border border-2 border-dark fs-5" id="editCommentText" rows="5" name="comment"></textarea>
                                    @error('comment')
                                    <div class="form-text">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <div class="position-relative custom-range-block2">
                                        <div class="range-value2 fs-4 mb-3 fw-bold text-center w-100" id="rangeValue2">6</div>
                                        <img src="/storage/images/star2.svg" alt="">
                                        <input type="range" class="form-range custom-range-style" min="1" max="10" step="1" id="customRange4" value="1" name="rating">
                                        <input type="hidden" name="commentId" value="{{ $comment->id }}">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-outline-dark fw-bold custom-slider-button">Редактировать</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        @endforeach
    </div>
</section>
<script>
    function fillModal(commentText, rating) {
        document.getElementById('editCommentText').value = commentText;
        document.getElementById('customRange4').value = rating;
        document.getElementById('rangeValue2').innerText = rating;
    }
</script>
@endsection('acc-content')