<?php

require_once 'Delhivery.php';

class DeliveryManager {
    private $config;
    private $conn;
    private $delhivery;
    private $isConfigured = false;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->loadConfig();
        $this->initializeDelhivery();
    }

    private function loadConfig() {
        $this->config = [];

        $sql = "SELECT config_key, config_value FROM shipping_config";
        $result = mysqli_query($this->conn, $sql);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $this->config[$row['config_key']] = $row['config_value'];
            }
        }
    }

    private function initializeDelhivery() {
        // Initialize Delhivery if configured
        if (!empty($this->config['delhivery_api_key'])) {
            try {
                $this->delhivery = new Delhivery($this->config['delhivery_api_key']);
                $this->isConfigured = true;
            } catch (Exception $e) {
                error_log("Failed to initialize Delhivery: " . $e->getMessage());
                $this->isConfigured = false;
            }
        }
    }
    
    public function createOrder($orderData) {
        if (!$this->isConfigured) {
            throw new Exception("Delhivery is not configured. Please check your API settings.");
        }

        try {
            $result = $this->delhivery->createOrder($orderData);

            // Log successful order creation
            $this->logDeliveryAttempt($orderData['order_id'], 'delhivery', 'create_order', 'success', $result);

            return $result;
        } catch (Exception $e) {
            // Log failed attempt
            $this->logDeliveryAttempt($orderData['order_id'], 'delhivery', 'create_order', 'failed', $e->getMessage());
            throw $e;
        }
    }

    public function trackShipment($trackingId) {
        if (!$this->isConfigured) {
            throw new Exception("Delhivery is not configured. Please check your API settings.");
        }

        return $this->delhivery->trackShipment($trackingId);
    }

    public function cancelOrder($orderId) {
        if (!$this->isConfigured) {
            throw new Exception("Delhivery is not configured. Please check your API settings.");
        }

        return $this->delhivery->cancelOrder($orderId);
    }

    public function getAvailableProviders() {
        return $this->isConfigured ? ['delhivery'] : [];
    }

    public function isProviderAvailable($provider = 'delhivery') {
        return $provider === 'delhivery' && $this->isConfigured;
    }

    public function isDelhiveryConfigured() {
        return $this->isConfigured;
    }
    

    
    private function logDeliveryAttempt($orderId, $provider, $action, $status, $response) {
        $sql = "INSERT INTO delivery_logs (order_id, provider, action, status, response, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        if ($stmt) {
            $responseJson = is_array($response) ? json_encode($response) : $response;
            mysqli_stmt_bind_param($stmt, 'sssss', $orderId, $provider, $action, $status, $responseJson);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    
    public function validateConfiguration() {
        if (!$this->isConfigured) {
            return ['delhivery' => ['status' => 'not_configured', 'message' => 'Delhivery API key not configured']];
        }

        try {
            // Test the Delhivery connection
            $testResult = $this->delhivery->testConnection();
            return ['delhivery' => ['status' => 'success', 'message' => 'Connection successful']];
        } catch (Exception $e) {
            return ['delhivery' => ['status' => 'failed', 'message' => $e->getMessage()]];
        }
    }

    public function getDelhiveryConfig() {
        $configKeys = [
            'delhivery_api_key',
            'delhivery_client_name',
            'delhivery_return_address',
            'delhivery_return_city',
            'delhivery_return_state',
            'delhivery_return_pincode',
            'delhivery_return_phone',
            'delhivery_seller_name',
            'delhivery_seller_address',
            'delhivery_seller_gst'
        ];

        $config = [];
        foreach ($configKeys as $key) {
            $config[$key] = $this->config[$key] ?? null;
        }

        return $config;
    }

    public function getGeneralConfig() {
        return [
            'default_package_weight' => $this->config['default_package_weight'] ?? '0.5',
            'default_dimensions' => $this->config['default_dimensions'] ?? json_encode(['length' => 10, 'width' => 10, 'height' => 10]),
            'auto_create_shipments' => $this->config['auto_create_shipments'] ?? '0'
        ];
    }
}
