<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require __DIR__ . '/vendor/autoload.php';

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\CheckboxCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\CheckboxCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\CheckboxCustomFieldValueModel;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use League\OAuth2\Client\Token\AccessToken;
use AmoCRM\Exceptions\AmoCRMApiException;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$clientId = $_ENV['CLIENT_ID'] ?? null;
$clientSecret = $_ENV['CLIENT_SECRET'] ?? null;
$redirectUri = $_ENV['REDIRECT_URI'] ?? null;
$subdomain = $_ENV['SUBDOMAIN'] ?? null;
$baseDomain = $_ENV['BASE_DOMAIN'] ?? null;
$accessTokenStr = $_ENV['ACCESS_TOKEN'] ?? null;
$refreshTokenStr = $_ENV['REFRESH_TOKEN'] ?? null;

if (!$clientId || !$clientSecret || !$redirectUri || !$baseDomain || !$accessTokenStr || !$refreshTokenStr) {
    echo json_encode(['status' => 'error', 'message' => 'Missing environment variables']);
    exit;
}

try {
    $accessToken = new AccessToken([
        'access_token' => $accessTokenStr,
        'refresh_token' => $refreshTokenStr,
        'expires' => time() + 3600,
        'baseDomain' => $baseDomain
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid access token configuration']);
    exit;
}

try {
    $apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
    $apiClient->setAccessToken($accessToken)
              ->setAccountBaseDomain($baseDomain)
              ->onAccessTokenRefresh(
                  function (AccessToken $accessToken, string $baseDomain) {
                  }
              );
} catch (AmoCRMApiException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to initialize API client: ' . $e->getMessage()]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['name'], $data['price'], $data['phone'], $data['email'])) {
    $name = $data['name'];
    $price = $data['price'];
    $phone = $data['phone'];
    $email = $data['email'];
    $timeSpentOnSite = isset($data['timeSpentOnSite']) ? $data['timeSpentOnSite'] : false;

    try {
        $contact = new ContactModel();
        $contact->setName($name);

        $phoneField = new MultitextCustomFieldValuesModel();
        $phoneField->setFieldCode('PHONE');
        $phoneField->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())
                        ->setValue($phone)
                )
        );

        $emailField = new MultitextCustomFieldValuesModel();
        $emailField->setFieldCode('EMAIL');
        $emailField->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())
                        ->setValue($email)
                )
        );

        $checkboxCustomFieldValuesModel = new CheckboxCustomFieldValuesModel();
        $checkboxCustomFieldValuesModel->setFieldId(837055); 
        $checkboxCustomFieldValuesModel->setValues(
            (new CheckboxCustomFieldValueCollection())
                ->add((new CheckboxCustomFieldValueModel())->setValue($timeSpentOnSite ? 1 : 0))
        );

        $customFieldsCollection = new CustomFieldsValuesCollection();
        $customFieldsCollection->add($checkboxCustomFieldValuesModel);
        $customFieldsCollection->add($phoneField);
        $customFieldsCollection->add($emailField);

        $contact->setCustomFieldsValues($customFieldsCollection);
        $addedContact = $apiClient->contacts()->addOne($contact);

        $lead = new LeadModel();
        $lead->setName('New Deal')
             ->setPrice($price)
             ->setContacts((new ContactsCollection())->add($addedContact));
        $addedLead = $apiClient->leads()->addOne($lead);

        echo json_encode(['status' => 'success', 'contact_id' => $addedContact->getId(), 'lead_id' => $addedLead->getId()]);
    } catch (AmoCRMApiException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
}
