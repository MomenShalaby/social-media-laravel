<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use App\Models\Connections;


class ConnectionController extends Controller
{
    use HttpResponses;


    /**
     * Display a listing of the user friends.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $receiverId = $request->user()->id;

        $connections = Connections::where('receiver_id', $receiverId)
            ->where('status', 'accepted')
            ->orWhere('sender_id', $receiverId)
            ->where('status', 'accepted')
            ->get();

        return $this->success($connections, 'connections');

    }



    /**
     * Display a listing of pending requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pendingRequests(Request $request)
    {

        $receiverId = $request->user()->id;

        $connections = Connections::where('receiver_id', $receiverId)
            ->where('status', 'pending')
            ->orWhere('sender_id', $receiverId)
            ->where('status', 'pending')
            ->get();

        return $this->success($connections, 'connections');

    }

    /**
     * Send a connection request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendRequest(Request $request)
    {
        $sender_id = $request->user()->id;

        $validatedData = $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        if ($sender_id == $request->receiver_id) {
            return $this->error('You cant send a friend request to yourself', 409);

        }
        $existingConnection = Connections

            ::where('sender_id', $sender_id)
            ->where('receiver_id', $request->receiver_id)

            ->orWhere('sender_id', $request->receiver_id)
            ->where('receiver_id', $sender_id)

            ->exists();

        if ($existingConnection) {
            return $this->error('You sent a request before', 409);
        }

        $friend_req = Connections::create([
            'sender_id' => $sender_id,
            'receiver_id' => $request->receiver_id,
            'status' => 'pending',
        ]);

        return $this->success($friend_req, 'request sent');

    }


    /**
     * Accept a connection request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $connection
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, $connection)
    {

        $receiver_id = $request->user()->id;
        $sender_id = $connection;
        $connection = Connections::where('sender_id', $sender_id)
            ->where('receiver_id', $receiver_id)
            ->update(['status' => 'accepted']);
        if (!$connection = 1) {

            return $this->error('Connection not found', 404);
        } else {

            return $this->success([], 'request accepted');
        }

    }

    /**
     * Reject a connection request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $connection
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, $connection)
    {
        $receiver_id = $request->user()->id;
        $sender_id = $connection;


        $existingConnection = Connections

            ::where('sender_id', $sender_id)
            ->where('receiver_id', $receiver_id)

            ->orWhere('sender_id', $receiver_id)
            ->where('receiver_id', $sender_id)

            ->exists();

        if (!$existingConnection) {
            return $this->error('You didnt send a request to this user', 409);
        }
        $deleted = Connections::where('sender_id', $sender_id)
            ->where('receiver_id', $receiver_id)
            ->delete();
        return $this->success($deleted, 'request deleted', 204);

    }
}
