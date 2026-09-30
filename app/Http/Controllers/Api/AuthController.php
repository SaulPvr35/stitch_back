<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Stripe\StripeClient;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => ['required', 'alpha_dash', 'min:3', 'max:60', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
        ]);

        $plan = Plan::where('slug', 'explorador-mensual')->where('active', true)->first();
        abort_unless($plan?->stripe_price_id && config('cashier.secret'), 503, 'Stripe no está configurado todavía.');

        try {
            $pendingId = DB::table('pending_registrations')->insertGetId([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $session = (new StripeClient(config('cashier.secret')))->checkout->sessions->create([
                'mode' => 'subscription',
                'line_items' => [['price' => $plan->stripe_price_id, 'quantity' => 1]],
                'customer_email' => $data['email'],
                'client_reference_id' => (string) $pendingId,
                'success_url' => config('app.frontend_url').'?checkout=success&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('app.frontend_url').'?checkout=cancelled',
            ]);

            DB::table('pending_registrations')->where('id', $pendingId)->update([
                'stripe_checkout_session_id' => $session->id,
                'updated_at' => now(),
            ]);

            return response()->json(['checkout_url' => $session->url], 202);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['message' => 'No se pudo iniciar el pago con Stripe.'], 502);
        }
    }

    public function completeCheckout(Request $request): JsonResponse
    {
        $data = $request->validate(['session_id' => ['required', 'string']]);
        $session = (new StripeClient(config('cashier.secret')))->checkout->sessions->retrieve($data['session_id']);

        if ($session->payment_status !== 'paid' || $session->status !== 'complete') {
            return response()->json(['message' => 'El pago todavía no está confirmado.'], 422);
        }

        $pending = DB::table('pending_registrations')->where('stripe_checkout_session_id', $session->id)->first();
        if (!$pending) {
            return response()->json(['message' => 'El registro pagado no existe o ya fue utilizado.'], 422);
        }

        $user = DB::transaction(function () use ($pending, $session) {
            $user = User::create([
                'name' => $pending->username,
                'username' => $pending->username,
                'email' => $pending->email,
                'password' => $pending->password,
                'stripe_id' => $session->customer,
            ]);
            $user->profile()->create(['level' => 1, 'base_stars' => 0, 'base_coins' => 0, 'avatar' => strtoupper(substr($user->name, 0, 1)), 'age_group' => '8-9']);
            DB::table('pending_registrations')->where('id', $pending->id)->delete();

            return $user;
        });

        return $this->tokenResponse($user, 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Las credenciales no son válidas.'], 422);
        }

        $user->profile()->firstOrCreate([], [
            'avatar' => strtoupper(substr($user->name, 0, 1)),
        ]);

        return $this->tokenResponse($user);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('profile'));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Sesión cerrada.']);
    }

    private function tokenResponse(User $user, int $status = 200): JsonResponse
    {
        return response()->json([
            'token' => $user->createToken('aventura-web')->plainTextToken,
            'user' => $user->load('profile'),
        ], $status);
    }
}