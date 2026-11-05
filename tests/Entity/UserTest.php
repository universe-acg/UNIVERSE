<?php

    namespace App\Tests\Entity;

    use App\Entity\Organizer;
    use App\Entity\User;
    use PHPUnit\Framework\TestCase;

    class UserTest extends TestCase
    {

        public function testStringifypointslevel()
        {
            // Create a User instance or mock it if necessary
            $user = new User();
            $organizer = new Organizer();
            $organizer->setCategory("Club");
            $user->setRoles(["ROLE_ORGANIZER"]);
            $user->setOrganizer($organizer); // Set the user as a regular user

            // Set the points on the user
            $user->setPoints(11); // Adjust this value based on your test case

            // Call the function to test
            $result = $user->stringifyPointsLevel();
            // Assert the expected result based on the test case
            $this->assertEquals("Learner of Many Things", $result);
        }
    }
