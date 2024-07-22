<?php

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

class AmoCRMClient
{
    private $apiClient;

    public function __construct($clientId, $clientSecret, $redirectUri, $accessTokenStr, $refreshTokenStr, $baseDomain)
    {
        $accessToken = new AccessToken([
            'access_token' => $accessTokenStr,
            'refresh_token' => $refreshTokenStr,
            'expires' => time() + 3600,
            'baseDomain' => $baseDomain
        ]);

        $this->apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
        $this->apiClient->setAccessToken($accessToken)
                        ->setAccountBaseDomain($baseDomain)
                        ->onAccessTokenRefresh(function (AccessToken $accessToken, string $baseDomain) {
                        });
    }

    public function getApiClient()
    {
        return $this->apiClient;
    }

    public function createContact($name, $phone, $email, $timeSpentOnSiteFieldId, $timeSpentOnSite)
    {
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
        $checkboxCustomFieldValuesModel->setFieldId($timeSpentOnSiteFieldId);
        $checkboxCustomFieldValuesModel->setValues(
            (new CheckboxCustomFieldValueCollection())
                ->add((new CheckboxCustomFieldValueModel())->setValue($timeSpentOnSite ? 1 : 0))
        );

        $customFieldsCollection = new CustomFieldsValuesCollection();
        $customFieldsCollection->add($checkboxCustomFieldValuesModel);
        $customFieldsCollection->add($phoneField);
        $customFieldsCollection->add($emailField);

        $contact->setCustomFieldsValues($customFieldsCollection);
        return $this->apiClient->contacts()->addOne($contact);
    }

    public function createLead($name, $price, $contact)
    {
        $lead = new LeadModel();
        $lead->setName($name)
             ->setPrice($price)
             ->setContacts((new ContactsCollection())->add($contact));
        return $this->apiClient->leads()->addOne($lead);
    }
}
?>
