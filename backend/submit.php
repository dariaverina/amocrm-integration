<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/AmoCRMClient.php';
require_once __DIR__ . '/CustomFieldUtils.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$clientId = $_ENV['CLIENT_ID'] ?? null;
$clientSecret = $_ENV['CLIENT_SECRET'] ?? null;
$redirectUri = $_ENV['REDIRECT_URI'] ?? null;
$baseDomain = $_ENV['BASE_DOMAIN'] ?? null;
$accessTokenStr = $_ENV['ACCESS_TOKEN'] ?? null;
$refreshTokenStr = $_ENV['REFRESH_TOKEN'] ?? null;
$timeSpentOnSiteFieldName = $_ENV['TIME_SPENT_ON_SITE_FIELD_NAME'] ?? 'TimeSpentOnSite';

if (!$clientId || !$clientSecret || !$redirectUri || !$baseDomain || !$accessTokenStr || !$refreshTokenStr) {
    echo json_encode(['status' => 'error', 'message' => 'Missing environment variables']);
    exit;
}

try {
    $amoCRMClient = new AmoCRMClient($clientId, $clientSecret, $redirectUri, $accessTokenStr, $refreshTokenStr, $baseDomain);
} catch (Exception $e) {
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

    $timeSpentOnSiteFieldId = CustomFieldUtils::getCustomFieldIdByName($amoCRMClient->getApiClient(), $timeSpentOnSiteFieldName);

    if ($timeSpentOnSiteFieldId === null) {
        echo json_encode(['status' => 'error', 'message' => 'Custom field "' . $timeSpentOnSiteFieldName . '" not found']);
        exit;
    }

    try {
        $contact = $amoCRMClient->createContact($name, $phone, $email, $timeSpentOnSiteFieldId, $timeSpentOnSite);
        $lead = $amoCRMClient->createLead('New Deal', $price, $contact);

        echo json_encode(['status' => 'success', 'contact_id' => $contact->getId(), 'lead_id' => $lead->getId()]);
    } catch (AmoCRMApiException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
}
?>
