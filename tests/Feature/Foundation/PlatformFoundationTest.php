<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\MassAssignmentException;

it('sends baseline security headers and a request id', function () {
    $response = $this->get('/');

    $response->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

    expect($response->headers->get('X-Request-Id'))->toMatch('/^[A-Za-z0-9\-_.]{8,64}$/');
});

it('ignores unsafe client-supplied request ids', function () {
    $response = $this->get('/', ['X-Request-Id' => "bad\nheader<script>"]);

    expect($response->headers->get('X-Request-Id'))->not->toContain('script');
});

it('reports health without leaking configuration', function () {
    $response = $this->getJson(route('health'));

    $response->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('checks.database', 'ok')
        ->assertJsonPath('checks.cache', 'ok')
        ->assertJsonPath('checks.storage', 'ok');

    $body = $response->getContent();
    expect($body)->not->toContain((string) config('database.connections.mysql.database'))
        ->and($body)->not->toContain((string) config('app.key'));
});

it('renders every legal page with a draft notice', function (string $page) {
    $this->get(route('legal.show', $page))
        ->assertOk()
        ->assertSee('Draft for legal review');
})->with(['terms', 'privacy', 'acceptable-use', 'refunds', 'ai-safety']);

it('returns a branded 404 for unknown pages', function () {
    $this->get('/legal/not-a-page')->assertNotFound()->assertSee('Page not found');
});

it('signs out a suspended user on their next request', function () {
    $student = userWithRole(Role::Student->value);
    $this->actingAs($student);

    $student->forceFill(['status' => User::STATUS_SUSPENDED])->save();

    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->assertGuest();
});

it('does not allow status to be mass-assigned', function () {
    $user = User::factory()->create();
    $user->fill(['status' => User::STATUS_SUSPENDED]);
})->throws(MassAssignmentException::class);
