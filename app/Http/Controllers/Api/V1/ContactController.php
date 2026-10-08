<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Http\Resources\ContactResource;

class ContactController extends Controller
{
    public function index(IndexContactRequest $request)
    {
        $query = Contact::with(['category', 'tags']);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                  ->orWhere('last_name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhereRaw('CONCAT(first_name, last_name) like ?', ["%{$keyword}%"])
                  ->orWhereRaw('CONCAT(last_name, first_name) like ?', ["%{$keyword}%"]);
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $perPage = $request->input('per_page', 20);
        $contacts = $query->latest()->paginate($perPage);

        return ContactResource::collection($contacts);
    }

    public function show($id)
    {
        $contact = Contact::with(['category', 'tags'])->find($id);

        if (!$contact) {
            return response()->json([
                'error' => 'お問い合わせが見つかりませんでした。',
            ], 404);
        }

        return new ContactResource($contact);
    }

    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();
        $contact = Contact::create($validated);

        if ($request->has('tag_ids') && is_array($request->tag_ids)) {
            $contact->tags()->attach($request->tag_ids);
        }

        $contact->load(['category', 'tags']);

        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateContactRequest $request, $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json([
                'error' => 'お問い合わせが見つかりませんでした。',
            ], 404);
        }

        $validated = $request->validated();
        $contact->update($validated);

        if ($request->has('tag_ids')) {
            $tagIds = is_array($request->tag_ids) ? $request->tag_ids : [];
            $contact->tags()->sync($tagIds);
        }

        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }

    public function destroy($id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json([
                'error' => 'お問い合わせが見つかりませんでした。',
            ], 404);
        }

        $contact->delete();

        return response()->json(null, 204);
    }
}