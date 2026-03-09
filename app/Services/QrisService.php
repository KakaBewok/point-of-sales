<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Illuminate\Support\Facades\Log;

/**
 * QrisService
 *
 * Handles all QRIS (Indonesia EMVCo-based) operations locally,
 * without any payment gateway integration.
 *
 * QRIS Payload Structure (EMVCo):
 *   Tag 00 - Payload Format Indicator (always "01")
 *   Tag 01 - Point of Initiation Method (11 = static, 12 = dynamic)
 *   Tag 51 - Merchant Account Information (QRIS-specific)
 *   Tag 52 - Merchant Category Code
 *   Tag 53 - Transaction Currency (360 = IDR)
 *   Tag 54 - Transaction Amount (injected for dynamic QRIS)
 *   Tag 58 - Country Code (ID)
 *   Tag 59 - Merchant Name
 *   Tag 60 - Merchant City
 *   Tag 63 - CRC (4-char hex, CRC16-CCITT-FALSE)
 */
class QrisService
{
    // ─── Public API ─────────────────────────────────────────────

    /**
     * Read the raw QRIS EMVCo payload from an uploaded image file.
     *
     * @param  string  $imagePath  Absolute path to the image file on disk
     * @return string  The raw payload string
     *
     * @throws \Exception  If the image cannot be read or no QR code is found
     */
    public function extractPayloadFromImage(string $imagePath): string
    {
        if (! file_exists($imagePath)) {
            throw new \Exception('File gambar tidak ditemukan.');
        }

        try {
            $qrReader = new \QrReader($imagePath);
            $payload = $qrReader->text();
        } catch (\Throwable $e) {
            Log::error('QrisService::extractPayloadFromImage error', ['error' => $e->getMessage()]);
            throw new \Exception('Gagal membaca QR dari gambar. Pastikan gambar jelas dan berformat JPEG/PNG.');
        }

        if (empty($payload)) {
            throw new \Exception('Tidak ditemukan QR code pada gambar yang diupload.');
        }

        return trim($payload);
    }

    /**
     * Validate that the given payload is a valid Indonesian QRIS payload.
     *
     * Checks:
     *  - Starts with "000201" (payload format indicator = 01)
     *  - Contains tag 51 (Merchant Account Information for QRIS)
     *  - Ends with "6304" + 4-char hex (CRC present)
     *  - CRC value matches the recalculated CRC16
     *
     * @throws \Exception  With a human-readable message if validation fails
     */
    public function validateQrisPayload(string $payload): void
    {
        if (! str_starts_with($payload, '000201')) {
            throw new \Exception('Payload bukan format EMVCo yang valid (harus dimulai dengan 000201).');
        }

        // Must contain merchant account info tag 51 (QRIS standard)
        if (! str_contains($payload, '51')) {
            throw new \Exception('Payload tidak mengandung informasi merchant QRIS (tag 51).');
        }

        // Must contain currency 360 (IDR)
        if (! str_contains($payload, '5303360')) {
            throw new \Exception('Payload bukan QRIS Indonesia (currency bukan IDR/360).');
        }

        // Must contain CRC field tag 63
        $crcPos = strrpos($payload, '6304');
        if ($crcPos === false) {
            throw new \Exception('Payload tidak memiliki checksum CRC (tag 6304).');
        }

        // Validate CRC
        $payloadWithoutCrc = substr($payload, 0, $crcPos + 4); // includes "6304"
        $expectedCrc = strtoupper($this->calculateCRC16($payloadWithoutCrc));
        $actualCrc = strtoupper(substr($payload, $crcPos + 4, 4));

        if ($expectedCrc !== $actualCrc) {
            throw new \Exception("CRC payload tidak valid (expected: {$expectedCrc}, got: {$actualCrc}). Pastikan Anda mengupload QRIS asli.");
        }
    }

    /**
     * Generate a dynamic QRIS payload by injecting the transaction amount.
     *
     * Steps:
     *  1. Switch Point of Initiation Method from 11 (static) to 12 (dynamic)
     *  2. Remove any existing tag 54 (transaction amount)
     *  3. Remove the CRC field (tag 6304 + 4 chars)
     *  4. Inject tag 54 with the formatted integer amount
     *  5. Recalculate and append the CRC16
     *
     * @param  string  $staticPayload  The raw static QRIS payload from the store
     * @param  float   $amount         The transaction grand total
     * @return string  The new dynamic payload ready to encode as QR
     */
    public function generateDynamicPayload(string $staticPayload, float $amount): string
    {
        // 1. Change initiation method from static (11) to dynamic (12)
        $payload = str_replace('010211', '010212', $staticPayload);

        // 2. Remove existing CRC (always last 8 chars: "6304XXXX")
        $crcPos = strrpos($payload, '6304');
        if ($crcPos !== false) {
            $payload = substr($payload, 0, $crcPos);
        }

        // 3. Remove existing amount tag 54 if present
        $payload = $this->removeTag($payload, '54');

        // 4. Inject tag 54: amount as integer string (no decimals per QRIS spec)
        $amountStr = (string) (int) round($amount);
        $amountLen = str_pad(strlen($amountStr), 2, '0', STR_PAD_LEFT);
        $amountTag = '54' . $amountLen . $amountStr;

        // Tag 54 must be inserted before tag 58 (country code) per EMVCo ordering
        $tag58Pos = strpos($payload, '5802ID');
        if ($tag58Pos !== false) {
            $payload = substr($payload, 0, $tag58Pos) . $amountTag . substr($payload, $tag58Pos);
        } else {
            // Fallback: append before CRC area
            $payload .= $amountTag;
        }

        // 5. Append CRC field tag + placeholder, calculate, and append the value
        $payloadForCrc = $payload . '6304';
        $crc = strtoupper($this->calculateCRC16($payloadForCrc));
        $finalPayload = $payloadForCrc . $crc;

        return $finalPayload;
    }

    /**
     * Generate a base64-encoded PNG QR code image from a QRIS payload.
     *
     * Returns a data-URI string: "data:image/png;base64,..."
     * suitable for embedding directly in an <img src="..."> tag.
     *
     * @param  string  $payload  The dynamic QRIS payload
     * @return string  Base64 data-URI
     */
    public function generateQrImage(string $payload): string
    {
        $qrCode = new QrCode(
            data: $payload,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 400,
            margin: 16,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255),
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return 'data:image/png;base64,' . base64_encode($result->getString());
    }

    // ─── Private Helpers ────────────────────────────────────────

    /**
     * Remove a tag (and its length + value) from an EMVCo TLV payload string.
     *
     * EMVCo TLV format: Tag(2) | Length(2) | Value(Length)
     *
     * @param  string  $payload  The payload string
     * @param  string  $tag      2-char tag identifier (e.g. "54")
     * @return string  Payload with the tag removed
     */
    private function removeTag(string $payload, string $tag): string
    {
        $pos = 0;
        $result = '';

        while ($pos < strlen($payload)) {
            if (strlen($payload) - $pos < 4) {
                // Remaining chars are too short to form a TLV; append as-is
                $result .= substr($payload, $pos);
                break;
            }

            $currentTag = substr($payload, $pos, 2);
            $lengthStr  = substr($payload, $pos + 2, 2);

            if (! ctype_digit($lengthStr)) {
                // Malformed payload; stop processing
                $result .= substr($payload, $pos);
                break;
            }

            $length = (int) $lengthStr;
            $totalLen = 4 + $length; // tag(2) + length(2) + value

            if ($currentTag === $tag) {
                // Skip this TLV entirely
                $pos += $totalLen;
                continue;
            }

            $result .= substr($payload, $pos, $totalLen);
            $pos += $totalLen;
        }

        return $result;
    }

    /**
     * Calculate CRC16-CCITT-FALSE checksum as used by EMVCo QRIS spec.
     *
     * Parameters:
     *  - Polynomial : 0x1021
     *  - Initial value : 0xFFFF
     *  - Input reflected  : false
     *  - Output reflected : false
     *  - Final XOR value  : 0x0000
     *
     * @param  string  $data  The string to checksum (NOT including the 4-char CRC value)
     * @return string  4-character uppercase hexadecimal CRC value
     */
    public function calculateCRC16(string $data): string
    {
        $crc = 0xFFFF;

        for ($i = 0; $i < strlen($data); $i++) {
            $byte = ord($data[$i]);
            $crc ^= ($byte << 8);

            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return str_pad(strtoupper(dechex($crc)), 4, '0', STR_PAD_LEFT);
    }
}
