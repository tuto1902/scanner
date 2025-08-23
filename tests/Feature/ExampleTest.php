<?php

test('returns a successful response', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('home route redirects to login when not authenticated', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});