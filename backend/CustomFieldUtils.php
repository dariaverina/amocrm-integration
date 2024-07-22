<?php

use AmoCRM\Exceptions\AmoCRMApiException;

class CustomFieldUtils
{
    public static function getCustomFieldIdByName($apiClient, $fieldName, $entityType = 'contacts')
    {
        try {
            $customFieldsService = $apiClient->customFields($entityType);
            $customFields = $customFieldsService->get();
            foreach ($customFields as $customField) {
                if ($customField->getName() === $fieldName) {
                    return $customField->getId();
                }
            }
        } catch (AmoCRMApiException $e) {
            throw $e;
        }
        return null;
    }
}
?>
