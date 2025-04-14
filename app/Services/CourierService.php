<?php

namespace App\Services;

use App\Models\Courier;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Jobs\IncreaseViewsJob;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CourierService {

    private $sortByFields = [
        'title',
        'created_at',
    ];

    private $sortDirections = [
        'asc',
        'desc',
    ];

    public function listCouriers(Request $request)
    {
        $search = $request->query('search');
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'asc');
        $page = $request->query('page', 1);

        $cacheMinutes = env('COURIER_LIST_CACHE_MINUTES', 10);
        $perPage = env('COURIER_LIST_PER_PAGE', 10);

        $cacheKey = "couriers:search={$search}:sort={$sort}:direction={$direction}:page={$page}:perPage={$perPage}";

        return Cache::remember($cacheKey, $cacheMinutes, function () use ($search, $sort, $direction, $perPage) {
            $query = Courier::query();

            $query->withCount([
                'votes as likes' => function ($q) {
                    $q->where('type', 'like');
                },
                'votes as dislikes' => function ($q) {
                    $q->where('type', 'dislike');
                },
            ]);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            if (!in_array($sort, $this->sortByFields)) {
                $sort = 'created_at';
            }

            if (!in_array($direction, $this->sortDirections)) {
                $direction = 'asc';
            }

            $query->orderBy($sort, $direction);

            return $query->paginate($perPage);
        });
    }

    public function vote($userId, $courierId, $type) {
        return DB::transaction(function () use ($userId, $courierId, $type) {
            return Vote::updateOrCreate([
                'user_id' => $userId,
                'courier_id' => $courierId,
            ], [
                'type' => $type,
            ]);
        });
    }

    public function removeVote($userId, $courierId) {
        return Vote::where('user_id', $userId)->where('courier_id', $courierId)->delete();
    }

    public function getCourierDetails($courier) {
        $cacheMinutes = env('COURIER_LIST_CACHE_MINUTES', 10);

        return Cache::remember("Courier:{$courier->id}", $cacheMinutes, function () use ($courier) {
            return $courier->loadCount([
                'votes as likes' => function ($q) {
                    return $q->where('type', 'like');
                },
                'votes as dislikes' => function ($q) {
                    return $q->where('type', 'dislike');
                }
            ]);
        });
    }

    public function increaseViews($courier) {
        dispatch(new IncreaseViewsJob($courier->id));
    }
}
