<?php

namespace MVC\controller;

use MVC\middleware\AuthMiddleware;
use MVC\model\User;
use MVC\model\Property;
use MVC\model\PropertyTransfer;
use Exception;

class PropertyController extends Controller
{
    public function ownedassets(): bool|array|string
    {
        AuthMiddleware::requireUser();

        try {
            $userData = User::findByUsername($_SESSION['Username']);

            if (!$userData) {
                throw new Exception("User not found");
            }

            $userId = $userData['User_ID'];

            $ownedProperties = Property::findByOwner((int)$userId);

            // Calculate statistics
            $totalAssets = count($ownedProperties);
            $landCount = 0;
            $apartmentCount = 0;

            foreach ($ownedProperties as $property) {
                $propertyType = strtolower((string)($property['type'] ?? ''));
                if ($propertyType === '') {
                    $propertyType = (empty($property['apartment_number']) || $property['apartment_number'] === '-') ? 'land' : 'apartment';
                }
                if ($propertyType === 'land') {
                    $landCount++;
                } else {
                    $apartmentCount++;
                }
            }

            $ordersCount = PropertyTransfer::countForUser((int)$userId, (string)$userData['User_NationalID']);

            return $this->render('home.User.ownedassets', [
                'ownedProperties' => $ownedProperties,
                'totalAssets' => $totalAssets,
                'ordersCount' => $ordersCount,
                'landCount' => $landCount,
                'apartmentCount' => $apartmentCount
            ]);
        } catch (Exception $e) {
            return $this->render('home.User.ownedassets', [
                'error' => $e->getMessage(),
                'ownedProperties' => [],
                'totalAssets' => 0,
                'ordersCount' => 0,
                'landCount' => 0,
                'apartmentCount' => 0
            ]);
        }
    }

    public function sell(): bool|array|string
    {
        AuthMiddleware::requireUser();
        try {
            $userData = User::findByUsername($_SESSION['Username']);
            if (!$userData) {
                throw new Exception('User not found');
            }

            // Sell page needs the user's properties (any status filtering can be added later).
            $properties = \MVC\model\Property::search(['owner_id' => (int)$userData['User_ID']]);
        } catch (Exception $e) {
            $properties = [];
            $error = $e->getMessage();
        }

        return $this->render('home.User.sell', [
            'properties' => $properties,
            'error' => $error ?? null
        ]);
    }

    public function allProperties(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        return $this->render('home.Employee.allProperties');
    }

    public function PropertyById(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        return $this->render('home.Employee.PropertyById');
    }

    public function PropertyCount(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        return $this->render('home.Employee.PropertyCount');
    }

    public function PropertyInfo(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        return $this->render('home.Employee.PropertyInfo');
    }
}

