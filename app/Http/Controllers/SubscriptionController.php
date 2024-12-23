<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function createSetupIntent(Request $request)
    {
        $setupIntent = $request->user()->createSetupIntent();

        return response()->json([
            'clientSecret' => $setupIntent->client_secret,
        ]);
    }

    public function subscribe(Request $request)
    {
        $paymentMethod = $request->paymentMethod;

        $request->user()->newSubscription('default', 'price_xxxx')->create($paymentMethod);

        return response()->json(['message' => 'Subscription created successfully']);
    }
}
