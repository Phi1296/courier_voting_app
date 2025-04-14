<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courier;
use App\Services\CourierService;
use App\Models\Vote;

class CouriersController extends Controller
{
    public function __construct(protected CourierService $service) {}

    public function index(Request $request) {
        $couriers = $this->service->listCouriers($request);

        return response()->json($couriers)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function show(Courier $courier) {
        $this->service->increaseViews($courier);

        $courierDetails = $this->service->getCourierDetails($courier);

        return response()->json($courierDetails)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function vote(Request $request, $courierId) {
        $request->validate(['type' => 'required|in:like,dislike']);

        $existingVote = Vote::where('user_id', auth()->id())
            ->where([
                'courier_id' => $courierId,
                'type' => $request->type
            ])
            ->first();

        if ($existingVote) {
            return response()->json([
                'message' => 'You have already voted for this courier.',
            ], 409);
        }

        $vote = $this->service->vote(auth()->id(), $courierId, $request->type);

        return response()->json([
            'message' => 'Vote recorded',
            'vote' => $vote
        ], 200)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function unvote($courierId) {
        $this->service->removeVote(auth()->id(), $courierId);

        return response()->json(['message' => 'Vote removed'])
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}
