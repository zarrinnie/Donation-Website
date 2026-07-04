<?php

namespace App\Http\Controllers;

use App\Actions\Doku\ProcessDokuNotificationAction;
use App\Actions\Doku\VerifyDokuWebhookSignatureAction;
use App\DTOs\Doku\WebhookNotificationData;
use App\Exceptions\Doku\InvalidDokuSignatureException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuWebhookController extends Controller
{
    /**
     * Server-to-server DOKU payment notification. Not a Livewire component
     * (no session/CSRF for a gateway callback) and not an Action (HTTP
     * concerns live here; business logic is delegated straight away).
     */
    public function __invoke(
        Request $request,
        VerifyDokuWebhookSignatureAction $verify,
        ProcessDokuNotificationAction $process,
    ): JsonResponse {
        try {
            $verify->execute($request);
        } catch (InvalidDokuSignatureException $e) {
            Log::warning('doku.webhook.invalid_signature', ['message' => $e->getMessage()]);

            return response()->json(['status' => 'invalid signature'], 401);
        }

        $process->execute(WebhookNotificationData::fromRequest($request));

        return response()->json(['status' => 'ok']);
    }
}
