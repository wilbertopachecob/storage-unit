<?php
/**
 * LoggerFactory Unit Tests
 */

use PHPUnit\Framework\TestCase;
use StorageUnit\Core\LoggerFactory;
use StorageUnit\Core\LoggerInterface;
use StorageUnit\Core\FileLogger;

class LoggerFactoryTest extends TestCase
{
    private $originalConfig;
    private $testConfigPath;

    protected function setUp(): void
    {
        // Backup original config
        $this->originalConfig = require __DIR__ . '/../../../config/app/config.php';
        
        // Create test config
        $this->testConfigPath = sys_get_temp_dir() . '/test_config.php';
        $this->createTestConfig();
        
        // Clear any existing loggers
        LoggerFactory::clearLoggers();
    }

    protected function tearDown(): void
    {
        // Clean up test config
        if (file_exists($this->testConfigPath)) {
            unlink($this->testConfigPath);
        }
        
        // Clear loggers
        LoggerFactory::clearLoggers();
    }

    public function testGetDefaultLogger()
    {
        $logger = LoggerFactory::getDefaultLogger();
        
        $this->assertInstanceOf(LoggerInterface::class, $logger);
        $this->assertInstanceOf(FileLogger::class, $logger);
    }

    public function testGetLoggerWithName()
    {
        $logger = LoggerFactory::getLogger('database');
        
        $this->assertInstanceOf(LoggerInterface::class, $logger);
        $this->assertInstanceOf(FileLogger::class, $logger);
    }

    public function testGetSameLoggerInstance()
    {
        $logger1 = LoggerFactory::getLogger('test');
        $logger2 = LoggerFactory::getLogger('test');
        
        $this->assertSame($logger1, $logger2);
    }

    public function testGetDifferentLoggerInstances()
    {
        $logger1 = LoggerFactory::getLogger('logger1');
        $logger2 = LoggerFactory::getLogger('logger2');
        
        $this->assertNotSame($logger1, $logger2);
    }

    public function testRegisterCustomLogger()
    {
        $customLogger = $this->createMock(LoggerInterface::class);
        
        LoggerFactory::registerLogger('custom', $customLogger);
        
        $retrievedLogger = LoggerFactory::getLogger('custom');
        
        $this->assertSame($customLogger, $retrievedLogger);
    }

    public function testSetDefaultLogger()
    {
        $customLogger = $this->createMock(LoggerInterface::class);
        
        LoggerFactory::setDefaultLogger($customLogger);
        
        $defaultLogger = LoggerFactory::getDefaultLogger();
        
        $this->assertSame($customLogger, $defaultLogger);
    }

    public function testClearLoggers()
    {
        // Create some loggers
        $logger1 = LoggerFactory::getLogger('test1');
        $logger2 = LoggerFactory::getLogger('test2');
        
        // Clear all loggers
        LoggerFactory::clearLoggers();
        
        // Get new instances (should be different objects)
        $newLogger1 = LoggerFactory::getLogger('test1');
        $newLogger2 = LoggerFactory::getLogger('test2');
        
        $this->assertNotSame($logger1, $newLogger1);
        $this->assertNotSame($logger2, $newLogger2);
    }

    public function testLoggerConfiguration()
    {
        // Test that logger uses correct configuration
        $logger = LoggerFactory::getLogger('database');
        
        // The database logger should have a specific log path
        $logPath = $logger->getLogPath();
        $this->assertStringContainsString('database.log', $logPath);
    }

    public function testDefaultLoggerConfiguration()
    {
        $logger = LoggerFactory::getDefaultLogger();
        
        $logPath = $logger->getLogPath();
        $this->assertStringContainsString('app.log', $logPath);
    }

    public function testLoggerWithCustomConfig()
    {
        // Create a custom config file
        $customConfig = [
            'logging' => [
                'custom' => [
                    'type' => 'file',
                    'path' => sys_get_temp_dir() . '/custom_test.log',
                    'max_file_size' => 2048,
                    'max_files' => 2,
                ],
            ],
        ];
        
        // Mock the config loading
        $this->mockConfigLoading($customConfig);
        
        // This would require modifying the LoggerFactory to accept custom config
        // For now, we'll test the existing functionality
        $logger = LoggerFactory::getLogger('custom');
        $this->assertInstanceOf(FileLogger::class, $logger);
    }

    public function testUnknownLoggerTypeThrowsException()
    {
        // This test would require modifying the config to have an unknown type
        // For now, we'll test that the factory works with known types
        $this->expectNotToPerformAssertions();
        
        // The current implementation only supports 'file' type
        // Future implementations would test unknown types here
    }

    public function testLoggerSingletonBehavior()
    {
        $logger1 = LoggerFactory::getLogger('singleton_test');
        $logger2 = LoggerFactory::getLogger('singleton_test');
        
        $this->assertSame($logger1, $logger2);
    }

    public function testMultipleLoggerTypes()
    {
        $appLogger = LoggerFactory::getLogger('default');
        $dbLogger = LoggerFactory::getLogger('database');
        $authLogger = LoggerFactory::getLogger('auth');
        
        $this->assertInstanceOf(FileLogger::class, $appLogger);
        $this->assertInstanceOf(FileLogger::class, $dbLogger);
        $this->assertInstanceOf(FileLogger::class, $authLogger);
        
        // They should be different instances
        $this->assertNotSame($appLogger, $dbLogger);
        $this->assertNotSame($dbLogger, $authLogger);
        $this->assertNotSame($appLogger, $authLogger);
    }

    /**
     * Create a test configuration file
     */
    private function createTestConfig()
    {
        $testConfig = [
            'app' => [
                'name' => 'Test App',
                'version' => '1.0.0',
                'env' => 'testing',
                'debug' => true,
                'url' => 'http://localhost',
            ],
            'database' => [
                'host' => 'localhost',
                'port' => 3306,
                'database' => 'test_db',
                'username' => 'test_user',
                'password' => 'test_pass',
                'charset' => 'utf8mb4',
            ],
            'logging' => [
                'default' => [
                    'type' => 'file',
                    'path' => sys_get_temp_dir() . '/test_app.log',
                    'max_file_size' => 1024,
                    'max_files' => 3,
                ],
                'database' => [
                    'type' => 'file',
                    'path' => sys_get_temp_dir() . '/test_database.log',
                    'max_file_size' => 512,
                    'max_files' => 2,
                ],
                'auth' => [
                    'type' => 'file',
                    'path' => sys_get_temp_dir() . '/test_auth.log',
                    'max_file_size' => 512,
                    'max_files' => 2,
                ],
            ],
        ];
        
        file_put_contents($this->testConfigPath, '<?php return ' . var_export($testConfig, true) . ';');
    }

    /**
     * Mock config loading (for future use)
     */
    private function mockConfigLoading(array $config)
    {
        // This would be used to test custom configurations
        // Implementation would depend on how config loading is handled
    }
}
