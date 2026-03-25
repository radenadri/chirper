<?php

namespace Tests\Feature;

use Tests\TestCase;

class HelloFileTest extends TestCase
{
    public function test_hello_markdown_fixture_exists_with_sample_data(): void
    {
        $path = base_path('HELLO.md');
        $contents = file_get_contents($path);

        $this->assertFileExists($path);
        $this->assertIsString($contents);
        $this->assertStringContainsString('Sample data for testing.', $contents);
        $this->assertStringContainsString('| 1  | alpha | ready   |', $contents);
    }
}
