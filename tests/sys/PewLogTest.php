<?php 

require 'pew_log.class.php';

class PewLogTest extends PHPUnit_Framework_TestCase
{
    public function testClassIsInstancedSuccessfully()
    {
        # No arguments
        $log = new PewLog();
        $this->assertInstanceOf('PewLog', $log);

        # Filename
        $log = new PewLog('file');
        $this->assertInstanceOf('PewLog', $log);
        $this->assertEquals('.\file', $log->log_file());

        # Date format
        $log = new PewLog(null, 'm-d');
        $this->assertInstanceOf('PewLog', $log);

        # Time format
        $log = new PewLog(null, null, 'H:m');
        $this->assertInstanceOf('PewLog', $log);
        $this->assertEquals('H:m', $log->time_format());

        # Filename and date format
        $log = new PewLog('logs/file.txt', 'm-d');
        $this->assertInstanceOf('PewLog', $log);
        $this->assertEquals('logs\file.txt', $log->log_file());
        $this->assertEquals('m-d', $log->date_format());

        # Filename and time format
        $log = new PewLog('file', null, 'H:m');
        $this->assertInstanceOf('PewLog', $log);

        # Date format and time format
        $log = new PewLog(null, 'm-d', 'H:m');
        $this->assertInstanceOf('PewLog', $log);

        # Filename, date format and time format
        $log = new PewLog('file', 'm-d', 'H:m');
        $this->assertInstanceOf('PewLog', $log);
    }

    public function testNewLogFilesAreCreatedCorrectly()
    {
        $filename = 'logs'. DIRECTORY_SEPARATOR . date('Y-m-d') . '.txt';

        $log = new PewLog;
        $log->error('test');
        $log->dump();

        $this->assertFileExists($log->log_file());
        $this->assertEquals($filename, $log->log_file());

        unlink($filename);
    }

    public function testLogFileIsCreatedOnLogDestruction()
    {
        $filename = 'logs/testLogFileIsCreatedOnLogDestruction.txt';

        $log = new PewLog($filename);
        $log->debug('debug');
        $log->alert('alert');
        $log->error('error');

        unset($log);

        $this->assertFileExists($filename);

        unlink($filename);   
    }

    public function testLogFileIsNotCreatedIfLogIsEmpty()
    {
        $filename = 'logs/testLogFileIsNotCreatedIfLogIsEmpty.txt';
        $log = new PewLog($filename);
        $log->dump();

        $this->assertFileNotExists($log->log_file());

        unset($log);
        $this->assertFileNotExists($filename);

    }

    public function testDebugMessagesAreLogged()
    {
        $filename = 'logs/testDebugMessagesAreLogged.txt';
        $log = new PewLog($filename);
        $log->debug('This is a debug message');
        $log->dump();

        $this->assertFileExists($filename);
        $logfilecontent = file_get_contents($filename);
        
        $this->assertTrue(strpos($logfilecontent, 'This is a debug message') !== false);
        $this->assertTrue(strpos($logfilecontent, '--DEBUG--') !== false);

        unlink($log->log_file());
    }

    public function testInfoMessagesAreLogged()
    {
        $log = new PewLog('logs/testInfoMessagesAreLogged.txt');
        $log->info('This is an info message');

        $log->dump();
        
        $this->assertFileExists($log->log_file());
        $logfilecontent = file_get_contents($log->log_file());
        $this->assertTrue(strpos($logfilecontent, 'This is an info message') !== false);
        $this->assertTrue(strpos($logfilecontent, '--INFO--') !== false);

        unlink($log->log_file());
    }

    public function testAlertMessagesAreLogged()
    {
        $log = new PewLog('logs/testAlertMessagesAreLogged.txt');
        $log->alert('This is an alert message');

        $log->dump();

        $this->assertFileExists($log->log_file());
        $logfilecontent = file_get_contents($log->log_file());
        $this->assertTrue(strpos($logfilecontent, 'This is an alert message') !== false);
        $this->assertTrue(strpos($logfilecontent, '--ALERT--') !== false);

        unlink($log->log_file());
    }

    public function testErrorMessagesAreLogged()
    {
        $log = new PewLog('logs/testErrorMessagesAreLogged.txt');
        $log->error('This is an error message');

        $log->dump();

        $this->assertFileExists($log->log_file());
        $logfilecontent = file_get_contents($log->log_file());
        $this->assertTrue(strpos($logfilecontent, 'This is an error message') !== false);
        $this->assertTrue(strpos($logfilecontent, '--ERROR--') !== false);

        unlink($log->log_file());
    }

    public function testFatalMessagesAreLogged()
    {
        $log = new PewLog('logs/testFatalMessagesAreLogged.txt');
        $log->fatal('This is a fatal message');

        $log->dump();

        $this->assertFileExists($log->log_file());
        $logfilecontent = file_get_contents($log->log_file());
        $this->assertTrue(strpos($logfilecontent, 'This is a fatal message') !== false);
        $this->assertTrue(strpos($logfilecontent, '--FATAL--') !== false);

        unlink($log->log_file());
    }

    public function testExistingLogFilesAreAppendedCorrectly()
    {
        $filename = 'logs/testExistingLogFilesAreAppendedCorrectly.txt';
        $logmessage = 'append test';
        $log = new PewLog($filename, '', '');

        $log->debug($logmessage);
        $log->dump();

        $fsize = filesize($filename);

        $log->debug($logmessage);
        $log->dump();

        $this->assertEquals($fsize * 2, filesize($log->log_file()));

        unlink($filename);
    }
}
