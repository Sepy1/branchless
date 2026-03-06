<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BranchlessGenerateController extends Controller
{
    /**
     * Proxy a generate request to the external branchless server.
     */
    public function generate(Request $request, $kode)
    {
        // remote service expects the path /generate/{kode}
        $endpoint = "http://192.176.1.10:5000/generate/{$kode}";

        try {
            // forward the request body if present; otherwise send empty body
            $payload = $request->all();

            $resp = Http::withHeaders([
                'X-API-KEY' => 'jateng!@#',
            ])->post($endpoint, $payload);

            $body = $resp->body();
            $status = $resp->status();
            $contentType = $resp->header('Content-Type', 'text/plain');

            return response($body, $status)->header('Content-Type', $contentType);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Proxy request failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
