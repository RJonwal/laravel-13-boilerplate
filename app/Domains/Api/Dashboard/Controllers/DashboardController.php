<?php

namespace App\Domains\Api\Dashboard\Controllers;

use App\Http\Controllers\APIController;
use Illuminate\Http\Request;
use App\Domains\Core\Event\Models\Event;
use App\Domains\Core\Event\Models\EventMember;
use App\Domains\Core\User\Models\HostMembers;
use Throwable;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Domains\Core\Event\Services\EventService;

class DashboardController extends APIController
{

    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function dashboard()
    {
        $authUser = request()->user('sanctum');
        $role = $authUser->roles()->first();
        if (!$role) {
            return $this->apiSuccess([
                'role'   => 'No Role',
                'data'   => [],
            ]);
        }
        switch ($role->name) {
            case 'Host':
                $data = $this->getHostDashboardData($authUser);
                break;

            case 'Member':
                $data = $this->getMemberDashboardData($authUser);
                break;

            default:
                return $this->apiError([
                    'message' => trans('messages.unautherized_role'),
                    'unautherized_role',
                    [],
                ], 403);
        }


        return $this->apiSuccess($data, 'Dashboard Data');
    }


    private function getHostDashboardData($user)
    {
        try {
            $authUser = $user;

            if (!$authUser->is_host) {
                return $this->apiError(
                    trans('messages.only_host_access'),
                    'only_host_access',
                    [],
                    403
                );
            }

            //1) Active Events
            $reqActive = request()->duplicate();
            $reqActive->query->set('status', 'active');

            $activeEvents = $this->eventService->getEventsList($reqActive, 'all');
            $activeEventCount = $activeEvents->count();


            // 2) UPCOMING EVENTS (via EventService)
            $reqUpcoming = request()->duplicate();
            $reqUpcoming->query->set('status', 'upcoming');
            $reqUpcoming->query->set('host_id', $authUser->id);

            $upcomingEventsFull = $this->eventService->getEventsList($reqUpcoming, 'all');
            $upcomingEvents = $upcomingEventsFull->take(5);

            // 3) Convert UUID → DB ID for EventMember queries
            $eventIds = $upcomingEventsFull->pluck('db_id')->toArray();

            $invitesSentCount = EventMember::whereIn('event_id', $eventIds)->count();
            $rsvpsAcceptedCount = EventMember::whereIn('event_id', $eventIds)
                ->where('status', 'accepted')
                ->count();

            // 4) EVENT CONTENTS (via EventService)
            $eventContents = $this->eventService->getEventContents(
                null,   // eventId
                false,  // paginated
                'all',  // section
                5       // limit
            );

            $requestedMembersCount = EventMember::whereIn('event_id', $eventIds)
                                    ->whereColumn('invited_by', 'member_id')
                                    ->where('status', 'pending')
                                    ->count();

            // 5) FINAL RESPONSE
            return [
                'active_event_count'   => $activeEventCount,
                'invites_sent_count'   => $invitesSentCount,
                'rsvps_accepted_count' => $rsvpsAcceptedCount,
                'requested_members_count' => $requestedMembersCount,
                'upcoming_events'      => $upcomingEvents,
                'event_contents'       => $eventContents,
            ];
        } catch (\Throwable $th) {
            return $this->apiError(trans('messages.error_message'));
        }
    }


    // private function getMemberDashboardData($user, Request $request = null)
    // {
    //     try {
    //         $authUser = $user;
    //         $request = $request ?? request();

    //         if (!$authUser->is_member) {
    //             return $this->apiError(trans('messages.only_member_access'), 'only_member_access', [], 403);
    //         }

    //         $countsQuery = Event::with(['category', 'createdBy', 'invitedMembers'])
    //             ->whereHas('createdBy', function ($q) {
    //                 $q->where('is_paused', 0)
    //                     ->where('status', 'active')
    //                     ->where('approval_status', 1);
    //             });

    //         // VIP → premium hosts only
    //         if ($authUser->member_tag === 'vip') {
    //             $countsQuery->whereHas('createdBy.subscriptions.plan', function ($q) {
    //                 $q->where('subscription_plans.plan_type', 'premium')
    //                     ->where('user_subscriptions.status', 'active')
    //                     ->where('user_subscriptions.end_date', '>=', now());
    //             });
    //         }

    //         // Member should see only events they are invited to (not denied)
    //         $countsQuery->whereHas('invitedMembers', function ($q) use ($authUser) {
    //             $q->where(function ($sub) use ($authUser) {
    //                 $sub->where('member_id', $authUser->id)
    //                     ->orWhere('invited_by', $authUser->id);
    //             })->where('status', '!=', 'denied');
    //         });

    //         // Active (upcoming)
    //         $activeEventCount = (clone $countsQuery)
    //             ->where('event_date_time', '>=', now())
    //             ->whereHas('invitedMembers', function ($q) use ($authUser) {
    //                 $q->where('invited_by', $authUser->id)
    //                     ->where('member_id', '!=', $authUser->id);
    //             })
    //             ->count();


    //         // ---------- FIXED UPCOMING EVENTS ----------
    //         $upcomingRequest = clone $request;
    //         $upcomingRequest->query->set('status', 'upcoming');

    //         $upcomingEvents = $this->eventService
    //             ->getEventsList($upcomingRequest, 'member_upcoming')
    //             ->take(5);

    //         // Invites Sent Count
    //         // $invitesSentCount = EventMember::with('invitedBy')->where('member_id', $authUser->id)
    //         //     ->where('invited_by', $authUser->id)
    //         //     ->where('status', 'accepted')
    //         //     ->count();
    //         $invitesSentCount = EventMember::where('invited_by', $authUser->id)
    //             ->where('member_id', '!=', $authUser->id)
    //             ->where('status', 'accepted')
    //             ->whereHas('invitedBy', function ($q) {
    //                 $q->where('is_paused', 0)
    //                     ->where('status', 'active')
    //                     ->where('approval_status', 1);
    //             })
    //             ->groupBy('event_id')
    //             ->get()
    //             ->count();

    //         // Explore contents remain unchanged (if correct for your logic)
    //         $upcomingEventContents = $this->eventService->getEventContents(null, false, 'explore', 5);

    //         return [
    //             'active_event_count' => $activeEventCount,
    //             'invites_sent_count' => $invitesSentCount,
    //             'upcoming_events' => $upcomingEvents,
    //             'explore_event_contents' => $upcomingEventContents,
    //         ];
    //     } catch (\Throwable $th) {
    //         return $this->apiError(trans('messages.error_message'));
    //     }
    // }

    private function getMemberDashboardData($user, Request $request = null)
    {
        try {
            $authUser = $user;
            $request = $request ?? request();

            if (!$authUser->is_member) {
                return $this->apiError(
                    trans('messages.only_member_access'),
                    'only_member_access',
                    [],
                    403
                );
            }

            // UPCOMING EVENTS COUNT (via EventService)   
            $upcomingReq = clone $request;
            $upcomingReq->query->set('status', 'upcoming');

            $upcomingEventsList = $this->eventService->getEventsList($upcomingReq, 'all');

            // Count distinct event IDs
            $upcomingEventCount = $upcomingEventsList
                ->pluck('id')
                ->unique()
                ->count();

            /*
            |--------------------------------------------------------------------------
            | INVITES SENT COUNT  (via EventService using filter)
            |--------------------------------------------------------------------------
            */
                // $sentReq = clone $request;
                // $sentReq->query->set('filter', 'invites_sent'); 

                // $invitesSentList = $this->eventService->getEventsList($sentReq, 'all');

                // $invitesSentCount = $invitesSentList
                //     ->pluck('id')
                //     ->unique()
                //     ->count();


            // ACTIVE EVENTS COUNT (via EventService)
            $activeReq = clone $request;
            $activeReq->query->set('status', 'active');

            $activeEventsList = $this->eventService->getEventsList($activeReq, 'all');

            $activeEventCount = $activeEventsList
                ->pluck('id')
                ->unique()
                ->count();


            // UPCOMING EVENTS (Only 5 items)
            $upcomingEvents = $upcomingEventsList->take(5);

            // EXPLORE CONTENTS
            $exploreContents = $this->eventService->getEventContents(
                null,      // eventId
                false,     // isPaginated
                'explore', // section
                5          // limit
            );

            // $exploreEvents = $this->eventService->getEventsList($request, 'explore',5);

            $exploreReq = clone $request;
            $exploreReq->query->set('location', $authUser->event_location_id);

            $exploreEvents = $this->eventService
                ->getEventsList($exploreReq, 'explore', 5);



            return [
                'upcoming_event_count'     => $upcomingEventCount,
                'active_event_count'      => $activeEventCount,
                'upcoming_events'         => $upcomingEvents,
                // 'explore_event_contents'  => $exploreContents,
                'explore_events'          => $exploreEvents,
            ];
        } catch (\Throwable $th) {
            return $this->apiError(trans('messages.error_message'));
        }
    }

    public function getRSPVSMembers(Request $request)
    {
        try {
            $authUser = $request->user();

            if (!$authUser->is_host) {
                return $this->apiError(
                    trans('messages.only_host_access'),
                    'only_host_access',
                    [],
                    403
                );
            }

            $eventId = $request->input('event_id');

            // CASE 1: Single Event   
            if ($eventId) {

                $event = Event::where('id', $eventId)
                    ->where('host_id', $authUser->id)
                    ->first();

                if (!$event) {
                    return $this->apiError(
                        trans('messages.unauthorized_event_access'),
                        'unauthorized',
                        [],
                        403
                    );
                }

                $eventMembers = EventMember::with('member.featuredProfileImage')
                    ->where('event_id', $eventId)
                    ->where('status', 'accepted')
                    ->get();
            }

            // CASE 2: All Upcoming Events
            else {

                $reqUpcoming = request()->duplicate();
                $reqUpcoming->query->set('status', 'upcoming');
                $reqUpcoming->query->set('host_id', $authUser->id);

                $hostEvents = $this->eventService->getEventsList($reqUpcoming, 'all');
                $eventIds   = $hostEvents->pluck('db_id')->toArray();

                if (empty($eventIds)) {
                    return $this->apiSuccess([
                        'event_id' => 'all',
                        'count'    => 0,
                        'members'  => [],
                    ]);
                }

                $eventMembers = EventMember::with('member.featuredProfileImage')
                    ->whereIn('event_id', $eventIds)
                    ->where('status', 'accepted')
                    ->get()
                    ->unique('member_id'); //  REMOVE DUPLICATES
            }

            /*
            |--------------------------------------------------------------------------
            | FORMAT RESPONSE
            |--------------------------------------------------------------------------
            */
            $membersData = $eventMembers->map(function ($eventMember) {
                $member = $eventMember->member;

                return [
                    'id'            => $member->id,
                    'uuid'          => $member->uuid,
                    'name'          => $member->name,
                    'member_tag'    => $member->member_tag,
                    'profile_image' => $member->featuredProfileImage?->file_url,
                ];
            })->values();

            return $this->apiSuccess([
                'event_id' => $eventId ?? 'all',
                'count'    => $membersData->count(),
                'members'  => $membersData,
            ]);

        } catch (\Throwable $th) {
            return $this->apiError(trans('messages.error_message'));
        }
    }




    // public function generate(Request $request)
    // {
    //     $event = Event::find($request->event_id);

    //     if (!$event) {
    //         return $this->apiError(trans('messages.invalid_qr_code'), 'invalid_qr_code', [], 404);
    //     }

    //     if (!$event->uuid) {
    //         $event->uuid = Str::uuid();
    //         $event->save();
    //     }

    //     $qr =
    //         // QrCode::format('png')
    //         QrCode::format('svg')
    //         ->size(300)
    //         ->margin(1)
    //         ->generate($event->uuid);

    //     $base64 = base64_encode($qr);

    //     return response()->json([
    //         'event_id' => $event->id,
    //         'uuid' => $event->uuid,
    //         'qr_code' => 'data:image/png;base64,' . $base64,
    //     ]);
    // }

    public function verifyQRcode(Request $request)
    {
        try {
            $authUser = $request->user();

            $uuid = $request->input('event_member_uuid');
            $qr_id = $request->input('qr_id');

            $eventMember = EventMember::with(['event', 'member'])
                ->where('uuid', $uuid)
                ->where('qr_id', $qr_id)
                ->first();

            
            if (!$eventMember) {
                return $this->apiError(trans('messages.invalid_qr_code'), 'invalid_qr_code', [], 404);
            }    

            // Who created the event, this user can scan the QR.    
            if ($eventMember->event->created_by !== $authUser->id) {
                return $this->apiError(trans('api.unauthorized_qr_scan'),'unauthorized_qr_scan', [], 403);
            }

            $eventDate = optional($eventMember->event->event_date_time);
            if (!$eventDate) {
                return $this->apiError(trans('api.event_date_missing'), 'event_date_missing', [], 404);
            }

            $eventStart = Carbon::parse($eventMember->event->event_date_time);
            $joinAllowedAt = $eventStart->copy()->subMinutes(10);
            if (now()->lt($joinAllowedAt)) {
                return $this->apiError(trans('api.event_coming_soon'), 'event_coming_soon', [], 404);
            }

            if ($eventMember->event->event_end_date_time < now()) {
                return $this->apiError(trans('api.event_already_passed'), 'event_already_passed', [], 404);
            }

            if ($eventMember->is_event_attended) {
                return $this->apiError(trans('api.already_checked_in'), 'already_checked_in', [], 400);
            }

            $isFavourite = HostMembers::where('host_id', $authUser->id)
                ->where('member_id', $eventMember->member_id)
                ->where('is_favorite', 1)
                ->exists();

            // $eventMember->update(['is_event_attended' => 1]);
            // $eventMember->update(['attended_at' => now()]);

            $eventMember->update([
                'is_event_attended' => 1,
                'attended_at'       => now(),
            ]);

            return $this->apiSuccess([
                'member' => [
                    'name'        => $eventMember->member->name ?? null,
                    'member_tag'  => $eventMember->member->member_tag ?? null,
                    'is_favorite' => $isFavourite ? 1 : 0,
                    'profile_image' => $eventMember->member->profile_image_url ?? "",
                ],
                'event' => [
                    'title' => $eventMember->event->name ?? null,
                    'date' => $eventMember->event->event_date_time?->format(config('constant.date_format.date_time')) ?? null,
                    'venue' => $eventMember->event->eventLocation?->name ?? null,
                ],
            ]);
        } catch (\Throwable $th) {
            return $this->apiError(trans('messages.error_message'));
        }
    }
}
