<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:subscribers,email'
        ]);

        Subscriber::create($validated);

        return back()->with('success', 'Mulțumim pentru înscriere! Te vom anunța când aplicația este gata.');
    }
}
