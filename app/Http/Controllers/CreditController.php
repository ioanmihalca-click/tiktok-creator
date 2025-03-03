<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use App\Models\CreditPackage;
use App\Services\CreditService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    /**
     * Afișează lista de pachete disponibile pentru achiziție
     */
    public function index()
    {
        $packages = CreditPackage::where('is_active', true)->get();
        $userCredit = Auth::user()?->userCredit; // Folosim operatorul null-safe

        return view('credits.index', [
            'packages' => $packages,
            'userCredit' => $userCredit
        ]);
    }

    public function checkout($id)
    {
        $package = CreditPackage::findOrFail($id);
        $user = Auth::user();

        if (!$user || !($user instanceof \App\Models\User)) {
            return redirect()->route('login');
        }

        $stripePriceId = $package->stripe_price_id;

        try {
            $checkoutSession = $user->checkout([$stripePriceId => 1], [
                'success_url' => route('credits.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('credits.cancel'),
                'metadata' => [
                    'package_id' => $package->id,
                    'credits' => $package->credits,
                ],
            ]);

            return redirect($checkoutSession->url);
        } catch (\Laravel\Cashier\Exceptions\IncompletePayment $e) { //Prindem exceptia specifica
            return redirect()->route(
                'cashier.payment',
                [$e->payment->id, 'redirect' => route('credits.index')] //Redirectionam catre o pagina unde se poate reincerca plata.
            );
        } catch (\Exception $e) { //Prindem restul exceptiilor.
            Log::error('Eroare la crearea sesiunii de checkout: ' . $e->getMessage());
            return back()->with('error', 'A apărut o eroare la procesarea plății. Te rugăm să încerci din nou.');
        }
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        try {
            $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                // Loghează dacă statusul nu este 'paid'
                Log::warning("Payment not completed for session ID: {$sessionId}, status: {$session->payment_status}");
                return redirect()->route('credits.cancel')->with('error', 'Plata nu a fost finalizată cu succes.');
            }

            $packageId = $session->metadata->package_id ?? null;
            $credits = $session->metadata->credits ?? null;

            if (!$packageId || !$credits) {
                // Loghează dacă lipsesc metadatele
                Log::error("Missing package_id or credits in metadata for session ID: {$sessionId}");
                return redirect()->route('credits.cancel')->with('error', 'A apărut o eroare la procesarea plății. (Missing metadata)');
            }

            $user = Auth::user();
            if (!$user || !($user instanceof \App\Models\User)) {
                Log::error('User not authenticated or incorrect type in success method for session ID: ' . $sessionId);
                return redirect()->route('credits.cancel')->with('error', 'A apărut o eroare la procesarea platii.');
            }

            $package = CreditPackage::find($packageId);
            if (!$package) {
                Log::error("Package with id {$packageId} not found for session ID: {$sessionId}");
                return redirect()->route('credits.cancel')->with('error', 'A apărut o eroare la procesarea plății. (Package not found)');
            }


            // Folosim CreditService
            $this->creditService->addCredits($user, $credits, 'purchase', $session->payment_intent, "Achizitie pachet {$package->name}");


            return view('credits.success', ['session_id' => $sessionId]);
        } catch (\Exception $e) {
            Log::error("Error retrieving session: {$sessionId} - " . $e->getMessage());
            return redirect()->route('credits.cancel')->with('error', 'A apărut o eroare la procesarea plății. Te rugăm să încerci din nou.');
        }
    }

    /**
     * Pagina de anulare după ce utilizatorul a anulat plata
     */
    public function cancel()
    {
        return view('credits.cancel');
    }

    public function history()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Folosește relația:
        $transactions = $user->creditTransactions()->orderBy('created_at', 'desc')->paginate(10);

        return view('credits.history', [
            'transactions' => $transactions
        ]);
    }
}
