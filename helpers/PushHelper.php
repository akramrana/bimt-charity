<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace app\helpers;

use Google\Auth\Credentials\ServiceAccountCredentials;

/**
 * Description of PushHelper
 *
 * @author akram
 */
class PushHelper {

    //put your code here

    private function generateAccessToken() {
        try {
            $credentialsFilePath = \Yii::getAlias('@webroot').'/bimt-charity-firebase-adminsdk-fbsvc-03a9965c43.json';
            $credentials = new ServiceAccountCredentials(['https://www.googleapis.com/auth/firebase.messaging'], $credentialsFilePath);
            $token = $credentials->fetchAuthToken();
            return $accessToken = $token['access_token'];
        } catch (\Exception $e) {
            debugPrint($e->getMessage());
        }
    }

    public function sendPush($param) {
        $title = $param['title'];
        $body = $param['body'];
        $screen = $param['screen'];
        $targetId = !empty($param['id']) ? $param['id'] : "";

        $message = [
            'topic' => 'all',
            'data' => [
                'title' => (string) $title,
                'body' => (string) $body,
                'screen' => (string) $screen,
                'id' => (string) $targetId,
            ],
            'android' => [
                'priority' => 'high'
            ]
        ];

        return $this->sendMessage($message);
    }

    public function sendPushToUser($userId, $param) {
        $pushTokens = \app\models\DeviceTokens::find()
                ->select('push_token')
                ->where([
                    'user_id' => $userId,
                    'push_type' => 'fcm',
                ])
                ->andWhere(['not', ['push_token' => null]])
                ->andWhere(['<>', 'push_token', ''])
                ->distinct()
                ->column();

        if (empty($pushTokens)) {
            return [];
        }

        $title = $param['title'];
        $body = $param['body'];
        $screen = $param['screen'];
        $targetId = !empty($param['id']) ? $param['id'] : "";
        $accessToken = $this->generateAccessToken();
        $results = [];

        foreach ($pushTokens as $pushToken) {
            $message = [
                'token' => $pushToken,
                'data' => [
                    'title' => (string) $title,
                    'body' => (string) $body,
                    'screen' => (string) $screen,
                    'id' => (string) $targetId,
                ],
                'android' => [
                    'priority' => 'high'
                ]
            ];

            $results[$pushToken] = $this->sendMessage($message, $accessToken);
        }

        return $results;
    }

    private function sendMessage($message, $accessToken = null) {
        $token = !empty($accessToken) ? $accessToken : $this->generateAccessToken();
        $url = 'https://fcm.googleapis.com/v1/projects/bimt-charity/messages:send';

        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ];

        $payload = [
            'message' => $message
        ];

        $payload = json_encode($payload);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $result = curl_exec($ch);

        if ($result === false) {
            die('Curl failed: ' . curl_error($ch));
        }

        curl_close($ch);

        return $result;
    }
}
