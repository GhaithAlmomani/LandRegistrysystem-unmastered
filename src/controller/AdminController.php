<?php

namespace MVC\controller;

use MVC\middleware\AuthMiddleware;
use MVC\model\Property;
use MVC\model\PropertyTransfer;
use MVC\model\User;

class AdminController extends Controller
{
    public function adminPortal(): bool|array|string
    {
        AuthMiddleware::requireAdmin();

        // High-level system totals (same metrics used in Admin dashboard).
        $total_users = User::countAll();
        $total_assets = Property::countAll();
        $total_orders = PropertyTransfer::countAll();

        $employee_status_list = User::listEmployeesByLastLogin(12);

        $pending_transfer_reviews = PropertyTransfer::countByStatus(PropertyTransfer::STATUS_PENDING);
        $properties_pending_transfer = Property::countByStatus(Property::STATUS_PENDING_TRANSFER);

        $since30d = date('Y-m-d H:i:s', strtotime('-30 days'));
        $completed_transfers_30d = PropertyTransfer::countByStatusSince(PropertyTransfer::STATUS_COMPLETED, $since30d, 'updated_at');
        $rejected_transfers_30d = PropertyTransfer::countByStatusSince(PropertyTransfer::STATUS_REJECTED, $since30d, 'updated_at');

        $pending_transfers_latest = PropertyTransfer::findPendingLimited(10);
        $stuck_pending_over_7d = PropertyTransfer::countStuckPending(7);

        // Blockchain (Etherscan) transaction count for the last 30 days.
        // Cached briefly to avoid rate limits and keep portal snappy.
        $tx_cache_ttl_seconds = 300;
        $cache_key = 'adminPortal_tx_30d';
        $tx_30d = null;
        $days = [];
        $tx_counts = [];
        $tx_data = null;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $cached = $_SESSION[$cache_key] ?? null;
        if (is_array($cached) && isset($cached['value'], $cached['ts']) && (time() - (int)$cached['ts']) < $tx_cache_ttl_seconds) {
            $tx_30d = (int)$cached['value'];
        } else {
            $etherscan_api_key = \Database::getEnv('ETHERSCAN_API_KEY');
            $contract_address = \Database::getEnv('ETHERSCAN_CONTRACT_ADDRESS');

            if ($etherscan_api_key && $contract_address) {
                $tx_per_day = [];
                for ($i = 29; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $days[] = $date;
                    $tx_per_day[$date] = 0;
                }

                $tx_url = "https://api-sepolia.etherscan.io/api?module=account&action=txlist&address={$contract_address}&startblock=0&endblock=99999999&sort=asc&apikey={$etherscan_api_key}";
                $options = ["http" => ["header" => "User-Agent: Mozilla/5.0\r\n"]];
                $context = stream_context_create($options);
                $tx_json = @file_get_contents($tx_url, false, $context);
                $tx_data = $tx_json ? json_decode($tx_json, true) : null;

                if ($tx_data && isset($tx_data['result']) && is_array($tx_data['result'])) {
                    foreach ($tx_data['result'] as $tx) {
                        $tx_time = isset($tx['timeStamp']) ? (int)$tx['timeStamp'] : 0;
                        $date = date('Y-m-d', $tx_time);
                        if (isset($tx_per_day[$date])) {
                            $tx_per_day[$date]++;
                        }
                    }
                }

                $tx_30d = array_sum(array_values($tx_per_day));
                $tx_counts = array_values($tx_per_day);
                $_SESSION[$cache_key] = ['value' => (int)$tx_30d, 'ts' => time()];
            }
        }

        // If we didn't build day labels (cache hit or missing config), still provide stable chart arrays.
        if (empty($days)) {
            $tx_per_day = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $days[] = $date;
                $tx_per_day[$date] = 0;
            }
            $tx_counts = array_values($tx_per_day);
        }

        $total_transactions = $tx_30d ?? 0;

        return $this->render('home.Admin.adminPortal', [
            'total_users' => $total_users,
            'total_assets' => $total_assets,
            'total_orders' => $total_orders,
            'total_transactions' => $total_transactions,
            'days' => $days,
            'tx_counts' => $tx_counts,
            'tx_data' => $tx_data,
            'pending_transfer_reviews' => $pending_transfer_reviews,
            'properties_pending_transfer' => $properties_pending_transfer,
            'completed_transfers_30d' => $completed_transfers_30d,
            'rejected_transfers_30d' => $rejected_transfers_30d,
            'pending_transfers_latest' => $pending_transfers_latest,
            'stuck_pending_over_7d' => $stuck_pending_over_7d,
            'blockchain_tx_30d' => $tx_30d,
            'employee_status_list' => $employee_status_list,
        ]);
    }

    public function setEmpAuth(): bool|array|string
    {
        AuthMiddleware::requireAdmin();
        return $this->render('home.Admin.setEmpAuth');
    }

    public function checkEmpAuth(): bool|array|string
    {
        AuthMiddleware::requireAdmin();
        return $this->render('home.Admin.checkEmpAuth');
    }

    public function adminAddress(): bool|array|string
    {
        AuthMiddleware::requireAdmin();
        return $this->render('home.Admin.adminAddress');
    }

    public function adminReports(): bool|array|string
    {
        AuthMiddleware::requireAdmin();

        $from = isset($_GET['from']) ? trim((string)$_GET['from']) : '';
        $to = isset($_GET['to']) ? trim((string)$_GET['to']) : '';
        $status = isset($_GET['status']) ? trim((string)$_GET['status']) : '';

        return $this->render('home.Admin.adminReports', [
            'from' => $from,
            'to' => $to,
            'status' => $status
        ]);
    }

    public function adminReportsDownload(): void
    {
        AuthMiddleware::requireAdmin();

        $type = isset($_GET['type']) ? trim((string)$_GET['type']) : 'transfers';
        $from = isset($_GET['from']) ? trim((string)$_GET['from']) : '';
        $to = isset($_GET['to']) ? trim((string)$_GET['to']) : '';
        $status = isset($_GET['status']) ? trim((string)$_GET['status']) : '';

        if ($type !== 'transfers') {
            http_response_code(400);
            echo "Unsupported report type";
            return;
        }

        $rows = PropertyTransfer::reportTransfers($from !== '' ? $from : null, $to !== '' ? $to : null, $status !== '' ? $status : null);

        $safeFrom = $from !== '' ? $from : date('Y-m-d', strtotime('-30 days'));
        $safeTo = $to !== '' ? $to : date('Y-m-d');
        $filename = "dlsj_transfers_report_{$safeFrom}_to_{$safeTo}.csv";

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        if ($out === false) {
            http_response_code(500);
            echo "Failed to generate report";
            return;
        }

        // Excel-friendly UTF-8 BOM
        fwrite($out, "\xEF\xBB\xBF");

        fputcsv($out, [
            'TrackingNumber',
            'Status',
            'CreatedAt',
            'UpdatedAt',
            'PropertyId',
            'District',
            'Village',
            'BlockName',
            'BlockNumber',
            'PlotNumber',
            'PropertyType',
            'Area',
            'SellerName',
            'SellerNationalId',
            'BuyerName',
            'BuyerNationalId',
            'BuyerPhone',
            'BuyerAddress'
        ]);

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['tracking_number'] ?? '',
                $r['status'] ?? '',
                $r['created_at'] ?? '',
                $r['updated_at'] ?? '',
                $r['property_id'] ?? '',
                $r['district_name'] ?? '',
                $r['village'] ?? '',
                $r['block_name'] ?? '',
                $r['block_number'] ?? '',
                $r['plot_number'] ?? '',
                $r['type'] ?? '',
                $r['area'] ?? '',
                $r['seller_name'] ?? '',
                $r['seller_national_id'] ?? '',
                $r['buyer_name'] ?? '',
                $r['buyer_national_id'] ?? '',
                $r['buyer_phone'] ?? '',
                $r['buyer_address'] ?? ''
            ]);
        }

        fclose($out);
        exit;
    }

    public function adminTransferQueue(): bool|array|string
    {
        AuthMiddleware::requireAdmin();

        $tracking = isset($_GET['tracking_number']) ? trim((string)$_GET['tracking_number']) : '';

        $pending_transfers_latest = PropertyTransfer::findPendingLimited(20);
        $transferRow = null;
        $transferDetails = null;
        $propertyDetails = null;
        $sellerDetails = null;
        $buyerDetails = null;

        if ($tracking !== '') {
            $transferRow = PropertyTransfer::findTransferDetailsForAdmin($tracking);
            if ($transferRow) {
                $transferDetails = [
                    'tracking_number' => $transferRow['tracking_number'],
                    'status' => $transferRow['status'],
                    'created_at' => $transferRow['created_at'],
                    'updated_at' => $transferRow['updated_at'] ?? null,
                ];

                $propertyDetails = [
                    'id' => $transferRow['property_id'] ?? null,
                    'district_name' => $transferRow['district_name'] ?? null,
                    'village' => $transferRow['village'] ?? null,
                    'block_name' => $transferRow['block_name'] ?? null,
                    'plot_number' => $transferRow['plot_number'] ?? null,
                    'block_number' => $transferRow['block_number'] ?? null,
                    'type' => $transferRow['type'] ?? 'land',
                    'area' => $transferRow['area'] ?? null,
                    'status' => $transferRow['status'] ?? null,
                ];

                $sellerDetails = [
                    'name' => $transferRow['seller_name'] ?? null,
                    'national_id' => $transferRow['seller_national_id'] ?? null,
                    'phone' => $transferRow['seller_phone'] ?? null,
                    'email' => $transferRow['seller_email'] ?? null,
                ];

                $buyerDetails = [
                    'name' => $transferRow['buyer_name'] ?? ($transferRow['buyer_user_name'] ?? null),
                    'national_id' => $transferRow['buyer_national_id'] ?? ($transferRow['buyer_user_national_id'] ?? null),
                    'phone' => $transferRow['buyer_phone'] ?? ($transferRow['buyer_user_phone'] ?? null),
                    'address' => $transferRow['buyer_address'] ?? null,
                    'email' => $transferRow['buyer_user_email'] ?? null,
                    'is_registered' => !empty($transferRow['buyer_user_id']),
                ];
            } else {
                $_SESSION['error'] = 'No transfer found with this tracking number.';
            }
        }

        return $this->render('home.Admin.adminTransferQueue', [
            'pending_transfers_latest' => $pending_transfers_latest,
            'transferDetails' => $transferDetails,
            'propertyDetails' => $propertyDetails,
            'sellerDetails' => $sellerDetails,
            'buyerDetails' => $buyerDetails,
            'tracking' => $tracking,
        ]);
    }

    public function adminUserSearch(): bool|array|string
    {
        AuthMiddleware::requireAdmin();

        $q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        $role = isset($_GET['role']) ? trim((string)$_GET['role']) : 'all';
        $sort = isset($_GET['sort']) ? trim((string)$_GET['sort']) : 'date_desc';

        $users = User::searchAdminUsers($q !== '' ? $q : null, $role, $sort);

        return $this->render('home.Admin.adminUserSearch', [
            'users' => $users,
            'q' => $q,
            'role' => $role,
            'sort' => $sort
        ]);
    }

    public function adminSystemRecords(): bool|array|string
    {
        AuthMiddleware::requireAdmin();

        $q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        $properties = Property::searchSystemRecords($q !== '' ? $q : null, 50);
        $transfers = PropertyTransfer::searchSystemRecords($q !== '' ? $q : null, 50);

        return $this->render('home.Admin.adminSystemRecords', [
            'q' => $q,
            'properties' => $properties,
            'transfers' => $transfers
        ]);
    }
}

