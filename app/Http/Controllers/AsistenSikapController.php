<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use App\peraturan;

class AsistenSikapController extends Controller
{
    const MISTRAL_API_URL = 'https://api.mistral.ai/v1/chat/completions';

    /**
     * Constructor to restrict access to ADMIN only.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::allows('ADMIN')) {
                return $next($request);
            }
            abort(403, 'Anda tidak memiliki hak akses');
        });
    }

    /**
     * Show the main chat interface.
     */
    public function index()
    {
        $sessionKey = 'asisten_sikap_chat_history_' . \Auth::id();
        $history = session()->get($sessionKey, []);
        return view('admin.asisten_sikap', compact('history'));
    }

    /**
     * Handle the chat request.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->get('message');
        $apiKey = env('MISTRAL_API_KEY', 'GysR79wNoYf9i763EAlv8Q58VcrPwhCq');

        // 1. Extract keywords for RAG
        $keywords = $this->getKeywords($userMessage);

        // 2. Query regulations based on keywords
        $matchedPeraturans = collect();
        if (!empty($keywords)) {
            $queryBuilder = peraturan::query();
            $queryBuilder->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('name', 'LIKE', "%$keyword%")
                      ->orWhere('uraian', 'LIKE', "%$keyword%")
                      ->orWhere('nosk', 'LIKE', "%$keyword%");
                }
            });
            $matchedPeraturans = $queryBuilder->limit(3)->get(['id', 'name', 'nosk', 'tglsk', 'kategori', 'jenis_surat', 'pdf', 'uraian']);
        }

        // 3. Build context by reading actual document content
        $context = "";
        $docContextList = [];

        if ($matchedPeraturans->isNotEmpty()) {
            foreach ($matchedPeraturans as $p) {
                $docInfo = [
                    'id' => $p->id,
                    'name' => $p->name,
                    'nosk' => $p->nosk,
                    'tglsk' => $p->tglsk,
                    'kategori' => $p->kategori,
                    'content' => null,
                    'content_type' => 'none',
                ];

                $isPdf = !empty($p->pdf) && preg_match('/\.pdf$/i', trim($p->pdf));
                $isImageHtml = !empty($p->pdf) && preg_match('/<img/i', $p->pdf);

                if ($isPdf) {
                    // PDF file - try to extract text with Python
                    $pdfPath = storage_path('app/public/pdfs/' . basename($p->pdf));
                    if (file_exists($pdfPath)) {
                        $pythonScript = app_path('Helpers/extract_pdf_pages.py');
                        $pythonCmd = DIRECTORY_SEPARATOR === '/' ? 'python3' : 'python';
                        $command = "$pythonCmd " . escapeshellarg($pythonScript)
                            . " " . escapeshellarg($pdfPath)
                            . " " . escapeshellarg(implode(' ', $keywords))
                            . " 2>&1";
                        $output = shell_exec($command);
                        if ($output && stripos($output, 'Error') === false && strlen(trim($output)) > 30) {
                            $docInfo['content'] = substr(trim($output), 0, 5000);
                            $docInfo['content_type'] = 'pdf_text';
                        }
                    }
                    if ($docInfo['content'] === null) {
                        $docInfo['content'] = "Dokumen ini tersimpan sebagai file PDF (" . basename($p->pdf) . "). File belum dapat diekstrak pada server ini. Admin bisa melihat dokumen resminya pada tautan yang disediakan.";
                        $docInfo['content_type'] = 'pdf_not_found';
                    }

                } elseif ($isImageHtml) {
                    // Scanned image-based document - extract image URLs and use Mistral Vision
                    $imageUrls = $this->extractImageUrls($p->pdf);

                    if (!empty($imageUrls)) {
                        $visionText = $this->readImagesWithMistralVision($imageUrls, $apiKey, $userMessage);
                        if ($visionText) {
                            $docInfo['content'] = $visionText;
                            $docInfo['content_type'] = 'vision_ocr';
                        } else {
                            $docInfo['content'] = "Dokumen ini tersimpan sebagai gambar scan (" . count($imageUrls) . " halaman). Pembacaan gambar tidak berhasil. Silakan buka tautan untuk melihat dokumen aslinya.";
                            $docInfo['content_type'] = 'image_failed';
                        }
                    } else {
                        $docInfo['content'] = "Dokumen tidak memiliki konten teks yang dapat dibaca.";
                        $docInfo['content_type'] = 'empty';
                    }

                } else {
                    // Plain text or empty uraian
                    $rawText = !empty($p->pdf) ? strip_tags($p->pdf) : strip_tags($p->uraian ?? '');
                    $rawText = trim($rawText);
                    if (strlen($rawText) > 20) {
                        $docInfo['content'] = substr($rawText, 0, 5000);
                        $docInfo['content_type'] = 'plain_text';
                    } else {
                        $docInfo['content'] = "Tidak ada isi teks yang tersedia untuk dokumen ini. Silakan buka tautan untuk melihat dokumen.";
                        $docInfo['content_type'] = 'empty';
                    }
                }

                $docContextList[] = $docInfo;
            }

            // Build the final context string
            $context .= "Berikut adalah isi resmi dokumen peraturan BPR Cianjur yang paling relevan.\n";
            $context .= "PERINTAH KETAT: Jawab HANYA berdasarkan isi dokumen di bawah ini. JANGAN mengarang, mengira-ngira, atau menambah informasi dari luar dokumen ini.\n";
            $context .= "Jika informasi spesifik tidak ada dalam dokumen, katakan dengan jujur bahwa detailnya tidak tersedia pada dokumen yang berhasil dibaca.\n\n";

            foreach ($docContextList as $doc) {
                $context .= "═══════════════════════════════════════════\n";
                $context .= "DOKUMEN ID: {$doc['id']}\n";
                $context .= "Judul: \"{$doc['name']}\"\n";
                $context .= "NoSK: {$doc['nosk']} | Tanggal: {$doc['tglsk']} | Kategori: {$doc['kategori']}\n";
                $context .= "Tautan Resmi: /peraturan/{$doc['id']}\n";
                $context .= "Tipe Konten: {$doc['content_type']}\n";
                $context .= "Isi Dokumen:\n{$doc['content']}\n\n";
            }

            $context .= "\nPETUNJUK FORMAT JAWABAN:\n";
            $context .= "1. Kutip langsung dari isi dokumen di atas. Sebutkan pasal/ayat/poin jika ada.\n";
            $context .= "2. Setiap peraturan yang disebutkan WAJIB disertai tautan Markdown: [Judul (NoSK: xxx)](/peraturan/{id})\n";
            $context .= "3. Gunakan format yang rapi dengan bullet points untuk detail.\n";

        } else {
            $context = "Tidak ditemukan dokumen peraturan yang cocok untuk kata kunci ini di database BPR Cianjur.\n";
            $context .= "Informasikan kepada user bahwa dokumen tidak ditemukan dan minta kata kunci yang lebih spesifik.\n";
        }

        // 4. Construct System Prompt
        $systemPrompt  = "Anda adalah 'Asisten Sikap AI', asisten resmi PT BPR Cianjur yang membantu Admin memahami peraturan dan kebijakan perusahaan.\n";
        $systemPrompt .= "Gunakan Bahasa Indonesia yang formal namun mudah dipahami.\n\n";
        $systemPrompt .= "ATURAN MUTLAK:\n";
        $systemPrompt .= "1. Jawaban HARUS berdasarkan dokumen resmi yang disediakan dalam Data Context.\n";
        $systemPrompt .= "2. Jika data tidak ada dalam dokumen, katakan dengan jelas: 'Informasi ini tidak ditemukan dalam dokumen resmi yang tersedia.'\n";
        $systemPrompt .= "3. Jangan menjawab pertanyaan di luar topik peraturan/kebijakan BPR Cianjur.\n\n";
        $systemPrompt .= "Data Context Dokumen Resmi:\n" . $context;

        // 5. Manage conversation history (per user ID)
        $sessionKey = 'asisten_sikap_chat_history_' . \Auth::id();
        $history = session()->get($sessionKey, []);

        $messages = [];
        $messages[] = ['role' => 'system', 'content' => $systemPrompt];

        foreach (array_slice($history, -8) as $chat) {
            $messages[] = ['role' => 'user', 'content' => $chat['user']];
            $messages[] = ['role' => 'assistant', 'content' => $chat['assistant']];
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        // 6. Call Mistral Chat API
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->withOptions([
                'verify' => false,
                'connect_timeout' => 8,
                'timeout' => 60,
            ])->post(self::MISTRAL_API_URL, [
                'model' => env('MISTRAL_MODEL', 'mistral-small-latest'),
                'messages' => $messages,
                'temperature' => 0.1,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $aiResponse = $result['choices'][0]['message']['content']
                    ?? 'Maaf, saya tidak menerima respon yang valid dari server AI.';

                $history[] = [
                    'user' => $userMessage,
                    'assistant' => $aiResponse,
                    'timestamp' => date('c'),
                ];
                session()->put($sessionKey, $history);

                return response()->json(['success' => true, 'message' => $aiResponse]);
            } else {
                Log::error('Mistral API Chat Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Server AI mengalami kendala (HTTP ' . $response->status() . '). Silakan coba lagi.',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Asisten Sikap Exception', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghubungi Asisten Sikap. Silakan coba kembali.',
            ], 500);
        }
    }

    /**
     * Resolve a URL to a local file path if it exists in local storage.
     */
    private function resolveLocalPathFromUrl(string $url): ?string
    {
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['path'])) {
            return null;
        }
        
        $path = $parsedUrl['path'];
        
        // In Laravel, /storage/ maps to storage/app/public/
        if (strpos($path, '/storage/') === 0) {
            $relativePath = substr($path, 9);
            
            $localPath = storage_path('app/public/' . $relativePath);
            if (file_exists($localPath)) {
                return $localPath;
            }
            
            $publicPath = public_path('storage/' . $relativePath);
            if (file_exists($publicPath)) {
                return $publicPath;
            }
        }
        
        return null;
    }

    /**
     * Use Mistral Vision (pixtral) to read scanned document images.
     * Sends up to 4 images (pages) and gets the OCR text.
     */
    private function readImagesWithMistralVision(array $imageUrls, string $apiKey, string $userQuestion): ?string
    {
        // Limit to first 4 pages to stay within token limits
        $imageUrls = array_slice($imageUrls, 0, 4);
        
        // Build multimodal message content
        $content = [];
        $content[] = [
            'type' => 'text',
            'text' => "Baca dan ekstrak SEMUA teks dari gambar-gambar dokumen resmi peraturan perusahaan berikut ini dengan akurat dan lengkap. Sertakan semua pasal, ayat, poin, angka, dan ketentuan yang tertulis. Pertanyaan yang akan dijawab berdasarkan dokumen ini: \"{$userQuestion}\""
        ];
        
        foreach ($imageUrls as $url) {
            $localPath = $this->resolveLocalPathFromUrl($url);
            if ($localPath && file_exists($localPath)) {
                $imageData = base64_encode(file_get_contents($localPath));
                $ext = pathinfo($localPath, PATHINFO_EXTENSION);
                $mimeType = 'image/png';
                if (in_array(strtolower($ext), ['jpg', 'jpeg'])) {
                    $mimeType = 'image/jpeg';
                } elseif (strtolower($ext) === 'gif') {
                    $mimeType = 'image/gif';
                } elseif (strtolower($ext) === 'webp') {
                    $mimeType = 'image/webp';
                }
                
                $dataUrl = "data:{$mimeType};base64,{$imageData}";
                
                $content[] = [
                    'type' => 'image_url',
                    'image_url' => ['url' => $dataUrl],
                ];
            } else {
                // Fallback to URL if file doesn't exist locally
                $content[] = [
                    'type' => 'image_url',
                    'image_url' => ['url' => $url],
                ];
            }
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->withOptions([
                'verify' => false,
                'connect_timeout' => 8,
                'timeout' => 60,
            ])->post(self::MISTRAL_API_URL, [
                'model' => 'pixtral-12b-2409',
                'messages' => [
                    ['role' => 'user', 'content' => $content],
                ],
                'temperature' => 0.0,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['choices'][0]['message']['content'] ?? null;
            }
            
            Log::warning('Mistral Vision API failed', [
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 500),
            ]);
            return null;

        } catch (\Exception $e) {
            Log::warning('Mistral Vision Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Extract all image URLs from stored HTML content.
     * Returns absolute URLs using APP_URL config.
     */
    private function extractImageUrls(string $html): array
    {
        $urls = [];
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches);
        
        $appUrl = rtrim(config('app.url', 'http://localhost'), '/');
        
        foreach ($matches[1] as $src) {
            // Normalize to absolute URL
            if (strpos($src, 'http') === 0) {
                $urls[] = $src;
            } else {
                $src = '/' . ltrim($src, '/');
                $urls[] = $appUrl . $src;
            }
        }
        
        return array_unique($urls);
    }

    /**
     * Reset the conversation history for the current user.
     */
    public function reset()
    {
        $sessionKey = 'asisten_sikap_chat_history_' . \Auth::id();
        session()->forget($sessionKey);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Riwayat obrolan berhasil dihapus.',
            ]);
        }

        return redirect()->route('admin.asisten-sikap.index')
            ->with('status', 'Riwayat obrolan berhasil dibersihkan.');
    }

    /**
     * Tokenize query string and extract keywords.
     */
    private function getKeywords(string $query): array
    {
        $stopWords = [
            'dan', 'yang', 'untuk', 'dari', 'atau', 'di', 'ke', 'ini', 'itu', 'ada', 'apa', 'bisa', 'saya',
            'ingin', 'tahu', 'tentang', 'bagaimana', 'apakah', 'adalah', 'dengan', 'pada', 'oleh', 'sebagai',
            'bahwa', 'secara', 'akan', 'telah', 'sudah', 'karena', 'jika', 'maka', 'namun', 'tetapi', 'saja',
            'juga', 'peraturan', 'mengenai', 'terkait', 'tolong', 'jelaskan', 'coba', 'sebutkan', 'tampilkan',
            'mencari', 'butuh', 'info', 'berapa', 'hari', 'tahun', 'durasi', 'lama', 'waktu', 'dalam', 'setahun',
            'ada', 'berapakah', 'berikan', 'detail', 'rincian',
        ];

        $cleanQuery = preg_replace('/[^\w\s]/u', '', strtolower($query));
        $words = preg_split('/\s+/', $cleanQuery, -1, PREG_SPLIT_NO_EMPTY);

        $keywords = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (mb_strlen($word) > 2 && !in_array($word, $stopWords)) {
                $keywords[] = $word;
            }
        }
        return array_unique($keywords);
    }
}
