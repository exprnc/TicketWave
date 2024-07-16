<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Dislike;
use App\Models\Event;
use App\Models\FavoriteEvent;
use App\Models\FavoritePerformer;
use App\Models\FavoritePlace;
use App\Models\Like;
use App\Models\Performer;
use App\Models\Place;
use App\Models\Scheme;
use App\Models\Session as ModelsSession;
use App\Models\Subscriber;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Symfony\Component\Mime\Part\TextPart;

class UserController extends Controller
{
    function reg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100|regex:/^[а-яё]+$/iu',
            'surname' => 'required|max:100|regex:/^[а-яё]+$/iu',
            'email' => 'required|max:100|email|unique:users',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,svg',
            'password' => 'required|confirmed|min:6|max:100'
        ], [
            'name.required' => 'Имя обязательно.',
            'name.max' => 'Максимальное количество символов в имени: 100.',
            'name.regex' => 'Имя должно состоять только из русских букв без цифр.',
            'surname.required' => 'Фамилия обязательна.',
            'surname.max' => 'Максимальное количество символов в фамилии: 100.',
            'surname.regex' => 'Фамилия должна состоять только из русских букв без цифр.',
            'email.required' => 'Электронная почта обязательна.',
            'email.max' => 'Максимальное количество символов в электронной почте: 100.',
            'email.email' => 'Электронная почта должна быть в правильном формате.',
            'email.unique' => 'Электронная почта должна быть уникальной.',
            'avatar.required' => 'Аватарка обязательна.',
            'avatar.image' => 'Аватарка должна быть в следующих форматах: jpeg, png, jpg, svg.',
            'avatar.mimes' => 'Аватарка должна быть в следующих форматах: jpeg, png, jpg, svg.',
            'password.required' => 'Пароль обязателен.',
            'password.confirmed' => 'Пароли не совпадают.',
            'password.min' => 'Минимальное количество символов в пароле: 6.',
            'password.max' => 'Максимальное количество символов в пароле: 100.'
        ]);

        if ($validator->fails()) return redirect('/')->withErrors($validator)->with('showRegOrAuth', ['type' => 'reg']);

        if ($file = $request->file('avatar')) {
            $file_name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            Storage::putFileAs('public/images', $file, $file_name);
        }

        $hashedPassword = Hash::make($request->password);

        $wallet = Wallet::create([
            'balance' => 0
        ]);

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'avatar' => $file_name,
            'password' => $hashedPassword,
            'wallet_id' => $wallet->id,
            'role_id' => 2
        ]);


        if (!$user || !$wallet) return redirect('/')->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => '#']);

        if ($request->has('subscribe')) {
            if ($request->subscribe) {
                $subscriber = Subscriber::where('user_id', $user->id)->orWhere('email', $user->email)->first();
                if ($subscriber) return redirect('/')->with('messageModal', ['title' => 'Ошибка', 'message' => 'Вы уже подписаны на рассылку, уберите галочку "Подписаться на рассылку".', 'scrollToElement' => '#']);

                $create = Subscriber::create([
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                if (!$create) return redirect('/')->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => '#']);
            }
        }

        $auth = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ]);

        if (!$auth) return redirect('/')->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => '#']);

        $regenerate = $request->session()->regenerate();

        if ($regenerate) {
            return redirect('/')->with('messageModal', ['title' => 'Успех', 'message' => "Успешная регистрация. Добро пожаловать $user->name!", 'scrollToElement' => '#']);
        } else {
            return redirect('/')->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => '#']);
        }
    }

    function auth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'authEmail' => 'required|max:100|email',
            'authPassword' => 'required|min:6|max:100'
        ], [
            'authEmail.required' => 'Электронная почта обязательна.',
            'authEmail.max' => 'Максимальное количество символов в электронной почте: 100.',
            'authEmail.email' => 'Электронная почта должна быть в правильном формате.',
            'authPassword.required' => 'Пароль обязателен.',
            'authPassword.min' => 'Минимальное количество символов в пароле: 6.',
            'authPassword.max' => 'Максимальное количество символов в пароле: 100.'
        ]);

        if ($validator->fails()) return redirect('/')->withErrors($validator)->with('showRegOrAuth', ['type' => 'auth']);

        $auth = Auth::attempt([
            'email' => $request->authEmail,
            'password' => $request->authPassword
        ]);

        if (!$auth) return redirect('/')->with('messageModal', ['title' => 'Ошибка', 'message' => 'Неправильная почта или пароль.', 'scrollToElement' => '#']);

        $regenerate = $request->session()->regenerate();

        $user = Auth::user();

        if($user->admin()) {
            return redirect('/admin')->with('messageModal', ['title' => 'Успех', 'message' => "Успешная авторизация. Добро пожаловать $user->name!", 'scrollToElement' => '#']);
        }

        if ($regenerate) {
            return redirect('/')->with('messageModal', ['title' => 'Успех', 'message' => "Успешная авторизация. Добро пожаловать $user->name!", 'scrollToElement' => '#']);
        } else {
            return redirect('/')->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => '#']);
        }
    }

    function logout()
    {
        Auth::logout();
        return redirect('/')->with('messageModal', ['title' => 'Успех', 'message' => "Вы вышли из аккаунта.", 'scrollToElement' => '#']);
    }

    function editAcc(Request $request)
    {
        $user = Auth::user();

        if ($user->avatar != $request->avatar && $file = $request->file('avatar')) {
            $file_name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            Storage::putFileAs('public/images', $file, $file_name);
        } else {
            $file_name = $user->avatar;
            $request->merge(['avatar' => $file_name]);
        }

        if ($request->file('avatar') !== null) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100|regex:/^[а-яё]+$/iu',
                'surname' => 'required|max:100|regex:/^[а-яё]+$/iu',
                'email' => 'required|max:100|email',
                'avatar' => 'required|image|mimes:jpeg,png,jpg,svg',
            ], [
                'name.required' => 'Имя обязательно.',
                'name.max' => 'Максимальное количество символов в имени: 100.',
                'name.regex' => 'Имя должно состоять только из русских букв без цифр.',
                'surname.required' => 'Фамилия обязательна.',
                'surname.max' => 'Максимальное количество символов в фамилии: 100.',
                'surname.regex' => 'Фамилия должна состоять только из русских букв без цифр.',
                'email.required' => 'Электронная почта обязательна.',
                'email.max' => 'Максимальное количество символов в электронной почте: 100.',
                'email.email' => 'Электронная почта должна быть в правильном формате.',
                'avatar.required' => 'Аватарка обязательна.',
                'avatar.image' => 'Аватарка должна быть в следующих форматах: jpeg, png, jpg, svg.',
                'avatar.mimes' => 'Аватарка должна быть в следующих форматах: jpeg, png, jpg, svg.',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100|regex:/^[а-яё]+$/iu',
                'surname' => 'required|max:100|regex:/^[а-яё]+$/iu',
                'email' => 'required|max:100|email',
            ], [
                'name.required' => 'Имя обязательно.',
                'name.max' => 'Максимальное количество символов в имени: 100.',
                'name.regex' => 'Имя должно состоять только из русских букв без цифр.',
                'surname.required' => 'Фамилия обязательна.',
                'surname.max' => 'Максимальное количество символов в фамилии: 100.',
                'surname.regex' => 'Фамилия должна состоять только из русских букв без цифр.',
                'email.required' => 'Электронная почта обязательна.',
                'email.max' => 'Максимальное количество символов в электронной почте: 100.',
                'email.email' => 'Электронная почта должна быть в правильном формате.',
            ]);
        }

        if ($validator->fails()) return redirect()->back()->withErrors($validator);

        if ($request->has('subscribe2')) {
            $subscriber = Subscriber::where('user_id', $user->id)->orWhere('email', $user->email)->first();
            if (!$subscriber) {
                $create = Subscriber::create([
                    'user_id' => $user->id,
                    'email' => $request->email
                ]);
                if (!$create) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => '#']);
            }else{
                $update = $subscriber->update([
                    'email' => $request->email
                ]);
                if (!$update) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => '#']);
            }
        } else {
            $subscriber = Subscriber::where('user_id', $user->id)->orWhere('email', $user->email)->first();
            if ($subscriber) {
                $delete = $subscriber->delete();
                if (!$delete) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => '#']);
            }
        }

        $update = $user->update([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'avatar' => $file_name,
        ]);

        if ($update) {
            return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Вы отредактировали информацию об аккаунте!", 'scrollToElement' => '#']);
        } else {
            return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => '#']);
        }
    }

    function deleteAccount()
    {
        $user = Auth::user();
        Session::flush();
        Auth::logout();
        $delete = $user->delete();
        if ($delete) return redirect('/')->with('messageModal', ['title' => 'Успех', 'message' => "Аккаунт успешно удалён.", 'scrollToElement' => '#']);
        else return redirect('/')->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => '#']);
    }

    function createComment(Request $request, Event $event)
    {
        if (!Auth::user()) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Сначала авторизуйтесь.', 'scrollToElement' => '#createComment']);

        $user = Auth::user();

        $comment = Comment::where('event_id', $event->id)->where('user_id', $user->id)->first();

        if ($comment) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'На одно событие можно оставить только один отзыв.', 'scrollToElement' => '#createComment']);

        $validator = Validator::make($request->all(), [
            'comment' => 'required',
        ], [
            'comment.required' => 'Комментарий обязателен.',
        ]);

        if ($validator->fails()) return redirect()->to(url()->previous() . '#createComment')->withErrors($validator);

        $create = Comment::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'comment' => $request->comment,
            'rating' => $request->rating
        ]);

        if ($create) {
            return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Вы оставили комментарий!", 'scrollToElement' => '#comment_' . $create->id]);
        } else {
            return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => '#']);
        }
    }

    function editComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required',
        ], [
            'comment.required' => 'Комментарий обязателен.',
        ]);

        $comment = Comment::find($request->commentId);

        if ($validator->fails()) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Комментарий обязателен.', 'scrollToElement' => '#comment_' . $comment->id]);

        $update = $comment->update([
            'comment' => $request->comment,
            'rating' => $request->rating,
        ]);

        if ($update) {
            return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Вы отредактировали комментарий!", 'scrollToElement' => '#comment_' . $comment->id]);
        } else {
            return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => '#comment_' . $comment->id]);
        }
    }

    function deleteComment(Comment $comment)
    {
        $delete = $comment->delete();
        if ($delete) return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Отзыв успешно удалён.", 'scrollToElement' => '#commentsBlock']);
        else return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => '#commentsBlock']);
    }

    function likeComment(Comment $comment)
    {
        if (!Auth::user()) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Сначала авторизуйтесь.', 'scrollToElement' => "#comment_" . $comment->id]);
        if (Auth::user()->isAdmin()) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Админ не может ставить лайки.', 'scrollToElement' => "#comment_" . $comment->id]);
        $user = Auth::user();

        $userLike = Like::where('user_id', $user->id)->where('comment_id', $comment->id)->first();
        $userDislike = Dislike::where('user_id', $user->id)->where('comment_id', $comment->id)->first();

        if ($userLike) {
            $userLike->delete();
            return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Вы убрали лайк с комментария!", 'scrollToElement' => '#comment_' . $comment->id]);
        } else {
            Like::create([
                'comment_id' => $comment->id,
                'user_id' => $user->id
            ]);
            if ($userDislike !== null) {
                $userDislike->delete();
            }
            return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Вы лайкнули комментарий!", 'scrollToElement' => '#comment_' . $comment->id]);
        }
    }

    function dislikeComment(Comment $comment)
    {
        if (!Auth::user()) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Сначала авторизуйтесь.', 'scrollToElement' => "#comment_" . $comment->id]);
        if (Auth::user()->isAdmin()) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Админ не может ставить дизлайки.', 'scrollToElement' => "#comment_" . $comment->id]);
        $user = Auth::user();

        $userLike = Like::where('user_id', $user->id)->where('comment_id', $comment->id)->first();
        $userDislike = Dislike::where('user_id', $user->id)->where('comment_id', $comment->id)->first();

        if ($userDislike) {
            $userDislike->delete();
            return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Вы убрали дизлайк с комментария!", 'scrollToElement' => '#comment_' . $comment->id]);
        } else {
            Dislike::create([
                'comment_id' => $comment->id,
                'user_id' => $user->id
            ]);
            if ($userLike !== null) {
                $userLike->delete();
            }
            return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Вы дизлайкнули комментарий!", 'scrollToElement' => '#comment_' . $comment->id]);
        }
    }

    function favoriteEvent(Event $event)
    {
        if (!Auth::user()) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Сначала авторизуйтесь.', 'scrollToElement' => "#event_" . $event->id]);
        $user = Auth::user();

        $userFavorite = FavoriteEvent::where('user_id', $user->id)->where('event_id', $event->id)->first();

        if ($userFavorite) {
            $userFavorite->delete();
            return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => 'Событие убрано из избранного.', 'scrollToElement' => "#event_" . $event->id]);
        } else {
            FavoriteEvent::create([
                'event_id' => $event->id,
                'user_id' => $user->id
            ]);
            return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => 'Событие добавлено в избранное.', 'scrollToElement' => "#event_" . $event->id]);
        }
    }

    function favoritePerformer(Performer $performer)
    {
        if (!Auth::user()) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Сначала авторизуйтесь.', 'scrollToElement' => "#performer_" . $performer->id]);
        $user = Auth::user();

        $userFavorite = FavoritePerformer::where('user_id', $user->id)->where('performer_id', $performer->id)->first();

        if ($userFavorite) {
            $userFavorite->delete();
            return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => 'Исполнитель убран из избранного.', 'scrollToElement' => "#performer_" . $performer->id]);
        } else {
            FavoritePerformer::create([
                'performer_id' => $performer->id,
                'user_id' => $user->id
            ]);
            return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => 'Исполнитель добавлен в избранное.', 'scrollToElement' => "#performer_" . $performer->id]);
        }
    }

    function favoritePlace(Place $place)
    {
        if (!Auth::user()) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Сначала авторизуйтесь.', 'scrollToElement' => "#place_" . $place->id]);
        $user = Auth::user();

        $userFavorite = FavoritePlace::where('user_id', $user->id)->where('place_id', $place->id)->first();

        if ($userFavorite) {
            $userFavorite->delete();
            return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => 'Развлекательный центр убран из избранного.', 'scrollToElement' => "#place_" . $place->id]);
        } else {
            FavoritePlace::create([
                'place_id' => $place->id,
                'user_id' => $user->id
            ]);
            return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => 'Развлекательный центр добавлен в избранное.', 'scrollToElement' => "#place_" . $place->id]);
        }
    }

    function topUpBalance(Request $request)
    {
        $user = Auth::user();
        $walletId = User::where('id', $user->id)->value('wallet_id');
        $wallet = Wallet::where('id', $walletId)->first();
        $update = $wallet->update([
            'balance' => $wallet->balance + $request->balance
        ]);

        if ($update) return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Баланс успешно пополнен.", 'scrollToElement' => "#"]);
        else return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#"]);
    }

    public function createTickets(Request $request)
    {
        if (!Auth::user()) {
            return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Сначала авторизуйтесь.', 'scrollToElement' => "#scheme"]);
        }

        $user = Auth::user();
        $selectedSeats = json_decode($request->selectedSeats, true);


        if (empty($selectedSeats)) {
            return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Вы должны выбрать хотя бы одно место.', 'scrollToElement' => "#scheme"]);
        }

        $totalPrice = 0;
        foreach ($selectedSeats as $seat) {
            $totalPrice += $seat['price'];
        }

        $walletId = User::where('id', $user->id)->value('wallet_id');
        $wallet = Wallet::where('id', $walletId)->first();


        if ($wallet->balance < $totalPrice) {
            return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Нехватка средств. Пополните баланс.', 'scrollToElement' => "#scheme"]);
        }


        $wallet->update([
            'balance' => $wallet->balance - $totalPrice
        ]);

        if($request->has('sessionId')) {
            $session = ModelsSession::where('id', $request->sessionId)->first();
            $dbScheme = Scheme::where('id', $session->scheme_id)->first();
        }else{
            $dbSchemeId = Event::where('id', $request->event)->first()->scheme_id;
            $dbScheme = Scheme::where('id', $dbSchemeId)->first();
        }
        $jsonScheme = json_decode($dbScheme->seats, true);

        foreach ($jsonScheme as &$schemeSeat) {
            foreach ($selectedSeats as $seat) {
                if ($schemeSeat['zone'] == $seat['zone'] && $schemeSeat['row'] == $seat['row'] && $schemeSeat['seat'] == $seat['seat']) {
                    $schemeSeat['reserved'] = true;
                    $schemeSeat['user_id'] = $user->id;
                }
            }
        }

        $dbScheme->update([
            'seats' => json_encode($jsonScheme)
        ]);

        $ticketIds = [];

        foreach ($selectedSeats as $seat) {
            $create = Ticket::create([
                'event_id' => $request->event,
                'user_id' => $user->id,
                'zone' => $seat['zone'],
                'row' => $seat['row'],
                'seat' => $seat['seat'],
                'price' => $seat['price'],
                'completed' => 0
            ]);
            if (!$create) {
                return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#scheme"]);
            }
            array_push($ticketIds, $create->id);
        }

        $tickets = Ticket::whereIn('id', $ticketIds)->get();

        $subscriber = Subscriber::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        if ($subscriber) {
            return $this->generateTicketsPDF($subscriber, $tickets);
        }

        return redirect('/tickets')->with('messageModal', ['title' => 'Успех', 'message' => "Успешная покупка.", 'scrollToElement' => "#"]);
    }

    function generateTicketsPDF($subscriber, $tickets)
    {
        $pdf = new Dompdf();
        $html = View::make('ticketsPDF', compact('tickets'))->render();
        $pdf->loadHtml($html);
        $pdf->render();
        $pdfData = $pdf->output();

        try {
            Mail::send([], [], function ($message) use ($pdfData, $subscriber) {
                $message->from('exprnc7@gmail.com', 'ticketwave.test')
                    ->to($subscriber->email)
                    ->subject('Ticket Wave')
                    ->attachData($pdfData, 'tickets.pdf');
            });
            return redirect('/tickets')->with('messageModal', ['title' => 'Успех', 'message' => "Успешная покупка. Электронные билеты также отправлены на вашу почту.", 'scrollToElement' => "#"]);
        } catch (\Exception $e) {
            return redirect('/tickets')->with('messageModal', ['title' => 'Ошибка', 'message' => "Не удалось отправить электронные билеты на вашу почту.", 'scrollToElement' => "#"]);
        }
    }

    function formattedDate($date)
    {
        return Carbon::parse($date)->format('d.m.Y H:i');
    }

    function ticketReturn(Request $request)
    {
        $ticket = Ticket::where('id', $request->ticket)->first();
        $user = Auth::user();
        if($ticket->isHaveSession()) {
            $dbScheme = Scheme::where('id', $ticket->session()->scheme_id)->first();
        }else{
            $dbSchemeId = Event::where('id', $ticket->event->id)->first()->scheme_id;
            $dbScheme = Scheme::where('id', $dbSchemeId)->first();
        }
        

        $jsonScheme = json_decode($dbScheme->seats, true);

        foreach ($jsonScheme as &$schemeSeat) {
            if ($schemeSeat['user_id'] == $user->id && $schemeSeat['zone'] == $ticket->zone && $schemeSeat['row'] == $ticket->row && $schemeSeat['seat'] == $ticket->seat) {
                $schemeSeat['reserved'] = false;
                $schemeSeat['user_id'] = 0;
            }
        }

        $update = $dbScheme->update([
            'seats' => json_encode($jsonScheme)
        ]);

        $walletId = User::where('id', $user->id)->value('wallet_id');
        $wallet = Wallet::where('id', $walletId)->first();

        $update = $wallet->update([
            'balance' => $wallet->balance + $ticket->price
        ]);

        if (!$update) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#"]);

        $delete = $ticket->delete();

        if (!$delete) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#"]);

        return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Вы вернули билет.", 'scrollToElement' => "#"]);
    }

    function subscribe(Request $request)
    {
        if (!Auth::user()) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Сначала авторизуйтесь.', 'scrollToElement' => "#newsEmail"]);
        $user = Auth::user();
        $subscriber = Subscriber::where('user_id', $user->id)->orWhere('email', $request->newsEmail)->first();
        if ($subscriber) return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Вы уже подписаны на рассылку.', 'scrollToElement' => "#newsEmail"]);

        $create = Subscriber::create([
            'user_id' => $user->id,
            'email' => $request->newsEmail,
        ]);

        if ($create) return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Успешная подписка на рассылку.", 'scrollToElement' => "#newsEmail"]);
        else return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#newsEmail"]);
    }
}
