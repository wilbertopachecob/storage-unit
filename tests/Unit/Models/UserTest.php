<?php
/**
 * User Model Tests
 */

namespace StorageUnit\Tests\Unit\Models;

use StorageUnit\Tests\TestCase;
use StorageUnit\Models\User;

class UserTest extends TestCase
{
    public function testUserCreation()
    {
        $user = new User('newuser@example.com', 'password123', 'New User');
        
        $this->assertEquals('newuser@example.com', $user->getEmail());
        $this->assertEquals('New User', $user->getName());
    }

    public function testUserCreate()
    {
        $user = new User('newuser@example.com', 'password123', 'New User');
        
        $this->assertTrue($user->create());
        $this->assertNotNull($user->getId());
        $this->assertTrue($user->emailExists('newuser@example.com'));
    }

    public function testUserCreateWithDuplicateEmail()
    {
        $user1 = new User('duplicate@example.com', 'password123', 'User 1');
        $user1->create();
        
        $user2 = new User('duplicate@example.com', 'password456', 'User 2');
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Email already exists');
        $user2->create();
    }

    public function testUserAuthenticate()
    {
        $user = new User('test@example.com', 'password123');
        
        $this->assertTrue($user->authenticate());
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('Test User', $user->getName());
    }

    public function testUserAuthenticateWithWrongPassword()
    {
        $user = new User('test@example.com', 'wrongpassword');
        
        $this->assertFalse($user->authenticate());
    }

    public function testUserAuthenticateWithNonExistentEmail()
    {
        $user = new User('nonexistent@example.com', 'password123');
        
        $this->assertFalse($user->authenticate());
    }

    public function testEmailExists()
    {
        $user = new User();
        
        $this->assertTrue($user->emailExists('test@example.com'));
        $this->assertFalse($user->emailExists('nonexistent@example.com'));
    }

    public function testFindById()
    {
        $user = User::findById(1);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('Test User', $user->getName());
    }

    public function testFindByIdWithNonExistentId()
    {
        $user = User::findById(999);
        
        $this->assertNull($user);
    }

    public function testUpdateUser()
    {
        $user = User::findById(1);
        $user->setName('Updated Name');
        $user->setEmail('updated@example.com');
        
        $this->assertTrue($user->update());
        
        $updatedUser = User::findById(1);
        $this->assertEquals('Updated Name', $updatedUser->getName());
        $this->assertEquals('updated@example.com', $updatedUser->getEmail());
    }

    public function testDeleteUser()
    {
        $userId = $this->createTestUser('todelete@example.com', 'To Delete User');
        
        $user = User::findById($userId);
        $this->assertTrue($user->delete());
        
        $deletedUser = User::findById($userId);
        $this->assertNull($deletedUser);
    }

    public function testIsLoggedIn()
    {
        $this->clearSession();
        $this->assertFalse(User::isLoggedIn());
        
        $this->authenticateUser();
        $this->assertTrue(User::isLoggedIn());
    }

    public function testGetCurrentUser()
    {
        $this->clearSession();
        $this->assertNull(User::getCurrentUser());
        
        $this->authenticateUser();
        $currentUser = User::getCurrentUser();
        
        $this->assertInstanceOf(User::class, $currentUser);
        $this->assertEquals(1, $currentUser->getId());
    }

    public function testLogout()
    {
        $this->authenticateUser();
        $this->assertTrue(User::isLoggedIn());
        
        User::logout();
        $this->assertFalse(User::isLoggedIn());
    }

    public function testUserValidation()
    {
        // Test with invalid email
        $user = new User('invalid-email', 'password123', 'Test User');
        $this->expectException(\Exception::class);
        $user->create();
    }

    public function testUserValidationWithShortPassword()
    {
        // Test with short password
        $user = new User('test@example.com', 'short', 'Test User');
        $this->expectException(\Exception::class);
        $user->create();
    }

    public function testUserValidationWithEmptyName()
    {
        // Test with empty name
        $user = new User('test@example.com', 'password123', '');
        $this->expectException(\Exception::class);
        $user->create();
    }
}
