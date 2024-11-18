<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Http\Controllers;

use App\Http\Requests\ListShareRequest;
use App\Http\Resources\ListShareResource;
use App\Models\ListShare;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ListShareController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', ListShare::class);

        return ListShareResource::collection(ListShare::all());
    }

    public function store(ListShareRequest $request)
    {
        $this->authorize('create', ListShare::class);

        return new ListShareResource(ListShare::create($request->validated()));
    }

    public function show(ListShare $listShare)
    {
        $this->authorize('view', $listShare);

        return new ListShareResource($listShare);
    }

    public function update(ListShareRequest $request, ListShare $listShare)
    {
        $this->authorize('update', $listShare);

        $listShare->update($request->validated());

        return new ListShareResource($listShare);
    }

    public function destroy(ListShare $listShare)
    {
        $this->authorize('delete', $listShare);

        $listShare->delete();

        return response()->json();
    }
}
