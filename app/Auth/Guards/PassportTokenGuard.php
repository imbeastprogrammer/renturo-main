<?php

namespace App\Auth\Guards;

use Laravel\Passport\Guards\TokenGuard;
use Laravel\Passport\Token;

class PassportTokenGuard extends TokenGuard
{
    /**
     * Get the user for the incoming request.
     *
     * @return mixed
     */
    public function user()
    {
        // ALWAYS re-authenticate - don't use cached user
        // This ensures we check token revocation on every request
        $this->user = null;
        
        return parent::user();
    }

    /**
     * Authenticate the incoming request via the Bearer token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function authenticateViaBearerToken($request)
    {
        if (! $psr = $this->getPsrRequestViaBearerToken($request)) {
            return;
        }

        $client = $this->clients->findActive(
            $psr->getAttribute('oauth_client_id')
        );

        if (! $client ||
            ($client->provider &&
             $client->provider !== $this->provider->getProviderName())) {
            return;
        }


        // Get the token instance from database with a fresh query every time
        $tokenId = $psr->getAttribute('oauth_access_token_id');
        
        // Force a fresh query from database - don't use cached token
        $token = \Laravel\Passport\Token::where('id', $tokenId)->first();

        // Check if token exists
        if (! $token) {
            return null;
        }

        // Check if token is revoked or expired
        if ($token->revoked || $token->expires_at->isPast()) {
            return null;
        }

        // Get the user
        $user = $this->provider->retrieveById(
            $psr->getAttribute('oauth_user_id') ?: null
        );

        if (! $user) {
            return;
        }

        return $user->withAccessToken($token);
    }
}
