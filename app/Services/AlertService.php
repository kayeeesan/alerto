<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Notification;
use App\Models\Threshold;
use App\Models\Sensor;
use App\Events\AlertCreated; 
use Illuminate\Support\Str;

class AlertService
{
    public function createAlertIfNeeded(Threshold $threshold)
    {
        $sensor = $threshold->sensorable;
        $river = $sensor->river;
        
        if (!$river) {
            return;
        }

        $riverId = $river->id;
        $statuses = [];

        // Current values
        $currentWater = $sensor->device_water_level;
        $currentRain = $sensor->device_rain_amount;

        // Previous values from sensor model
        $previousWater = $sensor->previous_water_level;
        $previousRain = $sensor->previous_rain_amount;

        // --- Water Level Alert ---
        if ($currentWater !== null && $currentWater != $previousWater) {
            $waterStatus = $this->checkWaterLevelStatus($currentWater, $threshold, $river);
            if ($waterStatus) {
                $statuses[] = $waterStatus['type'];
                $this->createAlertAndNotify(
                    $threshold,
                    $riverId,
                    $waterStatus['details'],
                    $waterStatus['type'],
                    'water_level'
                );
                // Update previous value
                $sensor->previous_water_level = $currentWater;
            }
        }

        // --- Rain Alert ---
        if ($currentRain !== null && $currentRain != $previousRain) {
            $rainStatus = $this->checkRainStatus($currentRain, $river);
            if ($rainStatus) {
                $statuses[] = $rainStatus['type'];
                $this->createAlertAndNotify(
                    $threshold,
                    $riverId,
                    $rainStatus['details'],
                    $rainStatus['type'],
                    'rain'
                );
                // Update previous value
                $sensor->previous_rain_amount = $currentRain;
            }
        }

        // Update sensor status if any alerts were created
        $this->updateSensorStatus($sensor, $statuses);
        
        // Save any changes to previous values or status
        if (!empty($statuses)) {
            $sensor->save();
        }
    }

    private function checkWaterLevelStatus($waterLevel, $threshold, $river)
    {
        if ($waterLevel >= $threshold->one_hundred_percent) {
            return [
                'details' => $river->name . ' is at critical water level: ' . $waterLevel . 'm',
                'type' => 'critical'
            ];
        } elseif ($waterLevel >= $threshold->eighty_percent) {
            return [
                'details' => $river->name . ' is on alert. Water level: ' . $waterLevel . 'm',
                'type' => 'alert'
            ];
        } elseif ($waterLevel >= $threshold->sixty_percent) {
            return [
                'details' => 'Please monitor ' . $river->name . '. Water level: ' . $waterLevel . 'm',
                'type' => 'warning'
            ];
        }
        
        return null;
    }

    private function checkRainStatus($rainAmount, $river)
    {
        if ($rainAmount >= 30) {
            return [
                'details' => 'Evacuation Alert! Rain amount is critical: ' . $rainAmount . 'mm at ' . $river->name,
                'type' => 'critical'
            ];
        } elseif ($rainAmount >= 15) {
            return [
                'details' => 'Rain Alert: ' . $rainAmount . 'mm at ' . $river->name,
                'type' => 'alert'
            ];
        } elseif ($rainAmount >= 7.5) {
            return [
                'details' => 'Rain Monitoring: ' . $rainAmount . 'mm at ' . $river->name,
                'type' => 'warning'
            ];
        }
        
        return null;
    }

    private function updateSensorStatus($sensor, $statuses)
    {
        if (empty($statuses)) {
            return;
        }

        if (in_array('critical', $statuses)) {
            $sensor->status = 'critical';
        } elseif (in_array('alert', $statuses)) {
            $sensor->status = 'alert';
        } elseif (in_array('warning', $statuses)) {
            $sensor->status = 'warning';
        }
    }

    private function createAlertAndNotify(Threshold $threshold, $riverId, $details, $type, $alertType)
    {
        // Create the alert
        $alert = Alert::create([
            'threshold_id' => $threshold->id,
            'details' => $details,
            'status' => 'pending',
            'expired_at' => now()->addMinutes(30),
            'alert_type' => $alertType,
        ]);

        // Get users assigned to this river
        $usersByRiver = \App\Models\User::whereHas('staff', function ($query) use ($riverId) {
            $query->where('river_id', $riverId);
        })->get();

        // Get admin users
        $adminUsers = \App\Models\User::whereHas('roles', function ($query) {
            $query->where('slug', 'administrator');
        })->get();

        // Merge and remove duplicates
        $usersToNotify = $usersByRiver->merge($adminUsers)->unique('id');

        // Associate users to alert
        $pivotData = $usersByRiver->pluck('id')->mapWithKeys(function ($userId) {
            return [
                $userId => ['uuid' => (string) Str::uuid()],
            ];
        });

        $alert->users()->attach($pivotData);

        // Create notifications
        foreach ($usersToNotify as $user) {
            $notification = Notification::create([
                'user_id' => $user->id,
                'river_id' => $riverId,
                'text' => $details,
                'type' => $type,
                'alert_type' => $alertType,
                'alert_id' => $alert->id,
            ]);

            // Broadcast once per user
            broadcast(new AlertCreated($notification));
        }
    }

}