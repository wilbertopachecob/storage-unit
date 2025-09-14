<?php
/**
 * FileLogger Unit Tests
 */

use PHPUnit\Framework\TestCase;
use StorageUnit\Core\FileLogger;

class FileLoggerTest extends TestCase
{
    private $testLogPath;
    private $logger;

    protected function setUp(): void
    {
        // Create a temporary log file for testing
        $this->testLogPath = sys_get_temp_dir() . '/test_app.log';
        
        // Clean up any existing test log file
        if (file_exists($this->testLogPath)) {
            unlink($this->testLogPath);
        }
        
        // Create logger with test path
        $this->logger = new FileLogger($this->testLogPath, 1024, 3); // 1KB max, 3 files max
    }

    protected function tearDown(): void
    {
        // Clean up test log files
        $this->logger->clearLogs();
    }

    public function testLoggerImplementsInterface()
    {
        $this->assertInstanceOf(\StorageUnit\Core\LoggerInterface::class, $this->logger);
    }

    public function testEmergencyLogging()
    {
        $message = 'System is unusable';
        $context = ['error' => 'Database down'];
        
        $this->logger->emergency($message, $context);
        
        $this->assertLogContains('EMERGENCY', $message, $context);
    }

    public function testAlertLogging()
    {
        $message = 'Action must be taken immediately';
        $context = ['alert' => 'High memory usage'];
        
        $this->logger->alert($message, $context);
        
        $this->assertLogContains('ALERT', $message, $context);
    }

    public function testCriticalLogging()
    {
        $message = 'Critical condition detected';
        $context = ['component' => 'database'];
        
        $this->logger->critical($message, $context);
        
        $this->assertLogContains('CRITICAL', $message, $context);
    }

    public function testErrorLogging()
    {
        $message = 'An error occurred';
        $context = ['code' => 500, 'file' => 'test.php'];
        
        $this->logger->error($message, $context);
        
        $this->assertLogContains('ERROR', $message, $context);
    }

    public function testWarningLogging()
    {
        $message = 'This is a warning';
        $context = ['deprecated' => true];
        
        $this->logger->warning($message, $context);
        
        $this->assertLogContains('WARNING', $message, $context);
    }

    public function testNoticeLogging()
    {
        $message = 'Normal but significant condition';
        $context = ['event' => 'user_login'];
        
        $this->logger->notice($message, $context);
        
        $this->assertLogContains('NOTICE', $message, $context);
    }

    public function testInfoLogging()
    {
        $message = 'Interesting event occurred';
        $context = ['user_id' => 123];
        
        $this->logger->info($message, $context);
        
        $this->assertLogContains('INFO', $message, $context);
    }

    public function testDebugLogging()
    {
        $message = 'Debug information';
        $context = ['variable' => 'test_value'];
        
        $this->logger->debug($message, $context);
        
        $this->assertLogContains('DEBUG', $message, $context);
    }

    public function testCustomLogLevel()
    {
        $message = 'Custom log level test';
        $context = ['custom' => 'data'];
        
        $this->logger->log('CUSTOM', $message, $context);
        
        $this->assertLogContains('CUSTOM', $message, $context);
    }

    public function testLoggingWithoutContext()
    {
        $message = 'Simple message without context';
        
        $this->logger->info($message);
        
        $this->assertLogContains('INFO', $message, []);
    }

    public function testLogFileCreation()
    {
        $this->assertFileExists($this->testLogPath);
    }

    public function testLogFormat()
    {
        $message = 'Test message';
        $context = ['key' => 'value'];
        
        $this->logger->info($message, $context);
        
        $logContent = file_get_contents($this->testLogPath);
        
        // Check timestamp format
        $this->assertMatchesRegularExpression('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $logContent);
        
        // Check level
        $this->assertStringContainsString('INFO:', $logContent);
        
        // Check message
        $this->assertStringContainsString($message, $logContent);
        
        // Check context JSON
        $this->assertStringContainsString('{"key":"value"}', $logContent);
    }

    public function testLogRotation()
    {
        // Fill the log file to trigger rotation
        $largeMessage = str_repeat('A', 1000); // 1KB message
        
        // Write multiple messages to exceed max file size
        for ($i = 0; $i < 2; $i++) {
            $this->logger->info($largeMessage . " - Message $i");
        }
        
        // Check if rotation occurred
        $this->assertFileExists($this->testLogPath . '.1');
    }

    public function testLogRotationMaxFiles()
    {
        // Create multiple log files to test max files limit
        $largeMessage = str_repeat('A', 1000);
        
        // Write enough messages to create multiple rotated files
        for ($i = 0; $i < 10; $i++) {
            $this->logger->info($largeMessage . " - Message $i");
        }
        
        // Should have current log + 3 rotated files (max_files = 3)
        $this->assertFileExists($this->testLogPath);
        $this->assertFileExists($this->testLogPath . '.1');
        $this->assertFileExists($this->testLogPath . '.2');
        $this->assertFileExists($this->testLogPath . '.3');
        
        // Should not have more than max_files
        $this->assertFileDoesNotExist($this->testLogPath . '.4');
    }

    public function testClearLogs()
    {
        // Write some logs
        $this->logger->info('Test message 1');
        $this->logger->error('Test message 2');
        
        // Verify logs exist
        $this->assertFileExists($this->testLogPath);
        $this->assertStringContainsString('Test message 1', file_get_contents($this->testLogPath));
        
        // Clear logs
        $this->logger->clearLogs();
        
        // Verify logs are cleared
        $this->assertFileDoesNotExist($this->testLogPath);
    }

    public function testGetLogPath()
    {
        $this->assertEquals($this->testLogPath, $this->logger->getLogPath());
    }

    public function testLogDirectoryCreation()
    {
        $nonExistentDir = sys_get_temp_dir() . '/non_existent_dir/test.log';
        $logger = new FileLogger($nonExistentDir);
        
        $this->assertFileExists($nonExistentDir);
        $this->assertTrue(is_dir(dirname($nonExistentDir)));
        
        // Clean up
        unlink($nonExistentDir);
        rmdir(dirname($nonExistentDir));
    }

    /**
     * Helper method to assert log contains specific content
     */
    private function assertLogContains(string $level, string $message, array $context)
    {
        $logContent = file_get_contents($this->testLogPath);
        
        $this->assertStringContainsString($level, $logContent);
        $this->assertStringContainsString($message, $logContent);
        
        if (!empty($context)) {
            $contextJson = json_encode($context);
            $this->assertStringContainsString($contextJson, $logContent);
        }
    }
}
