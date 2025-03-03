<?php

namespace App\Http\Controllers;

use App\Models\CreditPackage;
use App\Models\CreditTransaction;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Afișează lista de pachete disponibile pentru achiziție
     */
    public function index()
    {
        $packages = CreditPackage::where('is_active', true)->get();
        $userCredit = Auth::user()->userCredit;

        return view('credits.index', [
            'packages' => $packages,
            'userCredit' => $userCredit
        ]);
    }

    /**
     * Inițiază procesul de checkout pentru un pachet
     */
    public function checkout($id)
    {
        $package = CreditPackage::findOrFail($id);
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Explicit cast to User model
        $user = User::find($user->id);
        $session = $this->stripeService->createCheckoutSession($user, $package);

        return redirect($session->url);
    }

    /**
     * Pagina de succes după o plată reușită
     */
    public function success(Request $request)
    {
        return view('credits.success', [
            'session_id' => $request->session_id
        ]);
    }

    /**
     * Pagina de anulare după ce utilizatorul a anulat plata
     */
    public function cancel()
    {
        return view('credits.cancel');
    }

    /**
     * Afișează istoricul tranzacțiilor utilizatorului
     */
    public function history()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $transactions = CreditTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('credits.history', [
            'transactions' => $transactions
        ]);
    }
}
