<?php
/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

namespace App\Service;

class LeaderboardsService
{

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get all attendees from the leaderboard.
     *
     * @return array
     */
    public function getAllAttendees()
    {
        $users = $this->userService->getUsers([])->getQuery()->getResult();
        $filteredUsers = [];

        foreach ($users as $user) {
            if ($user->getOrganizer() == null) {
                $filteredUsers[] = $user;
            }
        }

        return $filteredUsers;
    }

    /**
     * Get all creators from the leaderboard.
     *
     * @return array
     */
    public function getAllCreators()
    {
        $users = $this->userService->getUsers([])->getQuery()->getResult();
        $filteredUsers = [];

        foreach ($users as $user) {
            if ($user->getOrganizer()) {
                $filteredUsers[] = $user;
            }
        }

        return $filteredUsers;
    }
}