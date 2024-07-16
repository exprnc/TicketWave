<?php

namespace App\Http\Controllers;

use App\Models\AgeLimit;
use App\Models\Comment;
use App\Models\Event;
use App\Models\Performer;
use App\Models\Place;
use App\Models\Scheme;
use App\Models\Session;
use App\Models\Subgenre;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AdminController extends Controller
{
    function admin()
    {
        $performers = Performer::all();
        $age_limits = AgeLimit::all();
        $subgenres = Subgenre::all();
        $places = Place::all();
        $selectedFilter = 0;
        $selectedGenre = 0;
        $events = Event::orderBy('completed', 'asc')->select('events.*', DB::raw('COUNT(favorite_events.event_id) as total'))
            ->leftJoin('favorite_events', 'events.id', '=', 'favorite_events.event_id')
            ->groupBy('events.id')
            ->orderByDesc('total')
            ->get();
        return view('admin', compact('events', 'selectedFilter', 'selectedGenre', 'performers', 'age_limits', 'subgenres', 'places'));
    }

    function filtAdminEvents(Request $request)
    {
        $performers = Performer::all();
        $age_limits = AgeLimit::all();
        $places = Place::all();
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

        $subgenres = Subgenre::all();

        $firstEvent = Event::where('completed', 0)->latest()->first();
        $newEvents = Event::where('completed', 0)->where('id', '!=', $firstEvent->id)->latest()->take(4)->get();
        $anchor = '#filtAdminEvents';
        return view('admin', compact('events', 'selectedFilter', 'selectedGenre', 'anchor', 'performers', 'age_limits', 'subgenres', 'places'));
    }

    function searchAdmin(Request $request)
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
            $output .= '<a href="/editEventPage/' . $event->id . '" class="mt-1 fw-bold list-group-item list-group-item-action active custom-header-search-result-item">'
                . $event->name . ' [Событие]'
                . $completedText . '</a>';
        }

        foreach ($performers as $performer) {
            $output .= '<a href="/editPerformerPage/' . $performer->id . '" class="mt-1 fw-bold list-group-item list-group-item-action active custom-header-search-result-item">' . $performer->name . ' [Исполнитель]</a>';
        }

        foreach ($places as $place) {
            $output .= '<a href="/editPlacePage/' . $place->id . '" class="mt-1 fw-bold list-group-item list-group-item-action active custom-header-search-result-item">' . $place->name . ' [Развлекательный центр]</a>';
        }

        $output .= '</div></div>';

        return response()->json(['output' => $output]);
    }

    function filtAdminSessions(Request $request, Event $event) {
        $sessions = Session::where('event_id', $event->id)->get();
        if ($sessions->isNotEmpty()) {
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
        
        $performers = Performer::all();
        $selectedPerformers = $event->performers->pluck('id')->toArray();
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

        $selectedFilter = 0;
        $comments = Comment::where('event_id', $event->id)
            ->leftJoin('likes', 'comments.id', '=', 'likes.comment_id')
            ->select('comments.*')
            ->selectRaw('COUNT(likes.id) as like_count')
            ->groupBy('comments.id')
            ->orderByDesc('like_count')
            ->get();

        $commentsCount = $comments->count();
        $age_limits = AgeLimit::all();
        $subgenres = Subgenre::all();
        $places = Place::all();

        $requestDate = Carbon::createFromFormat('d.m.Y', $request->date)->format('Y-m-d');
        $firstSessions = $sessions->where('date', $sessions->min('date'));
        $secondSessions = $sessions->where('date', $requestDate);
        $anchor = '#filtAdminSessions';
        $lastDate = $sessions->max('date');
        return view('editEventPage', compact('event', 'age_limits', 'subgenres', 'places', 'commentsCount', 'comments', 'selectedFilter', 'jsReservedSeats', 'screenText', 'performers', 'selectedPerformers', 'isHaveSessions', 'firstSessions', 'secondSessions', 'dates', 'anchor', 'lastDate'));
    }

    function editEventPage(Event $event)
    {
        $sessions = Session::where('event_id', $event->id)->get();
        if ($sessions->isNotEmpty()) {
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
        
        $performers = Performer::all();
        $selectedPerformers = $event->performers->pluck('id')->toArray();
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

        $selectedFilter = 0;
        $comments = Comment::where('event_id', $event->id)
            ->leftJoin('likes', 'comments.id', '=', 'likes.comment_id')
            ->select('comments.*')
            ->selectRaw('COUNT(likes.id) as like_count')
            ->groupBy('comments.id')
            ->orderByDesc('like_count')
            ->get();

        $commentsCount = $comments->count();
        $age_limits = AgeLimit::all();
        $subgenres = Subgenre::all();
        $places = Place::all();

        $firstSessions = $sessions->where('date', $sessions->min('date'));
        $secondSessions = $sessions->where('date', $sessions->min('date'));
        $lastDate = $sessions->max('date');
        return view('editEventPage', compact('event', 'age_limits', 'subgenres', 'places', 'commentsCount', 'comments', 'selectedFilter', 'jsReservedSeats', 'screenText', 'performers', 'selectedPerformers', 'isHaveSessions', 'firstSessions', 'secondSessions', 'dates', 'lastDate'));
    }

    function editEvent(Request $request, Event $event)
    {
        if ($file = $request->file('image')) {
            $file_name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            Storage::putFileAs('public/images', $file, $file_name);

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'description' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,svg',
                'price' => 'required|max:100'
            ], [
                'name.required' => 'Название обязательно.',
                'name.max' => 'Максимальное количество символов в названии: 100.',
                'description.required' => 'Описание обязательно.',
                'image.required' => 'Изображение обязательно.',
                'image.image' => 'Изображение должно быть в следующих форматах: jpeg, png, jpg, svg.',
                'image.mimes' => 'Изображение должно быть в следующих форматах: jpeg, png, jpg, svg.',
                'price.required' => 'Минимальная цена обязательна.',
                'price.max' => 'Максимальное количество символов в цене: 100.',
            ]);
        } else {
            $file_name = $event->image;
            $request->merge(['image' => $file_name]);

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'description' => 'required',
                'price' => 'required|max:100'
            ], [
                'name.required' => 'Название обязательно.',
                'name.max' => 'Максимальное количество символов в названии: 100.',
                'description.required' => 'Описание обязательно.',
                'price.required' => 'Минимальная цена обязательна.',
                'price.max' => 'Максимальное количество символов в цене: 100.',
            ]);
        }

        if ($validator->fails()) return redirect()->back()->withErrors($validator);

        $performers = $request->performers;

        $event->performers()->sync($performers);

        $completed = $event->completed;
        if ($request->unlock_places) {
            $sessions = Session::where('event_id', $event->id)->get();
            if ($sessions->isNotEmpty()) {
                foreach ($sessions as $session) {
                    $dbScheme = Scheme::where('id', $session->scheme_id)->first();
                    $json = file_get_contents(public_path('storage/seats.json'));
                    $dbScheme->update([
                        'seats' => $json
                    ]);
                }
            } else {
                $dbSchemeId = $event->scheme_id;
                $dbScheme = Scheme::where('id', $dbSchemeId)->first();
                $json = file_get_contents(public_path('storage/seats.json'));
                $dbScheme->update([
                    'seats' => $json
                ]);
            }
            $completed = 0;
        }

        // $sessions = Session::where('event_id', $event->id)->get();
        // if ($sessions->isNotEmpty()) {
        //     // $oldTimes = [];
        //     // foreach($sessions as $session) {
        //     //     $oldTimes[] = $session->time;
        //     // }

        //     // $input = $request->all();
        //     // $currentTimes = [];
        //     // foreach ($input as $key => $value) {
        //     //     if (preg_match('/^time\d+$/', $key)) {
        //     //         $currentTimes[] = $value;
        //     //     }
        //     // }

        //     // $timesForUpdate = $currentTimes;

        //     // foreach ($sessions as $index => $session) {
        //     //     if (isset($timesForUpdate[$index])) {
        //     //         $session->update(['time' => $currentTimes[$index]]);
        //     //         unset($currentTimes[$index]);
        //     //     }
        //     // }

        //     // $addedTimes = array_diff($currentTimes, $oldTimes);
        //     // $json = file_get_contents(public_path('storage/seats.json'));
            
        //     // foreach($addedTimes as $addedTime) {
        //     //     $currentScheme = Scheme::create([
        //     //         'seats' => $json
        //     //     ]);

        //     //     Session::create([
        //     //         'event_id' => $event->id,
        //     //         'scheme_id' => $currentScheme->id,
        //     //         'time' => $addedTime
        //     //     ]);
        //     // }

        //     $update = $event->update([
        //         'name' => $request->name,
        //         'description' => $request->description,
        //         'image' => $file_name,
        //         'date' => $request->date,
        //         'price' => $request->price,
        //         'age_limit_id' => $request->age_limit_id,
        //         'subgenre_id' => $request->subgenre_id,
        //         'place_id' => $request->place_id,
        //         'completed' => $completed
        //     ]);
        // }else{
        //     $update = $event->update([
        //         'name' => $request->name,
        //         'description' => $request->description,
        //         'image' => $file_name,
        //         'date' => $request->date,
        //         'time' => $request->time,
        //         'price' => $request->price,
        //         'age_limit_id' => $request->age_limit_id,
        //         'subgenre_id' => $request->subgenre_id,
        //         'place_id' => $request->place_id,
        //         'completed' => $completed
        //     ]);
        // }

        $sessions = Session::where('event_id', $event->id)->get();
        if ($sessions->isNotEmpty()) {
            $update = $event->update([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $file_name,
                // 'date' => $request->date,
                // 'time' => $request->time,
                'price' => $request->price,
                'age_limit_id' => $request->age_limit_id,
                'subgenre_id' => $request->subgenre_id,
                'place_id' => $request->place_id,
                'completed' => $completed
            ]);
        }else{
            $update = $event->update([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $file_name,
                'date' => $request->date,
                'time' => $request->time,
                'price' => $request->price,
                'age_limit_id' => $request->age_limit_id,
                'subgenre_id' => $request->subgenre_id,
                'place_id' => $request->place_id,
                'completed' => $completed
            ]);
        }

        if ($update) return redirect('/editEventPage/' . $event->id)->with('messageModal', ['title' => 'Успех', 'message' => "Успешное редактирование события.", 'scrollToElement' => "#"]);
        else return redirect('/editEventPage/' . $event->id)->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#"]);
    }


    function createEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,svg',
            'price' => 'required|max:100'
        ], [
            'name.required' => 'Название обязательно.',
            'name.max' => 'Максимальное количество символов в названии: 100.',
            'description.required' => 'Описание обязательно.',
            'image.required' => 'Изображение обязательно.',
            'image.image' => 'Изображение должно быть в следующих форматах: jpeg, png, jpg, svg.',
            'image.mimes' => 'Изображение должно быть в следующих форматах: jpeg, png, jpg, svg.',
            'price.required' => 'Минимальная цена обязательна.',
            'price.max' => 'Максимальное количество символов в цене: 100.',
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator);

        if ($file = $request->file('image')) {
            $file_name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            Storage::putFileAs('public/images', $file, $file_name);
        }

        $json = file_get_contents(public_path('storage/seats.json'));

        $scheme = Scheme::create([
            'seats' => $json
        ]);

        $input = $request->all();
        $times = [];

        foreach ($input as $key => $value) {
            if (preg_match('/^time\d+$/', $key)) {
                $times[] = $value;
            }
        }

        if ($request->unlock_sessions && !empty($times)) {
            $event = Event::create([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $file_name,
                'date' => $request->date,
                'time' => $times[0],
                'price' => $request->price,
                'age_limit_id' => $request->age_limit_id,
                'scheme_id' => $scheme->id,
                'subgenre_id' => $request->subgenre_id,
                'place_id' => $request->place_id,
                'completed' => 0
            ]);

            $performers = $request->performers;

            $event->performers()->sync($performers);

            $startDate = Carbon::parse($request->date);
            $endDate = Carbon::parse($request->date2);

            $dates = [];
            while ($startDate->lte($endDate)) {
                $dates[] = $startDate->format('d.m.Y');
                $startDate->addDay();
            }

            foreach ($dates as $key => $date) {
                $convertedDate = Carbon::createFromFormat('d.m.Y', $date)->format('Y-m-d');
                $dates[$key] = $convertedDate;
            }

            Session::create([
                'event_id' => $event->id,
                'scheme_id' => $scheme->id,
                'time' => $times[0],
                'date' => $dates[0],
            ]);

            $firstTime = $times[0];

            array_shift($times);

            if (!empty($times) && !empty($dates)) {
                foreach ($dates as $index => $date) {
                    foreach ($times as $time) {
                        $scheme = Scheme::create([
                            'seats' => $json
                        ]);
    
                        $session = Session::create([
                            'event_id' => $event->id,
                            'scheme_id' => $scheme->id,
                            'time' => $time,
                            'date' => $date
                        ]);
                    }
                    if($index == 0) {
                        array_unshift($times, $firstTime);
                    }
                }
            }
        } else {
            $event = Event::create([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $file_name,
                'date' => $request->date,
                'time' => $request->time,
                'price' => $request->price,
                'age_limit_id' => $request->age_limit_id,
                'scheme_id' => $scheme->id,
                'subgenre_id' => $request->subgenre_id,
                'place_id' => $request->place_id,
                'completed' => 0
            ]);

            $performers = $request->performers;

            $event->performers()->sync($performers);
        }

        if ($event) return redirect('admin')->with('messageModal', ['title' => 'Успех', 'message' => "Успешное создание события.", 'scrollToElement' => "#event_" . $event->id]);
        else return redirect('admin')->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#"]);
    }

    function deleteEvent(Event $event)
    {
        $sessions = Session::where('event_id', $event->id)->get();
        if ($sessions->isNotEmpty()) {
            foreach ($sessions as $session) {
                $scheme = Scheme::where('id', $session->scheme_id)->first();
                $scheme->delete();
            }
        } else {
            $scheme = Scheme::where('id', $event->scheme_id)->first();
            $scheme->delete();
        }
        $delete = $event->delete();
        if ($delete) return redirect('/admin')->with('messageModal', ['title' => 'Успех', 'message' => "Успешное удаление события.", 'scrollToElement' => "#"]);
        else return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#"]);
    }

    function cancelEvent(Event $event)
    {
        $delete = Ticket::where('event_id', $event->id)->where('completed', 0)->delete();
        $sessions = Session::where('event_id', $event->id)->get();
        if ($sessions->isNotEmpty()) {
            foreach ($sessions as $session) {
                $dbScheme = Scheme::where('id', $session->scheme_id)->first();
                $jsonScheme = json_decode($dbScheme->seats, true);

                $filteredScheme = array_filter($jsonScheme, function ($seat) {
                    return $seat['reserved'] == true;
                });

                $usersPrices = [];

                foreach ($filteredScheme as $seat) {
                    $price = $this->getPrice($seat['row'], $seat['seat'], $event);
                    $usersPrices[] = ['user_id' => $seat['user_id'], 'price' => $price];
                }

                foreach ($usersPrices as $userPrice) {
                    $user = User::where('id', $userPrice['user_id'])->first();
                    $wallet = Wallet::where('id', $user->wallet_id)->first();
                    $wallet->update([
                        'balance' => $wallet->balance + $userPrice['price']
                    ]);
                }

                $json = file_get_contents(public_path('storage/seats.json'));

                $dbScheme->update([
                    'seats' => $json
                ]);

                $update = $event->update([
                    'completed' => 1
                ]);
            }
        } else {
            $dbSchemeId = $event->scheme_id;
            $dbScheme = Scheme::where('id', $dbSchemeId)->first();
            $jsonScheme = json_decode($dbScheme->seats, true);

            $filteredScheme = array_filter($jsonScheme, function ($seat) {
                return $seat['reserved'] == true;
            });

            $usersPrices = [];

            foreach ($filteredScheme as $seat) {
                $price = $this->getPrice($seat['row'], $seat['seat'], $event);
                $usersPrices[] = ['user_id' => $seat['user_id'], 'price' => $price];
            }

            foreach ($usersPrices as $userPrice) {
                $user = User::where('id', $userPrice['user_id'])->first();
                $wallet = Wallet::where('id', $user->wallet_id)->first();
                $wallet->update([
                    'balance' => $wallet->balance + $userPrice['price']
                ]);
            }

            $json = file_get_contents(public_path('storage/seats.json'));

            $dbScheme->update([
                'seats' => $json
            ]);

            $update = $event->update([
                'completed' => 1
            ]);
        }
        if ($update) return redirect('/editEventPage/' . $event->id)->with('messageModal', ['title' => 'Успех', 'message' => "Успешная отмена события.", 'scrollToElement' => "#"]);
        else return redirect('/editEventPage/' . $event->id)->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#"]);
    }

    function getPrice($row, $seat, $event)
    {
        $price = $event->price;
        if ($row <= 5) {
            return $price;
        } else if ($row == 7 && $seat >= 5 && $seat <= 12) {
            return $price + 250;
        } else if ($row == 8 && $seat >= 4 && $seat <= 11) {
            return $price + 250;
        } else if ($row == 9 && $seat >= 3 && $seat <= 10) {
            return $price + 250;
        } else if ($row == 10 && $seat >= 2 && $seat <= 9) {
            return $price + 250;
        } else {
            return $price + 500;
        }
    }

    function filtAdminComments(Request $request, Event $event)
    {
        $sessions = Session::where('event_id', $event->id)->get();
        if ($sessions->isNotEmpty()) {
            $isHaveSessions = true;
        } else {
            $isHaveSessions = false;
        }
        $performers = Performer::all();
        $selectedPerformers = $event->performers->pluck('id')->toArray();
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

        $comments = $comments->where('event_id', $event->id)
            ->leftJoin('likes', 'comments.id', '=', 'likes.comment_id')
            ->select('comments.*')
            ->selectRaw('COUNT(likes.id) as like_count')
            ->groupBy('comments.id')
            ->orderByDesc('like_count')
            ->get();

        $commentsCount = $comments->count();
        $age_limits = AgeLimit::all();
        $subgenres = Subgenre::all();
        $places = Place::all();
        $anchor = '#filtAdminComments';
        return view('editEventPage', compact('event', 'age_limits', 'subgenres', 'places', 'commentsCount', 'comments', 'selectedFilter', 'anchor', 'jsReservedSeats', 'screenText', 'performers', 'selectedPerformers', 'isHaveSessions', 'sessions'));
    }

    function adminPerformers()
    {
        $performers = Performer::all();
        return view('adminPerformers', compact('performers'));
    }

    function createPerformer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,svg',
        ], [
            'name.required' => 'Название обязательно.',
            'name.max' => 'Максимальное количество символов в названии: 100.',
            'description.required' => 'Описание обязательно.',
            'image.required' => 'Изображение обязательно.',
            'image.image' => 'Изображение должно быть в следующих форматах: jpeg, png, jpg, svg.',
            'image.mimes' => 'Изображение должно быть в следующих форматах: jpeg, png, jpg, svg.',
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator);

        if ($file = $request->file('image')) {
            $file_name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            Storage::putFileAs('public/images', $file, $file_name);
        }

        $performer = Performer::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $file_name,
        ]);

        if ($performer) return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Успешное создание исполнителя.", 'scrollToElement' => "#performer_" . $performer->id]);
        else return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#"]);
    }

    function editPerformerPage(Performer $performer)
    {
        return view('editPerformerPage', compact('performer'));
    }

    function editPerformer(Request $request, Performer $performer)
    {
        if ($file = $request->file('image')) {
            $file_name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            Storage::putFileAs('public/images', $file, $file_name);

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'description' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,svg',
            ], [
                'name.required' => 'Название обязательно.',
                'name.max' => 'Максимальное количество символов в названии: 100.',
                'description.required' => 'Описание обязательно.',
                'image.required' => 'Изображение обязательно.',
                'image.image' => 'Изображение должно быть в следующих форматах: jpeg, png, jpg, svg.',
                'image.mimes' => 'Изображение должно быть в следующих форматах: jpeg, png, jpg, svg.',
            ]);
        } else {
            $file_name = $performer->image;
            $request->merge(['image' => $file_name]);

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'description' => 'required',
            ], [
                'name.required' => 'Название обязательно.',
                'name.max' => 'Максимальное количество символов в названии: 100.',
                'description.required' => 'Описание обязательно.',
            ]);
        }

        if ($validator->fails()) return redirect()->back()->withErrors($validator);

        $update = $performer->update([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $file_name,
        ]);

        if ($update) return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Успешное редактирование исполнителя.", 'scrollToElement' => "#"]);
        else return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#"]);
    }

    function deletePerformer(Performer $performer)
    {
        $delete = $performer->delete();
        if ($delete) return redirect('/adminPerformers')->with('messageModal', ['title' => 'Успех', 'message' => "Успешное удаление исполнителя.", 'scrollToElement' => "#"]);
        else return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#"]);
    }

    function adminPlaces()
    {
        $places = Place::all();
        return view('adminPlaces', compact('places'));
    }

    function createPlace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'address' => 'required|max:100',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,svg',
        ], [
            'name.required' => 'Название обязательно.',
            'name.max' => 'Максимальное количество символов в названии: 100.',
            'address.required' => 'Адрес обязателен.',
            'address.max' => 'Максимальное количество символов в адресе: 100.',
            'description.required' => 'Описание обязательно.',
            'image.required' => 'Изображение обязательно.',
            'image.image' => 'Изображение должно быть в следующих форматах: jpeg, png, jpg, svg.',
            'image.mimes' => 'Изображение должно быть в следующих форматах: jpeg, png, jpg, svg.',
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator);

        if ($file = $request->file('image')) {
            $file_name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            Storage::putFileAs('public/images', $file, $file_name);
        }

        $place = Place::create([
            'name' => $request->name,
            'address' => $request->address,
            'description' => $request->description,
            'image' => $file_name,
        ]);

        if ($place) return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Успешное создание развлекательного центра.", 'scrollToElement' => "#place_" . $place->id]);
        else return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#"]);
    }

    function editPlacePage(Place $place)
    {
        if ($place->id == 3) {
            $screenText = "ЭКРАН";
        } else {
            $screenText = "СЦЕНА";
        }
        return view('editPlacePage', compact('place', 'screenText'));
    }

    function editPlace(Request $request, Place $place)
    {
        if ($file = $request->file('image')) {
            $file_name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            Storage::putFileAs('public/images', $file, $file_name);

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'address' => 'required|max:100',
                'description' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,svg',
            ], [
                'name.required' => 'Название обязательно.',
                'name.max' => 'Максимальное количество символов в названии: 100.',
                'address.required' => 'Адрес обязателен.',
                'address.max' => 'Максимальное количество символов в адресе: 100.',
                'description.required' => 'Описание обязательно.',
                'image.required' => 'Изображение обязательно.',
                'image.image' => 'Изображение должно быть в следующих форматах: jpeg, png, jpg, svg.',
                'image.mimes' => 'Изображение должно быть в следующих форматах: jpeg, png, jpg, svg.',
            ]);
        } else {
            $file_name = $place->image;
            $request->merge(['image' => $file_name]);

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100',
                'address' => 'required|max:100',
                'description' => 'required',
            ], [
                'name.required' => 'Название обязательно.',
                'name.max' => 'Максимальное количество символов в названии: 100.',
                'address.required' => 'Адрес обязателен.',
                'address.max' => 'Максимальное количество символов в адресе: 100.',
                'description.required' => 'Описание обязательно.',
            ]);
        }

        if ($validator->fails()) return redirect()->back()->withErrors($validator);

        $update = $place->update([
            'name' => $request->name,
            'address' => $request->address,
            'description' => $request->description,
            'image' => $file_name,
        ]);

        if ($update) return redirect()->back()->with('messageModal', ['title' => 'Успех', 'message' => "Успешное редактирование развлекательного центра.", 'scrollToElement' => "#"]);
        else return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#"]);
    }

    function deletePlace(Place $place)
    {
        $delete = $place->delete();
        if ($delete) return redirect('/adminPlaces')->with('messageModal', ['title' => 'Успех', 'message' => "Успешное удаление развлекательного центра.", 'scrollToElement' => "#"]);
        else return redirect()->back()->with('messageModal', ['title' => 'Ошибка', 'message' => 'Что-то пошло не так.', 'scrollToElement' => "#"]);
    }
}
