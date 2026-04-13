<?php

test('guests are redirected to login when visiting dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated admin sees dashboard with metrics', function () {
    loginAs('Administrador');

    $response = $this->get('/dashboard');

    $response->assertOk();
    $response->assertSeeText('Hola,');
    $response->assertSeeText('Personal');
    $response->assertSeeText('Inventario');
});

test('dashboard shows quick access to users for admin', function () {
    loginAs('Administrador');

    $this->get('/dashboard')->assertSee(route('users.index'));
});

test('cashier does not see users quick access', function () {
    loginAs('Cajero');

    $response = $this->get('/dashboard');

    $response->assertOk();
    $response->assertDontSee(route('users.index'));
});
