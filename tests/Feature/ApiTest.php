<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Branch;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * the API Test will test the 
 */
class ApiTest extends TestCase
{
    /**
     * Client Credentials Grant Tokens Test
     *
     * @return token granted
     */
    public function testGetClientCredentialToken()
    {
        $response = $this->json('POST', '/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => env('CLIENT_CREDENTIAL_ID'),
            'client_secret' => env('CLIENT_CREDENTIAL_SECRET'),
            'scope' => '*'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token_type', 'expires_in', 'access_token']);
    }

    /**
     * @depends testGetClientCredentialToken
     */
    public function testApiLogin()
    {
        $existingUser = User::where('email', 'admin@admin.com')->first();
        if (empty($existingUser)) {
            $user = factory(User::class)->create([
                'email' => 'admin@admin.com',
            ]);
        }

        $body = [
            'grant_type' => 'password',
            'client_id' => env('PASSWORD_GRANT_ID'),
            'client_secret' => env('PASSWORD_GRANT_SECRET'),
            'username' => 'admin@admin.com',
            'password' => 'secret',
            'scope' => '',
        ];
        $response = $this->json('POST', '/oauth/token', $body);
        $response->assertStatus(200)
            ->assertJsonStructure(['token_type', 'expires_in', 'access_token', 'refresh_token']);

        $jsonContent = $response->getContent();
        $contentArr = json_decode($jsonContent, true);
        $token = $contentArr['access_token'];

        return $token;
    }

    /**
     * @depends testApiLogin
     */
    public function testUseToken(string $token)
    {
        Branch::truncate();
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];

        $createData = [
            'branch_name' => 'Test Branch',
        ];

        $response = $this->withHeaders($headers)->json('POST', route('branch.create'), $createData);

        $response->assertStatus(201);

        factory(Branch::class, 20)->create();

        $testId = rand(1, 20);
        $response2 = $this->withHeaders($headers)->json('GET', route('branch.view', ['id' => $testId]));
        $response2->assertStatus(200);


        $updateData = [
            'branch_name' => 'Test Branch Updated Name',
        ];

        $response = $this->withHeaders($headers)->json('PATCH', route('branch.update', ['id' => $testId]), $updateData);
        $response->assertStatus(200);

        Branch::truncate();
    }
}
