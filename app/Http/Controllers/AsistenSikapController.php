<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use App\peraturan;

class AsistenSikapController extends Controller
{
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
        $history = session()->get('asisten_sikap_chat_history', []);
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
        
        // 1. Extract keywords for RAG (Retrieval-Augmented Generation)
        $keywords = $this->getKeywords($userMessage);
        
        // 2. Query regulations based on keywords
        $matchedPeraturans = collect();
        if (!empty($keywords)) {
            $queryBuilder = peraturan::query();
            $queryBuilder->where(function($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('name', 'LIKE', "%$keyword%")
                      ->orWhere('uraian', 'LIKE', "%$keyword%")
                      ->orWhere('nosk', 'LIKE', "%$keyword%");
                }
            });
            $matchedPeraturans = $queryBuilder->limit(12)->get(['id', 'name', 'nosk', 'tglsk', 'kategori', 'jenis_surat']);
        }

        // 3. Build the RAG Context for System Prompt
        $context = "";
        if ($matchedPeraturans->isNotEmpty()) {
            $context .= "Berikut adalah daftar peraturan yang relevan dengan pertanyaan user dari database BPR Cianjur:\n";
            foreach ($matchedPeraturans as $p) {
                $context .= "- ID Peraturan: {$p->id} | Judul: \"{$p->name}\" | NoSK: \"{$p->nosk}\" | TglSK: \"{$p->tglsk}\" | Kategori: \"{$p->kategori}\"\n";
            }
            $context .= "\nPETUNJUK PENTING:\n";
            $context .= "Jika Anda menyarankan atau menyebutkan salah satu peraturan di atas, Anda WAJIB menyertakan tautannya dalam format Markdown link: `[Judul Peraturan (NoSK: xxx)](/peraturan/{id})`. Pastikan ID-nya sesuai dengan ID Peraturan yang kami sediakan di atas. Jangan mengarang ID.\n";
        } else {
            $context .= "Tidak ditemukan peraturan yang secara langsung mencocokkan kata kunci pertanyaan di database kami.\n";
            $context .= "PETUNJUK PENTING: Beritahu user dengan sopan bahwa Anda tidak menemukan peraturan secara spesifik, namun cobalah memberikan saran kata kunci lain atau jawaban umum seputar kebijakan perbankan / perusahaan jika memungkinkan.\n";
        }

        // 4. Construct System Prompt
        $systemPrompt = "Anda adalah 'Asisten Sikap', asisten kecerdasan buatan (AI) yang ramah, profesional, dan berwawasan luas khusus untuk membantu Admin PT BPR Cianjur.\n";
        $systemPrompt .= "Tugas utama Anda adalah menjawab pertanyaan Admin mengenai peraturan, kebijakan internal/eksternal, SOP, dan administrasi BPR Cianjur.\n";
        $systemPrompt .= "Bahasa respons: Bahasa Indonesia yang formal namun ramah.\n\n";
        $systemPrompt .= "Batasan Chat:\n";
        $systemPrompt .= "1. Jawablah pertanyaan HANYA seputar peraturan perusahaan, SOP, kebijakan, perbankan, dan hal-hal yang berkaitan dengan internal/eksternal BPR Cianjur.\n";
        $systemPrompt .= "2. Jika pertanyaan di luar konteks BPR Cianjur atau perbankan (misalnya: resep makanan, pemrograman umum, gosip luar, dll.), Anda HARUS menolak dengan sopan, menjelaskan bahwa Anda dirancang khusus untuk membantu perihal regulasi dan kebijakan BPR Cianjur.\n\n";
        $systemPrompt .= "Data Context saat ini:\n" . $context;

        // 5. Manage Conversation History (Limit to last 10 messages to manage tokens)
        $history = session()->get('asisten_sikap_chat_history', []);
        
        // Prepare messages for Mistral API
        $messages = [];
        $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        
        // Append history
        foreach (array_slice($history, -10) as $chat) {
            $messages[] = ['role' => 'user', 'content' => $chat['user']];
            $messages[] = ['role' => 'assistant', 'content' => $chat['assistant']];
        }
        
        // Append new message
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        // 6. Call Mistral AI API
        $apiKey = env('MISTRAL_API_KEY', 'GysR79wNoYf9i763EAlv8Q58VcrPwhCq');
        $model = env('MISTRAL_MODEL', 'mistral-small-latest');
        $apiUrl = 'https://api.mistral.ai/v1/chat/completions';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->withOptions([
                'verify' => false, // For local environment compatibility
                'connect_timeout' => 5,
                'timeout' => 30,
            ])->post($apiUrl, [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.2
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $aiResponse = $result['choices'][0]['message']['content'] ?? 'Maaf, saya tidak menerima respon yang valid dari server AI.';
                
                // Save to history
                $history[] = [
                    'user' => $userMessage,
                    'assistant' => $aiResponse,
                    'timestamp' => date('c')
                ];
                session()->put('asisten_sikap_chat_history', $history);

                return response()->json([
                    'success' => true,
                    'message' => $aiResponse,
                    'history' => $history
                ]);
            } else {
                Log::error('Mistral API Chat Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, server AI sedang mengalami kendala teknis (HTTP ' . $response->status() . '). Silakan coba beberapa saat lagi.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Asisten Sikap Exception', [
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menghubungi Asisten Sikap. Silakan coba kembali.'
            ], 500);
        }
    }

    /**
     * Reset the conversation history.
     */
    public function reset()
    {
        session()->forget('asisten_sikap_chat_history');
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Riwayat obrolan berhasil dihapus.'
            ]);
        }
        
        return redirect()->route('admin.asisten-sikap.index')->with('status', 'Riwayat obrolan berhasil dibersihkan.');
    }

    /**
     * Tokenize query string and extract keywords.
     */
    private function getKeywords($query)
    {
        $stopWords = [
            'dan', 'yang', 'untuk', 'dari', 'atau', 'di', 'ke', 'ini', 'itu', 'ada', 'apa', 'bisa', 'saya', 'kamu',
            'ingin', 'tahu', 'tentang', 'bagaimana', 'apakah', 'adalah', 'dengan', 'pada', 'oleh', 'sebagai',
            'bahwa', 'secara', 'akan', 'telah', 'sudah', 'karena', 'jika', 'maka', 'namun', 'tetapi', 'saja',
            'juga', 'peraturan', 'tentang', 'mengenai', 'terkait', 'tolong', 'jelaskan', 'coba', 'sebutkan', 'tampilkan',
            'mencari', 'mencocokkan', 'butuh', 'info'
        ];

        // Clean character-sequences except alphanumeric and spaces
        $cleanQuery = preg_replace('/[^\w\s-]/', '', strtolower($query));
        $words = preg_split('/\s+/', $cleanQuery);

        $keywords = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) > 2 && !in_array($word, $stopWords)) {
                $keywords[] = $word;
            }
        }
        return array_unique($keywords);
    }
}
