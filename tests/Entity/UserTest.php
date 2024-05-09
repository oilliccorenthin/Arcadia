
<?php

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetAndSetEmail()
    {
        $user = new User();
        $user->setEmail('john_doe@gmail.com');

        $this->assertEquals('john_doe@gmail.com', $user->getEmail());
    }

    public function testGetAndSetPassword()
    {
        $user = new User();
        $user->setPassword('password');

        $this->assertEquals('password', $user->getPassword());
    }

    public function testGetRoles()
    {
        $user = new User();

        // Test when no roles are set
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        // Test when roles are set
        $user->setRoleObjects(new ArrayCollection([$this->createMock(Role::class)]));
        $this->assertContains('ROLE_USER', $user->getRoles());
    }
}
