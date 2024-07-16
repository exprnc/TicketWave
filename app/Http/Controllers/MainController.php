<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Event;
use App\Models\Genre;
use App\Models\Performer;
use App\Models\Place;
use App\Models\Scheme;
use App\Models\Session;
use App\Models\Subgenre;
use App\Models\Subscriber;
use App\Models\Ticket;
use App\Models\Watched;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class MainController extends Controller
{
    function index()
    {
        $selectedFilter = 0;
        $selectedGenre = 0;
        $events = Event::orderBy('completed', 'asc')->select('events.*', DB::raw('COUNT(favorite_events.event_id) as total'))
            ->leftJoin('favorite_events', 'events.id', '=', 'favorite_events.event_id')
            ->groupBy('events.id')
            ->orderByDesc('total')
            ->get();
        $firstEvent = Event::where('completed', 0)->latest()->first();
        $newEvents = Event::where('completed', 0)->where('id', '!=', $firstEvent->id)->latest()->take(4)->get();
        return view('index', compact('events', 'newEvents', 'firstEvent', 'selectedGenre', 'selectedFilter'));
    }

    function filtUserSessions(Request $request, Event $event) {
        $sessions = Session::where('event_id', $event->id)->get();
        if($sessions->isNotEmpty()) {
            $isHaveSessions = true;
        } else {
            $isHaveSessions = false;
        }

        $anchor = "#filtUserSessions";

        $startDate = Carbon::parse($sessions->min('date'));
        $endDate = Carbon::parse($sessions->max('date'));
        
        $dates = [];
        while ($startDate->lte($endDate)) {
            $dates[] = $startDate->format('d.m.Y');
            $startDate->addDay();
        }
        $requestDate = Carbon::createFromFormat('d.m.Y', $request->date)->format('Y-m-d');
        $secondSessions = $sessions->where('date', $requestDate);

        $selectedFilter = 0;
        $scheme = $event->scheme;
        if ($event->subgenre->genre->name == "Кино") $screenText = "ЭКРАН";
        else $screenText = "СЦЕНА";
        $seatsData = json_decode($scheme->seats, true);
        $reservedSeats = [];
        foreach ($seatsData as $seat) {
            if ($seat['reserved']) {
                $reservedSeats[] = [$seat['row'], $seat['seat']];
            }
        }
        $jsReservedSeats = json_encode($reservedSeats);
        if ($user = Auth::user()) {
            $userWatched = Watched::where('user_id', $user->id)->where('event_id', $event->id)->first();
            if (!$userWatched) {
                Watched::create([
                    'event_id' => $event->id,
                    'user_id' => $user->id
                ]);
            } else {
                $userWatched->update([
                    'updated_at' => Carbon::now()
                ]);
            }
            if ($userComment = Comment::where('user_id', $user->id)->where('event_id', $event->id)->first()) {
                $hasComment = true;
                $comments = Comment::where('comments.event_id', $event->id)
                    ->where('comments.user_id', '!=', $user->id)
                    ->leftJoin('likes', 'comments.id', '=', 'likes.comment_id')
                    ->select('comments.*')
                    ->selectRaw('COUNT(likes.id) as like_count')
                    ->groupBy('comments.id')
                    ->orderByDesc('like_count')
                    ->get();

                $commentsCount = $comments->count() + 1;
                return view('event', compact('event', 'jsReservedSeats', 'screenText', 'comments', 'userComment', 'hasComment', 'commentsCount', 'selectedFilter', 'isHaveSessions', 'secondSessions', 'dates', 'anchor'));
            }
        }
        $hasComment = false;
        $comments = Comment::where('event_id', $event->id)
            ->leftJoin('likes', 'comments.id', '=', 'likes.comment_id')
            ->select('comments.*')
            ->selectRaw('COUNT(likes.id) as like_count')
            ->groupBy('comments.id')
            ->orderByDesc('like_count')
            ->get();

        $commentsCount = $comments->count();
        return view('event', compact('event', 'jsReservedSeats', 'screenText', 'comments', 'hasComment', 'commentsCount', 'selectedFilter', 'isHaveSessions', 'secondSessions', 'dates', 'anchor'));
    }

    function event(Event $event)
    {
        $sessions = Session::where('event_id', $event->id)->get();
        if($sessions->isNotEmpty()) {
            $isHaveSessions = true;
        } else {
            $isHaveSessions = false;
        }

        $startDate = Carbon::parse($sessions->min('date'));
        $endDate = Carbon::parse($sessions->max('date'));
        
        $dates = [];
        while ($startDate->lte($endDate)) {
            $dates[] = $startDate->format('d.m.Y');
            $startDate->addDay();
        }
        $secondSessions = $sessions->where('date', $sessions->min('date'));

        $selectedFilter = 0;
        $scheme = $event->scheme;
        if ($event->subgenre->genre->name == "Кино") $screenText = "ЭКРАН";
        else $screenText = "СЦЕНА";
        $seatsData = json_decode($scheme->seats, true);
        $reservedSeats = [];
        foreach ($seatsData as $seat) {
            if ($seat['reserved']) {
                $reservedSeats[] = [$seat['row'], $seat['seat']];
            }
        }
        $jsReservedSeats = json_encode($reservedSeats);
        if ($user = Auth::user()) {
            $userWatched = Watched::where('user_id', $user->id)->where('event_id', $event->id)->first();
            if (!$userWatched) {
                Watched::create([
                    'event_id' => $event->id,
                    'user_id' => $user->id
                ]);
            } else {
                $userWatched->update([
                    'updated_at' => Carbon::now()
                ]);
            }
            if ($userComment = Comment::where('user_id', $user->id)->where('event_id', $event->id)->first()) {
                $hasComment = true;
                $comments = Comment::where('comments.event_id', $event->id)
                    ->where('comments.user_id', '!=', $user->id)
                    ->leftJoin('likes', 'comments.id', '=', 'likes.comment_id')
                    ->select('comments.*')
                    ->selectRaw('COUNT(likes.id) as like_count')
                    ->groupBy('comments.id')
                    ->orderByDesc('like_count')
                    ->get();

                $commentsCount = $comments->count() + 1;
                return view('event', compact('event', 'jsReservedSeats', 'screenText', 'comments', 'userComment', 'hasComment', 'commentsCount', 'selectedFilter', 'isHaveSessions', 'secondSessions', 'dates'));
            }
        }
        $hasComment = false;
        $comments = Comment::where('event_id', $event->id)
            ->leftJoin('likes', 'comments.id', '=', 'likes.comment_id')
            ->select('comments.*')
            ->selectRaw('COUNT(likes.id) as like_count')
            ->groupBy('comments.id')
            ->orderByDesc('like_count')
            ->get();

        $commentsCount = $comments->count();
        return view('event', compact('event', 'jsReservedSeats', 'screenText', 'comments', 'hasComment', 'commentsCount', 'selectedFilter', 'isHaveSessions', 'secondSessions', 'dates'));
    }

    function performer(Performer $performer)
    {
        return view('performer', compact('performer'));
    }

    function favoritePerformers()
    {
        $user = Auth::user();
        $performers = Performer::whereHas('favoritePerformers', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('created_at', 'desc')->get();

        return view('favoritePerformers', compact('performers'));
    }

    function favoriteEvents()
    {
        $user = Auth::user();
        $events = Event::whereHas('favoriteEvents', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('created_at', 'desc')->get();

        return view('favoriteEvents', compact('events'));
    }

    function favoritePlaces()
    {
        $user = Auth::user();
        $places = Place::whereHas('favoritePlaces', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('created_at', 'desc')->get();

        return view('favoritePlaces', compact('places'));
    }

    function comments()
    {
        $user = Auth::user();
        $comments = Comment::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return view('comments', compact('comments'));
    }

    function place(Place $place)
    {
        if($place->id == 3) {
            $screenText = "ЭКРАН";
        }else{
            $screenText = "СЦЕНА";
        }
        return view('place', compact('place', 'screenText'));
    }

    public function genre(Genre $genre)
    {
        $subgenres = Subgenre::where('genre_id', $genre->id)->pluck('id');
        $events = Event::orderBy('completed', 'asc')->whereIn('subgenre_id', $subgenres)->get();
        return view('genre', compact('events', 'genre'));
    }

    function search(Request $request)
    {
        $output = "";

        $keywords = $request->input('keywords');

        $events = Event::orderBy('completed', 'asc')->where('name', 'like', "%$keywords%")
            ->orWhere('description', 'like', "%$keywords%")
            ->orWhere('price', 'like', "%$keywords%")
            ->orWhere('date', 'like', "%$keywords%")
            ->orWhereHas('ageLimit', function ($query) use ($keywords) {
                $query->where('name', 'like', "%$keywords%");
            })->orWhereHas('subgenre', function ($query) use ($keywords) {
                $query->where('name', 'like', "%$keywords%")
                    ->orWhereHas('genre', function ($query) use ($keywords) {
                        $query->where('name', 'like', "%$keywords%");
                    });
            })->get();

        $places = Place::where('name', 'like', "%$keywords%")
            ->orWhere('description', 'like', "%$keywords%")
            ->orWhere('address', 'like', "%$keywords%")
            ->get();

        $performers = Performer::where('name', 'like', "%$keywords%")
            ->orWhere('description', 'like', "%$keywords%")
            ->get();

        $output .= '<div class="custom-search-result-block border border-1 border-dark rounded-2 mt-2 p-2"><div class="list-group">';

        if ($events->isEmpty() && $places->isEmpty() && $performers->isEmpty()) {
            $output .= '<div class="fw-bold">По вашему запросу ничего не найдено.</div>';
            return response()->json(['output' => $output]);
        }

        foreach ($events as $event) {
            $completedText = $event->completed == 1 ? ' [Завершено]' : '';
            $output .= '<a href="/event/' . $event->id . '" class="mt-1 fw-bold list-group-item list-group-item-action active custom-header-search-result-item">'
                . $event->name . ' [Событие]'
                . $completedText . '</a>';
        }

        foreach ($performers as $performer) {
            $output .= '<a href="/performer/' . $performer->id . '" class="mt-1 fw-bold list-group-item list-group-item-action active custom-header-search-result-item">' . $performer->name . ' [Исполнитель]</a>';
        }

        foreach ($places as $place) {
            $output .= '<a href="/place/' . $place->id . '" class="mt-1 fw-bold list-group-item list-group-item-action active custom-header-search-result-item">' . $place->name . ' [Развлекательный центр]</a>';
        }

        $output .= '</div></div>';

        return response()->json(['output' => $output]);
    }

    function watched()
    {
        $user = Auth::user();
        $watched2 = Watched::where('user_id', $user->id)->orderBy('updated_at', 'desc')->get();
        return view('watched', compact('watched2'));
    }

    function tickets()
    {
        $user = Auth::user();
        $tickets = Ticket::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($date) {
                return $date->created_at; // группируем по дате без времени
            });
        return view('tickets', compact('tickets'));
    }

    function about()
    {
        return view('about');
    }

    function account()
    {
        $user = Auth::user();
        $signed = Subscriber::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        return view('account', compact('signed'));
    }

    function filtIndexEvents(Request $request)
    {
        if ($request->filter == 1) {
            $selectedFilter = 1;
            $events = Event::orderBy('completed', 'asc')->orderBy('updated_at', 'desc')->get();
        } else if ($request->filter == 2) {
            $selectedFilter = 2;
            $events = Event::orderBy('completed', 'asc')->orderBy('updated_at', 'asc')->get();
        } else if ($request->filter == 3) {
            $selectedFilter = 3;
            $events = Event::all()->sortBy([
                ['completed', 'asc'],
                ['averageRating', 'desc']
            ]);
        } else {
            $selectedFilter = 0;

            $events = Event::orderBy('completed', 'asc')->select('events.*', DB::raw('COUNT(favorite_events.event_id) as total'))
                ->leftJoin('favorite_events', 'events.id', '=', 'favorite_events.event_id')
                ->groupBy('events.id')
                ->orderByDesc('total')
                ->get();
        }

        if ($request->genre_id != 0) {
            if ($request->genre_id == 6) {
                $selectedGenre = $request->genre_id;
                $events = $events->where('completed', 1);
            } else {
                $selectedGenre = $request->genre_id;
                $subgenres = Subgenre::where('genre_id', $request->genre_id)->pluck('id');
                $events = $events->whereIn('subgenre_id', $subgenres);
            }
        } else {
            $selectedGenre = 0;
        }

        $anchor = '#filtIndexEvents';
        $firstEvent = Event::where('completed', 0)->latest()->first();
        $newEvents = Event::where('completed', 0)->where('id', '!=', $firstEvent->id)->latest()->take(4)->get();
        return view('index', compact('events', 'newEvents', 'firstEvent', 'selectedFilter', 'selectedGenre', 'anchor'));
    }

    function filtComments(Request $request, Event $event)
    {
        $sessions = Session::where('event_id', $event->id)->get();
        if ($sessions->isNotEmpty()) {
            $isHaveSessions = true;
        } else {
            $isHaveSessions = false;
        }
        $anchor = '#filtComments';
        if ($request->filter == 1) {
            $selectedFilter = 1;
            $comments = Comment::orderBy('created_at', 'desc');
        } else if ($request->filter == 2) {
            $selectedFilter = 2;
            $comments = Comment::orderBy('created_at', 'asc');
        } else if ($request->filter == 3) {
            $selectedFilter = 3;
            $comments = Comment::orderBy('rating', 'desc');
        } else {
            $selectedFilter = 0;
            $comments = Comment::withCount('likes')
                ->orderByDesc('like_count');
        }

        $scheme = $event->scheme;
        if ($event->subgenre->genre->name == "Кино") $screenText = "ЭКРАН";
        else $screenText = "СЦЕНА";
        $seatsData = json_decode($scheme->seats, true);
        $reservedSeats = [];
        foreach ($seatsData as $seat) {
            if ($seat['reserved']) {
                $reservedSeats[] = [$seat['row'], $seat['seat']];
            }
        }
        $jsReservedSeats = json_encode($reservedSeats);
        if ($user = Auth::user()) {
            $userWatched = Watched::where('user_id', $user->id)->where('event_id', $event->id)->first();
            if (!$userWatched) {
                Watched::create([
                    'event_id' => $event->id,
                    'user_id' => $user->id
                ]);
            } else {
                $userWatched->update([
                    'updated_at' => Carbon::now()
                ]);
            }
            if ($userComment = Comment::where('user_id', $user->id)->where('event_id', $event->id)->first()) {
                $hasComment = true;
                $comments = $comments->where('comments.event_id', $event->id)
                    ->where('comments.user_id', '!=', $user->id)
                    ->leftJoin('likes', 'comments.id', '=', 'likes.comment_id')
                    ->select('comments.*')
                    ->selectRaw('COUNT(likes.id) as like_count')
                    ->groupBy('comments.id')
                    ->orderByDesc('like_count')
                    ->get();

                $commentsCount = $comments->count() + 1;
                return view('event', compact('event', 'jsReservedSeats', 'screenText', 'comments', 'userComment', 'hasComment', 'commentsCount', 'selectedFilter', 'anchor', 'isHaveSessions', 'sessions'));
            }
        }
        $hasComment = false;
        $comments = $comments->where('event_id', $event->id)
            ->leftJoin('likes', 'comments.id', '=', 'likes.comment_id')
            ->select('comments.*')
            ->selectRaw('COUNT(likes.id) as like_count')
            ->groupBy('comments.id')
            ->orderByDesc('like_count')
            ->get();

        $commentsCount = $comments->count();
        return view('event', compact('event', 'jsReservedSeats', 'screenText', 'comments', 'hasComment', 'commentsCount', 'selectedFilter', 'anchor', 'isHaveSessions', 'sessions'));
    }
}
