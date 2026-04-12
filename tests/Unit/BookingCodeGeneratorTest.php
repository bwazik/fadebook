<?php

declare(strict_types=1);

use App\Services\BookingCodeGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('generates a 6-character booking code', function () {
    $generator = new BookingCodeGenerator;
    $code = $generator->generate();

    expect($code)->toHaveLength(6);
});

it('generates codes using only the valid charset', function () {
    $validCharset = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $generator = new BookingCodeGenerator;

    for ($i = 0; $i < 50; $i++) {
        $code = $generator->generate();
        foreach (str_split($code) as $char) {
            expect(str_contains($validCharset, $char))->toBeTrue(
                "Character '{$char}' is not in the valid charset"
            );
        }
    }
});

it('generates codes with no ambiguous characters', function () {
    $ambiguous = ['O', '0', 'I', '1'];
    $generator = new BookingCodeGenerator;

    for ($i = 0; $i < 50; $i++) {
        $code = $generator->generate();
        foreach ($ambiguous as $char) {
            expect(str_contains($code, $char))->toBeFalse(
                "Code '{$code}' contains ambiguous character '{$char}'"
            );
        }
    }
});
