<?php
/**
 * User Model Unit Tests
 */

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use StorageUnit\Models\User;
use StorageUnit\Core\Database;
use StorageUnit\Core\Security;
use PDO;
use PDOStatement;

class UserTest extends TestCase
{
    private $mockDb;
    private $mockConnection;
    private $mockStatement;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock database connection
        $this->mockStatement = $this->createMock(PDOStatement::class);
        $this->mockConnection = $this->createMock(PDO::class);
        $this->mockDb = $this->createMock(Database::class);
        
        // Set up database mock
        $this->mockDb->method('getConnection')->willReturn($this->mockConnection);
        Database::setInstance($this->mockDb);
        
        // Mock Security class
        $this->mockSecurity = $this->createMock(Security::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Database::setInstance(null);
    }

    public function testUserCreation()
    {
        $user = new User('testuser', 'test@example.com', 'password123');
        
        $this->assertEquals('testuser', $user->getName());
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('password123', $user->getPassword());
    }

    public function testUserSetters()
    {
        $user = new User();
        
        $user->setName('newuser');
        $user->setEmail('new@example.com');
        $user->setPassword('newpassword');
        $user->setStorageUnitName('My Storage');
        $user->setStorageUnitAddress('123 Main St');
        $user->setStorageUnitLatitude(40.7128);
        $user->setStorageUnitLongitude(-74.0060);
        $user->setProfilePicture('avatar.jpg');
        
        $this->assertEquals('newuser', $user->getName());
        $this->assertEquals('new@example.com', $user->getEmail());
        $this->assertEquals('newpassword', $user->getPassword());
        $this->assertEquals('My Storage', $user->getStorageUnitName());
        $this->assertEquals('123 Main St', $user->getStorageUnitAddress());
        $this->assertEquals(40.7128, $user->getStorageUnitLatitude());
        $this->assertEquals(-74.0060, $user->getStorageUnitLongitude());
        $this->assertEquals('avatar.jpg', $user->getProfilePicture());
    }

    public function testUsernameAliases()
    {
        $user = new User();
        
        $user->setUsername('testuser');
        $this->assertEquals('testuser', $user->getUsername());
        $this->assertEquals('testuser', $user->getName());
        
        $user->setName('newname');
        $this->assertEquals('newname', $user->getUsername());
    }

    public function testCreateUser()
    {
        $user = new User('testuser', 'test@example.com', 'password123');
        
        $this->mockStatement->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->exactly(2))
            ->method('prepare')
            ->willReturn($this->mockStatement);
            
        $this->mockConnection->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('123');
        
        $result = $user->create();
        
        $this->assertTrue($result);
        $this->assertEquals(123, $user->getId());
    }

    public function testCreateUserFails()
    {
        $user = new User('testuser', 'test@example.com', 'password123');
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(false);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $user->create();
        
        $this->assertFalse($result);
    }

    public function testAuthenticateUser()
    {
        $user = new User('testuser', 'test@example.com', 'password123');
        
        $mockData = [
            'id' => 123,
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with();
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $user->authenticate();
        
        $this->assertTrue($result);
        $this->assertEquals(123, $user->getId());
    }

    public function testAuthenticateUserFails()
    {
        $user = new User('testuser', 'test@example.com', 'wrongpassword');
        
        $mockData = [
            'id' => 123,
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':email' => 'test@example.com']);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $user->authenticate();
        
        $this->assertFalse($result);
    }

    public function testEmailExists()
    {
        $user = new User();
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':email' => 'test@example.com']);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(1);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $exists = $user->emailExists('test@example.com');
        
        $this->assertTrue($exists);
    }

    public function testStaticEmailExists()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':email' => 'test@example.com']);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(1);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $exists = User::emailExists('test@example.com');
        
        $this->assertTrue($exists);
    }

    public function testStaticEmailExistsWithExcludeId()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':email' => 'test@example.com', ':exclude_id' => 123]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(0);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $exists = User::emailExists('test@example.com', 123);
        
        $this->assertFalse($exists);
    }

    public function testUsernameExists()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':username' => 'testuser']);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(1);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $exists = User::usernameExists('testuser');
        
        $this->assertTrue($exists);
    }

    public function testUsernameExistsWithExcludeId()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':username' => 'testuser', ':exclude_id' => 123]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(0);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $exists = User::usernameExists('testuser', 123);
        
        $this->assertFalse($exists);
    }

    public function testFindByUsernameOrEmail()
    {
        $mockData = [
            'id' => 123,
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'hashedpassword',
            'storage_unit_name' => 'My Storage',
            'storage_unit_address' => '123 Main St',
            'storage_unit_latitude' => 40.7128,
            'storage_unit_longitude' => -74.0060,
            'storage_unit_updated_at' => '2024-01-01 00:00:00',
            'profile_picture' => 'avatar.jpg',
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':identifier' => 'testuser']);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $user = User::findByUsernameOrEmail('testuser');
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(123, $user->getId());
        $this->assertEquals('testuser', $user->getName());
        $this->assertEquals('test@example.com', $user->getEmail());
    }

    public function testFindByUsernameOrEmailNotFound()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':identifier' => 'nonexistent']);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn(false);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $user = User::findByUsernameOrEmail('nonexistent');
        
        $this->assertNull($user);
    }

    public function testFindById()
    {
        $mockData = [
            'id' => 123,
            'name' => 'testuser',
            'email' => 'test@example.com',
            'storage_unit_name' => 'My Storage',
            'storage_unit_address' => '123 Main St',
            'storage_unit_latitude' => 40.7128,
            'storage_unit_longitude' => -74.0060,
            'storage_unit_updated_at' => '2024-01-01 00:00:00',
            'profile_picture' => 'avatar.jpg',
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':id' => 123]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $user = User::findById(123);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(123, $user->getId());
        $this->assertEquals('testuser', $user->getName());
    }

    public function testFindByIdNotFound()
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':id' => 999]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn(false);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $user = User::findById(999);
        
        $this->assertNull($user);
    }

    public function testUpdateUser()
    {
        $user = new User('testuser', 'test@example.com', 'password123');
        $user->setId(123);
        $user->setStorageUnitName('Updated Storage');
        $user->setProfilePicture('new-avatar.jpg');
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $user->update();
        
        $this->assertTrue($result);
    }

    public function testUpdateUserWithPassword()
    {
        $user = new User('testuser', 'test@example.com', 'newpassword');
        $user->setId(123);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $user->update();
        
        $this->assertTrue($result);
    }

    public function testUpdateStorageUnit()
    {
        $user = new User();
        $user->setId(123);
        $user->setStorageUnitName('My Storage');
        $user->setStorageUnitAddress('123 Main St');
        $user->setStorageUnitLatitude(40.7128);
        $user->setStorageUnitLongitude(-74.0060);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $user->updateStorageUnit();
        
        $this->assertTrue($result);
    }

    public function testUpdateProfilePicture()
    {
        $user = new User();
        $user->setId(123);
        $user->setProfilePicture('new-avatar.jpg');
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $user->updateProfilePicture();
        
        $this->assertTrue($result);
    }

    public function testDeleteUser()
    {
        $user = new User();
        $user->setId(123);
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->willReturn(true);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $result = $user->delete();
        
        $this->assertTrue($result);
    }

    public function testIsLoggedIn()
    {
        // Test when not logged in
        $_SESSION = [];
        $this->assertFalse(User::isLoggedIn());
        
        // Test when logged in
        $_SESSION['user_id'] = 123;
        $this->assertTrue(User::isLoggedIn());
    }

    public function testGetCurrentUser()
    {
        // Test when not logged in
        $_SESSION = [];
        $user = User::getCurrentUser();
        $this->assertNull($user);
        
        // Test when logged in
        $_SESSION['user_id'] = 123;
        
        $mockData = [
            'id' => 123,
            'name' => 'testuser',
            'email' => 'test@example.com',
            'storage_unit_name' => 'My Storage',
            'storage_unit_address' => '123 Main St',
            'storage_unit_latitude' => 40.7128,
            'storage_unit_longitude' => -74.0060,
            'storage_unit_updated_at' => '2024-01-01 00:00:00',
            'profile_picture' => 'avatar.jpg',
            'created_at' => '2024-01-01 00:00:00',
            'updated_at' => '2024-01-01 00:00:00'
        ];
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([':id' => 123]);
            
        $this->mockStatement->expects($this->once())
            ->method('fetch')
            ->willReturn($mockData);
            
        $this->mockConnection->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStatement);
        
        $user = User::getCurrentUser();
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(123, $user->getId());
    }
}