<?php

use Application\Model\Role;

/**
 * List role
 * GET /
 */
$this->get('/', function() {

    $roles = Role::all();

    return $this->render('roles/list', [
        'roles' => $roles
    ]);
});


/**
 * Show create role page
 * GET /new
 */
$this->get('/new', function() {

    return $this->render('roles/new');

});


/**
 * Create role
 * POST /
 */
$this->post('/', function() {

    $role = new Role();

    $validator = $this->validator->create([
        'name' => ['required', 'unique' => $role]
    ]);

    $data = $this->params->only('name')->trim();

    if (!$validator->validate($data)) {
        return $validator;
    }

    return $role->fromArray($data)->save();


});


/**
 * Show edit role page
 * GET /::id/edit
 */
$this->get('/::id/edit', function($id) {

    $role = Role::first($id)?: $this->notFound();

    return $this->render('roles/edit', [
        'role' => $role
    ]);

});


/**
 * Update role
 * PUT /::id
 */
$this->put('/::id', function($id) {


    $role = Role::first($id);
    $role || $this->notFound();

    $validator = $this->validator->create([
        'name' => ['required', 'unique' => $role]
    ]);

    $data = $this->params->only('name')->trim();

    if (!$validator->validate($data)) {
        return $validator;
    }

    return $role->fromArray($data)->save();

});


/**
 * Delete role
 * DELETE /::id
 */
$this->delete('/::id', function($id) {

    $role = Role::first($id)?: $this->notFound();

    return $role->delete();

});