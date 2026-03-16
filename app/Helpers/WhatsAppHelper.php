<?php namespace App\Helpers;
use Illuminate\Support\Facades\Log;
class WhatsAppHelper
{
    public static function convertPhoneNumber($nohp)
    {
        if (empty($nohp)) {
            return null;
        }
        $nohp = trim($nohp);
        $nohp = str_replace([' ', '-', '+', '(', ')'], '', $nohp);
        if (strpos($nohp, '0') === 0) {
            $nohp = '62' . substr($nohp, 1);
        } elseif (strpos($nohp, '62') !== 0) {
            $nohp = '62' . $nohp;
        }
        return $nohp;
    }
    public static function sendMessage($phoneNumber, $message)
    {
        if (!config('services.whatsapp.enabled')) {
            Log::info('WhatsApp disabled', ['phone' => $phoneNumber]);
            return ['success' => false, 'message' => 'WhatsApp disabled'];
        }
        $phone = self::convertPhoneNumber($phoneNumber);
        if (!$phone) {
            return ['success' => false, 'message' => 'Nomor HP kosong'];
        }
        $data = ['code' => config('services.whatsapp.code'), 'penerima' => [['name' => '', 'phone' => $phone]], 'message_type' => '1', 'message_schedule' => 'normal', 'file' => '', 'pesan' => $message, 'delay' => '1000'];
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [CURLOPT_URL => config('services.whatsapp.url'), CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'POST', CURLOPT_POSTFIELDS => json_encode($data), CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/json'], CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false]);
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            curl_close($curl);
            if ($error) {
                Log::error('WhatsApp cURL Error', ['phone' => $phone, 'error' => $error]);
                return ['success' => false, 'message' => $error];
            }
            Log::info('WhatsApp API Response', ['phone' => $phone, 'http_code' => $httpCode, 'response' => $response]);
            return ['success' => true, 'http_code' => $httpCode, 'response' => json_decode($response, true)];
        } catch (\Exception $e) {
            Log::error('WhatsApp Exception', ['phone' => $phoneNumber, 'message' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public static function sendCutiApprovalNotification($pegawai, $ordercuti, $approver)
    {
        $message = "Permohonan cuti Anda telah disetujui.\n";
        $message .= 'Jenis Cuti: ' . $ordercuti->jeniscuti . "\n";
        $message .= 'Tanggal Cuti: ' . date('d-m-Y', strtotime($ordercuti->tglawal)) . ' s.d. ' . date('d-m-Y', strtotime($ordercuti->tglakhir)) . "\n";
        $message .= 'Jumlah Hari: ' . $ordercuti->jmlcuti . " hari\n";
        $message .= 'Disetujui oleh: ' . $approver->name;
        return self::sendMessage($pegawai->nohp, $message);
    }
}
