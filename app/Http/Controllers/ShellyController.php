<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\GenericProvider;


class ShellyController extends Controller
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new GenericProvider([
            'clientId' => env('SHELLY_CLIENT_ID'),
            'clientSecret' => env('SHELLY_CLIENT_SECRET'),
            'redirectUri' => env('SHELLY_REDIRECT_URI'),
            'urlAuthorize' => env('SHELLY_AUTHORIZATION_URL'),
            'urlAccessToken' => env('SHELLY_TOKEN_URL'),
            'urlResourceOwnerDetails' => ''
        ]);
    }

    public function redirectToProvider()
    {
        $authorizationUrl = $this->provider->getAuthorizationUrl();
        session(['oauth2state' => $this->provider->getState()]);
        return redirect($authorizationUrl);
    }

    public function handleProviderCallback(Request $request)
    {
        if (empty($request->get('state')) || ($request->get('state') !== session('oauth2state'))) {
            exit('Invalid state');
        }

        $accessToken = $this->provider->getAccessToken('authorization_code', [
            'code' => $request->get('code')
        ]);

        // Store the access token for future API calls
        session(['access_token' => $accessToken->getToken()]);
        return redirect()->route('home');
    }
}
