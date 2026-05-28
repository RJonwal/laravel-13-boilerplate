<?php

namespace App\Domains\Api\Dashboard\Controllers;

use Illuminate\Http\Request;
use App\Domains\Core\User\Models\User;
use App\Domains\Core\User\Models\ScheduleNotification;
use App\Domains\Core\Event\Models\Event;
use App\Domains\Core\Event\Models\EventMember;
use App\Domains\Core\User\Models\HostMembers;

use Illuminate\Notifications\DatabaseNotification as Notification;
use App\Notifications\SendNotification;

use Carbon\Carbon;

use App\Http\Controllers\APIController;

class NotificationController extends APIController
{

    // public function notificationActivityList(Request $request)
    // {
    //     try {
    //         // $status = $request->query('status', null); // all, read, unread
    //         $user = $request->user();

    //         // // Base notifications query
    //         $notificationsQuery = $user->notifications()->orderBy('created_at', 'desc');

    //         // Apply filters
    //         if (!empty($status)) {
    //             if ($status === 'read') {
    //                 $notificationsQuery->whereNotNull('read_at');
    //             } elseif ($status === 'unread') {
    //                 $notificationsQuery->whereNull('read_at');
    //             }
    //         }

    //         // Counts
    //         $allCount = $user->notifications()->count();
    //         $readCount = $user->notifications()->whereNotNull('read_at')->count();
    //         $unreadCount = $user->notifications()->whereNull('read_at')->count();

    //         // Paginate (or use ->get() if you truly want *all* without pagination)
    //         $notificationsPaginated = $notificationsQuery
    //             ->simplePaginate(config('constant.api_paginations.notifications', 10));

    //         // Map notifications (include even blocked users)
    //         $mapped = $notificationsPaginated->through(function ($notification) {
    //             $senderId = $notification->data['sender_id'] ?? null;
    //             $sender = $senderId ? User::find($senderId) : null;

    //             $response = [
    //                 'id' => $notification->id,
    //                 'member_uuid' => $sender?->uuid ?? null,
    //                 'title' => $notification->data['title'] ?? '',
    //                 'message' => $notification->data['message'] ?? '',
    //                 'type' => $notification->data['type'] ?? null,
    //                 'event_member_id' => $notification->data['extra']['event_member_id'] ?? '',
    //                 'read_at' => $notification->read_at?->translatedFormat(config('constant.date_format.date_time')),
    //                 'created_at' => $notification->created_at->translatedFormat(config('constant.date_format.date_time')),
    //                 // 'profile_image' => $sender?->profile_image_url ?? '',
    //                 // 'profile_image' => $sender->is_member ? $sender->featuredProfileImage?->file_url ?? null : $sender->profileImage['file_url'] ?? null,
    //                    'profile_image' => $sender  ? (
    //                         $sender->is_member
    //                             ? $sender->featuredProfileImage?->file_url
    //                             : $sender->profileImage?->file_url
    //                     )
    //                     : null,
    //                 'member_tag' => $sender?->member_tag ?? '',
    //                 'user_name' => $notification->data['extra']['user'] ?? '',
    //                 'event_name' => $notification->data['extra']['event'] ?? '',
    //             ];

    //             // Add response flag for event_invite_request
    //             if (($notification->data['type'] ?? null) === 'event_invite_request' || ($notification->data['type'] ?? null) === 'member_event_invite') {
    //                 // $status = EventMember::where('uuid', $notification->data['extra']['event_member_id'] ?? 0)
    //                 //     ->value('status');
    //                 // $response['response_flag'] = in_array($status, ['accepted', 'denied']);

    //                 $eventMember = EventMember::where('uuid',$notification->data['extra']['event_member_id'] ?? null)->first();

    //                 $status = $eventMember?->status;

    //                 $response['response_flag'] = in_array($status, ['accepted', 'denied']);

    //                 $response['is_event_full'] = $eventMember?->event ? $eventMember->event->isEventFull() : false;

    //                 $response['event_details'] = $eventMember?->event ? [
    //                     'name' => $eventMember->event->name,
    //                     'event_date_time' => $eventMember->event->event_date_time->translatedFormat(config('constant.date_format.date_time')),
    //                     'event_end_date_time' => $eventMember->event->event_end_date_time->translatedFormat(config('constant.date_format.date_time')),
    //                     'location' => $eventMember->event->eventLocation?->name ?? '',

    //                 ] : null;
                    
    //             }

    //             return $response;
    //         });

    //         return $this->apiSuccess([
    //             'all_count' => $allCount,
    //             'unread_count' => $unreadCount,
    //             'read_count' => $readCount,
    //             'activity_notifications' => $mapped,
    //         ]);

    //         // $getRequestedMemberNotifiactions = $user->notifications()->where($notificationsQuery->data['type'] === 'event_invite_request')->count();
    //         // dd($getRequestedMemberNotifiactions);

    //     } catch (\Throwable $th) {
    //         // dd($th);
    //         return $this->apiError(trans('messages.error_message'));
    //     }
    // }

    public function notificationActivityList(Request $request)
    {
        try {
            // $status = $request->query('status', null); // all, read, unread
            $user = $request->user();

            // // Base notifications query
            $notificationsQuery = $user->notifications()->orderBy('created_at', 'desc');

            // Apply filters
            if (!empty($status)) {
                if ($status === 'read') {
                    $notificationsQuery->whereNotNull('read_at');
                } elseif ($status === 'unread') {
                    $notificationsQuery->whereNull('read_at');
                }
            }

            // Counts
            $allCount = $user->notifications()->count();
            $readCount = $user->notifications()->whereNotNull('read_at')->count();
            $unreadCount = $user->notifications()->whereNull('read_at')->count();

            // Paginate (or use ->get() if you truly want *all* without pagination)
            $notificationsPaginated = $notificationsQuery
                ->simplePaginate(config('constant.api_paginations.notifications', 10));

            // Map notifications (include even blocked users)
            $mapped = $notificationsPaginated->through(function ($notification) {
                $senderId = $notification->data['sender_id'] ?? null;
                $sender = $senderId ? User::find($senderId) : null;

                $response = [
                    'id' => $notification->id,
                    'member_uuid' => $sender?->uuid ?? null,
                    'title' => $notification->data['title'] ?? '',
                    'message' => $notification->data['message'] ?? '',
                    'type' => $notification->data['type'] ?? null,
                    'event_member_id' => $notification->data['extra']['event_member_id'] ?? '',
                    'read_at' => $notification->read_at?->translatedFormat(config('constant.date_format.date_time')),
                    'created_at' => $notification->created_at->translatedFormat(config('constant.date_format.date_time')),
                    // 'profile_image' => $sender?->profile_image_url ?? '',
                    // 'profile_image' => $sender->is_member ? $sender->featuredProfileImage?->file_url ?? null : $sender->profileImage['file_url'] ?? null,
                       'profile_image' => $sender  ? (
                            $sender->is_member
                                ? $sender->featuredProfileImage?->file_url
                                : $sender->profileImage?->file_url
                        )
                        : null,
                    'member_tag' => $sender?->member_tag ?? '',
                    'user_name' => $notification->data['extra']['user'] ?? '',
                    'event_name' => $notification->data['extra']['event'] ?? '',
                ];

                // Add response flag for event_invite_request
                if (($notification->data['type'] ?? null) === 'event_invite_request' || ($notification->data['type'] ?? null) === 'member_event_invite') {
                    // $status = EventMember::where('uuid', $notification->data['extra']['event_member_id'] ?? 0)
                    //     ->value('status');
                    // $response['response_flag'] = in_array($status, ['accepted', 'denied']);

                    $eventMember = EventMember::withTrashed()->where('uuid',$notification->data['extra']['event_member_id'] ?? null)->first();

                    $status = $eventMember?->status;

                    $response['response_flag'] = in_array($status, ['accepted', 'denied']);

                    $response['is_event_full'] = $eventMember?->event ? $eventMember->event->isEventFull() : false;

                    $response['event_details'] = $eventMember?->event ? [
                        'event_id' => $eventMember->event->uuid,
                        'name' => $eventMember->event->name,
                        'event_date_time' => $eventMember->event->event_date_time->translatedFormat(config('constant.date_format.date_time')),
                        'event_end_date_time' => $eventMember->event->event_end_date_time->translatedFormat(config('constant.date_format.date_time')),
                        'location' => $eventMember->event->eventLocation?->name ?? '',

                    ] : null;
                    
                }

                return $response;
            });

            return $this->apiSuccess([
                'all_count' => $allCount,
                'unread_count' => $unreadCount,
                'read_count' => $readCount,
                'activity_notifications' => $mapped,
            ]);

            // $getRequestedMemberNotifiactions = $user->notifications()->where($notificationsQuery->data['type'] === 'event_invite_request')->count();
            // dd($getRequestedMemberNotifiactions);

        } catch (\Throwable $th) {
            // dd($th);
            return $this->apiError(trans('messages.error_message'));
        }
    }



    public function notificationScheduledList(Request $request)
    {
        $user = $request->user();

        if (!$user->is_host) {
            return $this->apiError(trans('messages.only_host_access'), 'only_host_access', [],  403);
        }

        try {

            $notifications = ScheduleNotification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->uuid,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'type' => $notification->type,
                        'schedule_at' => optional($notification->schedule_at) ? Carbon::parse($notification->schedule_at)->translatedFormat(config('constant.date_format.date_time')) : null,
                        'member_count' => isset($notification->all_data['member_count']) ? $notification->all_data['member_count'] : null,
                        'status' => $notification->status,
                    ];
                });

            return $this->apiSuccess(['scheduled_notifications' => $notifications]);
        } catch (\Throwable $th) {
            return $this->apiError(trans('messages.error_message'));
        }
    }


    public function requestedMemberList(Request $request)
    {
        try {
            $host = $request->user();

            if (!$host->is_host) {
                return $this->apiError(
                    'Only host can access',
                    'only_host_access',
                    [],
                    403
                );
            }

            //  Host upcoming event IDs
            $eventIds = Event::where('created_by', $host->id)
                ->where('status', 'approved')
                ->where('event_date_time', '>', now())
                ->pluck('id');

            if ($eventIds->isEmpty()) {
                return $this->apiSuccess(['data' => []]);
            }

            //  Pending Event Members (keyed by UUID)
            $pendingEventMembers = EventMember::whereIn('event_id', $eventIds)
                ->where('status', 'pending')
                ->get()
                ->keyBy('uuid');

            if ($pendingEventMembers->isEmpty()) {
                return $this->apiSuccess(['data' => []]);
            }

            //  Notifications for join requests
            $notifications = Notification::where('notifiable_id', $host->id)
                ->where('type', SendNotification::class)
                ->whereJsonContains('data->type', 'event_invite_request')
                ->orderBy('created_at', 'desc')
                ->get();

            //  Map response (MATCHES notificationActivityList)
            $data = $notifications->map(function ($notification) use ($pendingEventMembers) {

                $eventMemberUUID = $notification->data['extra']['event_member_id'] ?? null;

                if (!$eventMemberUUID || !isset($pendingEventMembers[$eventMemberUUID])) {
                    return null;
                }

                $eventMember = $pendingEventMembers[$eventMemberUUID];
                $member = User::find($eventMember->member_id);

                return [
                    'id'               => $notification->id,
                    'member_uuid'      => $member?->uuid,
                    'title'            => $notification->data['title'] ?? '',
                    'message'          => $notification->data['message'] ?? '',
                    'type'             => $notification->data['type'] ?? '',
                    'event_member_id'  => $eventMember->uuid,
                    'read_at'          => $notification->read_at
                        ? $notification->read_at->translatedFormat(config('constant.date_format.date_time'))
                        : null,
                    'created_at'       => $notification->created_at->translatedFormat(
                        config('constant.date_format.date_time')
                    ),
                    'profile_image'    => $member
                        ? (
                            $member->is_member
                                ? $member->featuredProfileImage?->file_url
                                : $member->profileImage?->file_url
                        )
                        : null,
                    'member_tag'       => $member?->member_tag ?? '',
                    'user_name'        => $notification->data['extra']['user'] ?? '',
                    'event_name'       => $notification->data['extra']['event'] ?? '',
                    'response_flag'    => false, // always false because status = pending
                ];
            })->filter()->values();

            return $this->apiSuccess([
                'data' => $data
            ]);

        } catch (\Throwable $th) {
            return $this->apiError(trans('messages.error_message'));
        }
    }


    public function markAsRead($id)
    {
        try {
            $notification = auth()->user()->notifications()->find($id);
            if ($notification) {
                $notification->markAsRead();
                return response()->json(['message' => 'Marked as read']);
            }
            return $this->apiSuccess([], 'Notification not found', 404);
        } catch (\Throwable $th) {
            return $this->apiError(trans('messages.error_message'));
        }
    }

    public function createSchedule(Request $request)
    {
        try {
            $authUser = $request->user();

            if (!$authUser->is_host) {
                return $this->apiError(trans('messages.only_host_access'), 'only_host_access', [], 403);
            }

            $data = $request->validate([
                'title'       => 'required|string',
                'message'     => 'required|string',
                'type'        => 'nullable|string',
                'url'         => 'nullable|string',
                'all_data'    => 'required|array',
                'schedule_at' => 'required|date',
            ]);

            $allData  = $data['all_data'];
            $eventIds = $allData['eventIds'] ?? null;
            $userIds  = $allData['userIds'] ?? [];


            $userIds = array_unique($userIds);

            if ($eventIds && count($eventIds) > 0) {
                $memberCount = EventMember::whereIn('event_id', $eventIds)->count();
                $allData['eventIds'] = $eventIds;
                unset($allData['userIds']);

                $allData['member_count'] = $memberCount;
                // $allData['custom'] = true;
            } else {

                $allData['userIds']      = $userIds;
                $allData['member_count'] = count($userIds);
                // $allData['custom'] = false;
            }

            $data['all_data'] = $allData;

            $notification = ScheduleNotification::create([
                'user_id'     => $authUser->id,
                'title'       => $data['title'],
                'message'     => $data['message'],
                'type'        => $data['type'] ?? 'event_reminder',
                'url'         => $data['url'] ?? null,
                'all_data'    => $data['all_data'],
                'schedule_at' => $data['schedule_at'],
                'status'      => 'scheduled',
            ]);

            return $this->apiSuccess([], trans('api.notification_scheduled_successfully'));
        } catch (\Throwable $th) {
            return $this->apiError(trans('messages.error_message'));
        }
    }

    public function editSchedule(Request $request, $uuid)
    {
        $authUser = $request->user();

        if (!$authUser->is_host) {
            return $this->apiError(trans('messages.only_host_access'), 'only_host_access', [], 403);
        }

        // Get the notification
        $notification = ScheduleNotification::where('uuid', $uuid)->first();

        if (!$notification) {
            return $this->apiError(trans('messages.not_found'), 'not_found', [], 404);
        }

        // Format notification manually
        $notificationData = [
            'id'           => $notification->uuid,
            'title'        => $notification->title,
            'message'      => $notification->message,
            'type'         => $notification->type,
            'schedule_at'  => optional($notification->schedule_at) ? Carbon::parse($notification->schedule_at)->translatedFormat(config('constant.date_format.date_time')) : null,
            'member_count' => $notification->all_data['member_count'] ?? null,
            'status'       => $notification->status,
            'all_data'     => $notification->all_data ?? [],
        ];

        // Limit handling
        $limit = $request->has('limit') ? (int)$request->limit : null;
        $applyLimit = !is_null($limit) && $limit > 0;

        $allData  = $notificationData['all_data'];
        $custom   = $allData['custom'] ?? false;
        $eventIds = $allData['eventIds'] ?? [];
        $userIds  = $allData['userIds'] ?? [];

        $hiddenIds = $authUser->hiddenUserIds();

        // Counts
        $baseQuery = User::whereHas('roles', fn($q) => $q->where('name', 'Member'))
            ->where('approval_status', 1)
            ->where('is_paused', 0)
            ->where('status', 'active')
            ->whereNotIn('id', $hiddenIds);

        $counts = [
            'all'      => (clone $baseQuery)->count(),
            'new'      => (clone $baseQuery)->where('member_tag', 'new')->where('created_at', '>=', now()->subDays(30))->count(),
            'frequent' => (clone $baseQuery)->where('member_tag', 'frequent')->count(),
            'vip'      => (clone $baseQuery)->where('member_tag', 'vip')->count(),
            'favorite' => $authUser->favoriteMembers()->whereNotIn('member_id', $hiddenIds)->count(),
        ];

        $members = null;
        $customGroups = null;
        $selectedTargetMember = null;

        // CASE 1: Only selected members (userIds)
        if (!empty($userIds)) {
            $membersQuery = User::whereIn('id', $userIds)
                ->whereNotIn('id', $hiddenIds)
                ->with('profileImage');

            if ($applyLimit) {
                $membersQuery->limit($limit);
            }

            $members = $membersQuery->get()->map(function ($user) {
                return [
                    'id'           => $user->id,
                    'uuid'         => $user->uuid,
                    'name'         => $user->name,
                    'email'        => $user->email,
                    'profile_image' => $user->profile_image_url,
                    'member_tag'   => $user->member_tag,
                ];
            })->values(); // Reset keys

            $selectedTargetMember = 'custom';
        }
        // CASE 2: Only selected custom groups (eventIds)
        elseif ($custom && !empty($eventIds)) {
            $groups = $this->getHostAvailableGroups($authUser)
                ->filter(fn($g) => in_array($g['event_id'], $eventIds));

            if ($applyLimit) {
                $groups = $groups->take($limit);
            }

            $customGroups = $groups->map(function ($g) {
                return [
                    'event_id'      => $g['event_id'],
                    'event_name'    => $g['event_name'],
                    'members_count' => $g['members_count'],
                    'image_url'     => $g['image_url'],
                ];
            })->values(); // Reset keys

            $selectedTargetMember = 'custom';
        }
        // CASE 3: other target types (all/new/vip/etc.)
        else {
            foreach (['all', 'new', 'frequent', 'vip', 'favorite', 'custom'] as $key) {
                if (!empty($allData[$key] ?? false)) {
                    $selectedTargetMember = $key;
                    break;
                }
            }
        }

        return $this->apiSuccess([
            'scheduled_notification' => $notificationData,
            'target_counts'          => $counts,
            'members'                => $members,
            'custom_groups'          => $customGroups,
            'selected_target_member' => $selectedTargetMember,
            'update_button' => $notification->status == 'scheduled' && (optional($notification->schedule_at) && Carbon::parse($notification->schedule_at)->isFuture()),
        ]);
    }

    public function updateSchedule(Request $request, $uuid)
    {
        try {
            $authUser = $request->user();

            if (!$authUser->is_host) {
                return $this->apiError(trans('messages.only_host_access'), 'only_host_access', [], 403);
            }

            // Fetch the existing notification
            $notification = ScheduleNotification::where('uuid', $uuid)->first();
            if (!$notification) {
                return $this->apiError(trans('messages.not_found'), 'not_found', [], 404);
            }

            if ($notification->status == 'sent') {
                return $this->apiError(trans('messages.already_sent'), 'already_sent', [], 400);
            }
            if ($notification->status == 'cancelled') {
                return $this->apiError(trans('messages.already_cancelled'), 'already_cancelled', [], 400);
            }


            $data = $request->validate([
                'title'       => 'required|string',
                'message'     => 'required|string',
                'type'        => 'nullable|string',
                'url'         => 'nullable|string',
                'all_data'    => 'required|array',
                'schedule_at' => 'required|date',
            ]);

            $allData  = $data['all_data'];
            $eventIds = $allData['eventIds'] ?? null;
            $userIds  = $allData['userIds'] ?? [];

            $userIds = array_unique($userIds);

            if ($eventIds && count($eventIds) > 0) {
                // Event-based notification
                $memberCount = EventMember::whereIn('event_id', $eventIds)->count();
                $allData['eventIds'] = $eventIds;
                unset($allData['userIds']);
                $allData['member_count'] = $memberCount;
                $allData['custom'] = true;
            } else {
                // When all | new | frequent | vip | favorite users
                $allData['userIds'] = $userIds;
                unset($allData['eventIds']);
                $allData['member_count'] = count($userIds);
                $allData['custom'] = false;
            }

            $notification->update([
                'title'       => $data['title'],
                'message'     => $data['message'],
                'type'        => $data['type'] ?? $notification->type,
                'url'         => $data['url'] ?? $notification->url,
                'all_data'    => $allData,
                'schedule_at' => $data['schedule_at'],
                'status'      => $notification->status,
            ]);

            return $this->apiSuccess([], trans('api.notification_updated_successfully'));
        } catch (\Throwable $th) {
            return $this->apiError(trans('messages.error_message'));
        }
    }


    public function targetMembers(Request $request)
    {
        $authUser = $request->user('sanctum');

        if (!$authUser || !$authUser->isHost) {
            return $this->apiError(trans('messages.only_host_access'), 'unauthorized_access', [], 403);
        }

        try {

            /*
        |--------------------------------------------------------------------------
        | LIMIT HANDLING (FIXED)
        |--------------------------------------------------------------------------
        | If limit is NOT provided → unlimited
        | If limit is provided → apply limit
        */
            $limit = $request->has('limit') ? (int) $request->limit : null;
            $applyLimit = !is_null($limit) && $limit > 0;

            /*
        |--------------------------------------------------------------------------
        | HIDDEN USER IDS (cached)
        |--------------------------------------------------------------------------
        */
            static $hiddenIdsCache = null;
            if ($hiddenIdsCache === null) {
                $hiddenIdsCache = $authUser->hiddenUserIds();
            }
            $hiddenIds = $hiddenIdsCache;

            /*
        |--------------------------------------------------------------------------
        | BASE QUERY (UNLIMITED - USED FOR COUNTS)
        |--------------------------------------------------------------------------
        */
            $baseQuery = User::whereHas('roles', fn($q) => $q->where('name', 'Member'))
                ->where('approval_status', 1)
                ->where('is_paused', 0)
                ->where('status', 'active')
                ->whereNotIn('id', $hiddenIds);

            /*
        |--------------------------------------------------------------------------
        | COUNTS (ALWAYS FULL, NO LIMIT APPLIED)
        |--------------------------------------------------------------------------
        */
            $counts = [
                'all' => (clone $baseQuery)->count(),

                'new' => (clone $baseQuery)
                    ->where('member_tag', 'new')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count(),

                'frequent' => (clone $baseQuery)
                    ->where('member_tag', 'frequent')
                    ->count(),

                'vip' => (clone $baseQuery)
                    ->where('member_tag', 'vip')
                    ->count(),

                'favorite' => $authUser->favoriteMembers()
                    ->whereNotIn('member_id', $hiddenIds)
                    ->count(),
            ];

            /*
        |--------------------------------------------------------------------------
        | CUSTOM REQUEST → RETURN ONLY COUNTS
        |--------------------------------------------------------------------------
        */
            if ($request->status === 'custom') {
                return $this->apiSuccess([
                    'target_counts' => $counts,
                    'custom_groups' => $this->getHostAvailableGroups($authUser)
                        ->when($limit, fn($q) => $q->take($limit)),
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | MEMBER LIST (LIMIT APPLIED ONLY HERE)
        |--------------------------------------------------------------------------
        */
            $status = strtolower($request->input('status', 'all'));
            $membersQuery = clone $baseQuery;

            switch ($status) {

                case 'new':
                    $membersQuery->where('member_tag', 'new')
                        ->where('created_at', '>=', now()->subDays(30));
                    break;

                case 'frequent':
                    $membersQuery->where('member_tag', 'frequent');
                    break;

                case 'vip':
                    $membersQuery->where('member_tag', 'vip');
                    break;

                case 'favorite':
                    // Overwrite query with favorite relation
                    $membersQuery = $authUser->favoriteMembers()
                        ->whereNotIn('member_id', $hiddenIds)
                        ->with('member.profileImage');
                    break;

                case 'all':
                default:
                    // no extra filter
                    break;
            }

            // Apply sorting & load relation for non-favorite queries
            if ($status !== 'favorite') {
                $membersQuery->orderByDesc('created_at')
                    ->with('profileImage');
            }

            // LIMIT APPLIED ONLY WHEN limit is given
            if ($applyLimit) {
                $membersQuery->limit($limit);
            }

            /*
        |--------------------------------------------------------------------------
        | FAVORITE FLAGS
        |--------------------------------------------------------------------------
        */
            $favoriteMemberIds = HostMembers::where('host_id', $authUser->id)
                ->where('is_favorite', 1)
                ->pluck('member_id')
                ->toArray();

            /*
        |--------------------------------------------------------------------------
        | FINAL RESPONSE MAPPING
        |--------------------------------------------------------------------------
        */
            $members = $membersQuery->get()->map(function ($item) use ($status, $favoriteMemberIds) {
                $user = ($status === 'favorite') ? $item->member : $item;

                return [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'email' => $user->email,
                    // 'profile_image' => $user->profile_image_url,
                    'profile_image' => $user->featuredProfileImage?->file_url ?? null,
                    'member_tag' => $user->member_tag,
                    'is_favorite' => in_array($user->id, $favoriteMemberIds) ? 1 : 0,
                ];
            });

            return $this->apiSuccess([
                'target_counts' => $counts,
                'members' => $members,
            ]);
        } catch (\Throwable $th) {
            return $this->apiError(trans('messages.error_message'), 'server_error', [], 500);
        }
    }


    private function getHostAvailableGroups($user)
    {
        // Fetch Old events created by the host
        $upcomingEvents = Event::where('created_by', $user->id)
            // ->where('event_date_time', '<=', now())
            ->withCount('invitedMembers')
            ->orderBy('event_date_time', 'asc')
            ->get();

        return $upcomingEvents->map(function ($event) {
            return [
                'event_id' => $event->id,
                // 'user_ids' => $event->invitedMembers->pluck('member_id')->toArray(),
                'event_name' => $event->name,
                'members_count' => $event->invited_members_count,
                'image_url' => $event->getCoverImageUrlAttribute() ?? null,
            ];
        });
    }

}
