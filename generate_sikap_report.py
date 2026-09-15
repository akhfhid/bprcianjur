import os, zipfile, html, shutil

def escape_xml(s):
    if s is None:
        return ''
    return html.escape(str(s)).replace('"', '&quot;').replace("'", '&apos;')

def run(text, bold=False, italic=False, underline=False, font='Arial', size=None, shd=None, br=False, lang='id-ID'):
    rpr = []
    if font:
        rpr.append(f'<w:rFonts w:ascii="{font}" w:hAnsi="{font}" w:cs="{font}"/>')
    if bold:
        rpr.append('<w:b/><w:bCs/>')
    if italic:
        rpr.append('<w:i/><w:iCs/>')
    if underline:
        rpr.append('<w:u w:val="single"/>')
    if size:
        rpr.append(f'<w:sz w:val="{size}"/><w:szCs w:val="{size}"/>')
    if shd:
        rpr.append(f'<w:shd w:val="clear" w:color="auto" w:fill="{shd}"/>')
    if lang:
        rpr.append(f'<w:lang w:val="{lang}"/>')
    
    rpr_str = f'<w:rPr>{" ".join(rpr)}</w:rPr>' if rpr else ''
    parts = []
    if br:
        parts.append('<w:br/>')
    if text:
        parts.append(f'<w:t xml:space="preserve">{escape_xml(text)}</w:t>')
    return f'<w:r>{rpr_str}{" ".join(parts)}</w:r>'

def p(runs, jc='both', first_line=0, space_after=100, space_before=0, line=276, style='NormalWeb'):
    p_pr = []
    if style:
        p_pr.append(f'<w:pStyle w:val="{style}"/>')
    if jc:
        p_pr.append(f'<w:jc w:val="{jc}"/>')
    if first_line > 0:
        p_pr.append(f'<w:ind w:firstLine="{first_line}"/>')
    
    sp_attr = []
    if space_after > 0:
        sp_attr.append(f'w:after="{space_after}"')
    if space_before > 0:
        sp_attr.append(f'w:before="{space_before}"')
    if line:
        sp_attr.append(f'w:line="{line}" w:lineRule="auto"')
    if sp_attr:
        p_pr.append(f'<w:spacing {" ".join(sp_attr)}/>')
        
    p_pr_str = f'<w:pPr>{" ".join(p_pr)}</w:pPr>' if p_pr else ''
    if isinstance(runs, str):
        runs = [runs]
    return f'<w:p>{p_pr_str}{" ".join(runs)}</w:p>'

def make_table(headers, rows, col_widths=None, table_width=5000):
    num_cols = len(headers) if headers else (len(rows[0]) if rows else 1)
    if not col_widths:
        w_per_col = 9016 // num_cols
        col_widths = [w_per_col] * num_cols
        col_widths[-1] = 9016 - sum(col_widths[:-1])
    
    tbl_pr = f'''<w:tblPr>
        <w:tblW w:w="{table_width}" w:type="pct"/>
        <w:tblBorders>
            <w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/>
            <w:left w:val="single" w:sz="4" w:space="0" w:color="auto"/>
            <w:bottom w:val="single" w:sz="4" w:space="0" w:color="auto"/>
            <w:right w:val="single" w:sz="4" w:space="0" w:color="auto"/>
            <w:insideH w:val="single" w:sz="4" w:space="0" w:color="auto"/>
            <w:insideV w:val="single" w:sz="4" w:space="0" w:color="auto"/>
        </w:tblBorders>
        <w:tblLayout w:type="fixed"/>
        <w:tblCellMar>
            <w:top w:w="80" w:type="dxa"/>
            <w:left w:w="120" w:type="dxa"/>
            <w:bottom w:w="80" w:type="dxa"/>
            <w:right w:w="120" w:type="dxa"/>
        </w:tblCellMar>
    </w:tblPr>'''
    
    tr_list = []
    
    if headers:
        tc_list = []
        for i, h in enumerate(headers):
            w = col_widths[i]
            cell_p = p([run(h, bold=True, font='Arial')], jc='left', space_after=60, line=240, style='NormalWeb')
            tc_list.append(f'<w:tc><w:tcPr><w:tcW w:w="{w}" w:type="dxa"/><w:shd w:val="clear" w:color="auto" w:fill="F1F5F9"/></w:tcPr>{cell_p}</w:tc>')
        tr_list.append(f'<w:tr><w:trPr><w:tblHeader/></w:trPr>{" ".join(tc_list)}</w:tr>')
        
    for r in rows:
        tc_list = []
        for i, cell_val in enumerate(r):
            w = col_widths[i] if i < len(col_widths) else col_widths[-1]
            is_bold = (i == 0 and len(r) == 2 and not headers) or (i == 0 and len(r) == 3 and headers and 'Rating' in headers[-1])
            cell_p = p([run(str(cell_val), bold=is_bold, font='Arial')], jc='left', space_after=60, line=240, style='NormalWeb')
            tc_list.append(f'<w:tc><w:tcPr><w:tcW w:w="{w}" w:type="dxa"/></w:tcPr>{cell_p}</w:tc>')
        tr_list.append(f'<w:tr>{" ".join(tc_list)}</w:tr>')
        
    return f'<w:tbl>{tbl_pr}{" ".join(tr_list)}</w:tbl>'

def make_signature_table():
    tbl_pr = '''<w:tblPr>
        <w:tblW w:w="10199" w:type="dxa"/>
        <w:jc w:val="center"/>
        <w:tblBorders>
            <w:top w:val="nil"/>
            <w:left w:val="nil"/>
            <w:bottom w:val="nil"/>
            <w:right w:val="nil"/>
            <w:insideH w:val="nil"/>
            <w:insideV w:val="nil"/>
        </w:tblBorders>
        <w:tblLayout w:type="fixed"/>
        <w:tblCellMar>
            <w:top w:w="0" w:type="dxa"/>
            <w:left w:w="108" w:type="dxa"/>
            <w:bottom w:w="0" w:type="dxa"/>
            <w:right w:w="108" w:type="dxa"/>
        </w:tblCellMar>
    </w:tblPr>'''
    
    # Row 1: Menyetujui
    tc1 = '''<w:tc>
        <w:tcPr><w:tcW w:w="5596" w:type="dxa"/><w:tcBorders><w:top w:val="nil"/><w:left w:val="nil"/><w:bottom w:val="nil"/><w:right w:val="nil"/></w:tcBorders></w:tcPr>
        ''' + p([run('Menyetujui,', font='Arial')], jc='center', space_after=0, line=276) + '''
        ''' + p([], jc='center', space_after=0, line=276) + '''
        ''' + p([], jc='center', space_after=0, line=276) + '''
        ''' + p([], jc='center', space_after=0, line=276) + '''
        ''' + p([run('M. Luthfi Mubarok', bold=False, underline=True, font='Arial')], jc='center', space_after=0, line=276) + '''
        ''' + p([run('Kepala Bagian TI', font='Arial')], jc='center', space_after=0, line=276) + '''
    </w:tc>'''
    
    tc2 = '''<w:tc>
        <w:tcPr><w:tcW w:w="4603" w:type="dxa"/><w:tcBorders><w:top w:val="nil"/><w:left w:val="nil"/><w:bottom w:val="nil"/><w:right w:val="nil"/></w:tcBorders></w:tcPr>
        ''' + p([], jc='center', space_after=0, line=276) + '''
        ''' + p([], jc='center', space_after=0, line=276) + '''
        ''' + p([], jc='center', space_after=0, line=276) + '''
        ''' + p([], jc='center', space_after=0, line=276) + '''
        ''' + p([run('Affan Khulafa Hidayah', bold=False, underline=True, font='Arial')], jc='center', space_after=0, line=276) + '''
        ''' + p([run('Staf TI', font='Arial')], jc='center', space_after=0, line=276) + '''
    </w:tc>'''
    
    tr1 = f'<w:tr><w:trPr><w:jc w:val="center"/></w:trPr>{tc1}{tc2}</w:tr>'
    
    # Row 2: Mengetahui
    tc_mengetahui = '''<w:tc>
        <w:tcPr><w:tcW w:w="10199" w:type="dxa"/><w:gridSpan w:val="2"/><w:tcBorders><w:top w:val="nil"/><w:left w:val="nil"/><w:bottom w:val="nil"/><w:right w:val="nil"/></w:tcBorders></w:tcPr>
        ''' + p([], jc='center', space_after=0, line=276) + '''
        ''' + p([run('Mengetahui,', font='Arial')], jc='center', space_after=0, line=276) + '''
        ''' + p([], jc='center', space_after=0, line=276) + '''
        ''' + p([], jc='center', space_after=0, line=276) + '''
        ''' + p([], jc='center', space_after=0, line=276) + '''
        ''' + p([run('Dimas Setyadi', bold=False, underline=True, font='Arial')], jc='center', space_after=0, line=276) + '''
        ''' + p([run('Kepala Divisi Perencanaan & TI', font='Arial')], jc='center', space_after=0, line=276) + '''
    </w:tc>'''
    
    tr2 = f'<w:tr><w:trPr><w:trHeight w:val="1824"/><w:jc w:val="center"/></w:trPr>{tc_mengetahui}</w:tr>'
    
    return f'<w:tbl>{tbl_pr}{tr1}{tr2}</w:tbl>'

def generate_body():
    elems = []
    
    # 0. Header Title
    title_runs = [
        run('BERITA ACARA', bold=True, font='Arial'),
        run('', br=True),
        run('PENGUJIAN INFRASTRUKTUR DAN LOAD TEST', bold=True, font='Arial'),
        run('', br=True),
        run('APLIKASI SIKAP (SISTEM INFORMASI KEPEGAWAIAN DAN PERATURAN)', bold=True, font='Arial'),
        run('', br=True),
        run('PT BPR CIANJUR JABAR (PERSERODA)', bold=True, font='Arial')
    ]
    elems.append(p(title_runs, jc='center', space_after=200, space_before=100, style='isselectedend'))
    
    # 1. Opening Paragraph
    p1 = ("Pada hari Jumat, tanggal Dua Puluh Delapan bulan Agustus tahun Dua Ribu Dua Puluh Enam (28-08-2026), "
          "telah dilakukan pengujian infrastruktur dan performa Aplikasi SIKAP. Pengujian dilakukan dengan metode "
          "stress test nyata menggunakan skenario 50 concurrent users untuk mengetahui kemampuan aplikasi dan infrastruktur "
          "server dalam menangani akses pengguna secara bersamaan, serta untuk memastikan kestabilan sistem dari sisi penggunaan "
          "CPU, RAM, bandwidth, database, response time, dan tingkat keberhasilan request.")
    elems.append(p([run(p1, font='Arial')], jc='both', first_line=720, space_after=100, style='isselectedend'))
    
    # Tools Intro
    elems.append(p([run('Pengujian menggunakan tool :', font='Arial')], jc='left', space_after=60, style='NormalWeb'))
    
    # Tool items
    tools = [
        ('ab (Apache Bench v2.3)', ' — load/stress testing HTTP'),
        ('top -bn1', ' — monitoring CPU real-time saat test berjalan'),
        ('free -h', ' — monitoring RAM real-time'),
        ('mysql / php artisan tinker', ' — query statistik MySQL & InnoDB'),
        ('curl', ' — pengukuran response time per-endpoint')
    ]
    for t_cmd, t_desc in tools:
        elems.append(p([
            run(t_cmd, bold=False, font='Courier New', shd='F1F5F9'),
            run(t_desc, font='Arial')
        ], jc='left', first_line=360, space_after=60, style='DaftarParagraf'))
        
    # Baseline Intro
    p_before = ("Sebelum dilakukan stress test, aplikasi SIKAP sudah digunakan oleh ±16–52 pengguna aktif "
                "dalam operasional harian (dari total 176 pengguna terdaftar). Data berikut adalah hasil dari monitoring server:")
    elems.append(p([run(p_before, font='Arial')], jc='both', space_after=100, style='isselectedend'))
    
    # Table 1: Kondisi Real
    t1_headers = ['Metrik', 'Kondisi Real (Operasional SIKAP)']
    t1_rows = [
        ['Pengguna terdaftar', '176 pengguna (175 pegawai aktif)'],
        ['Pengguna aktif', '±16–52 orang (operasional harian)'],
        ['Response time per-request', '15–25 ms (diukur via curl)'],
        ['CPU penggunaan', '2–5% saat jam kerja normal'],
        ['RAM terpakai', '6.5 GB / 39 GB (16.6%)'],
        ['Koneksi database aktif', '1–5 koneksi'],
        ['Error rate', '0% (tidak ada laporan error)'],
        ['Uptime server', '50 hari tanpa restart']
    ]
    elems.append(make_table(t1_headers, t1_rows, [3082, 5934]))
    
    # Catatan Table 1
    p_note = "Catatan penting: Data di atas adalah kondisi nyata operasional, bukan hasil stress test. sehingga server sangat santai."
    elems.append(p([run(p_note, italic=True, font='Arial')], jc='both', space_after=140, space_before=80, style='isselectedend'))
    
    # 1. Spesifikasi Infrastruktur Server
    elems.append(p([run('1.  Spesifikasi Infrastruktur Server', bold=True, font='Arial')], jc='left', space_after=100, space_before=140, style='NormalWeb'))
    t2_headers = ['Komponen', 'Detail']
    t2_rows = [
        ['CPU', 'Intel® Xeon® Gold 5218R CPU @ 2.10GHz'],
        ['Jumlah Core', '4 vCPU (2 Socket × 2 Core/socket)'],
        ['Total RAM', '39 GB'],
        ['Storage', '491 GB (HDD/SSD) — terpakai: 56 GB (12%)'],
        ['OS', 'Ubuntu 18.04.6 LTS (Kernel 5.4.0-150-generic)'],
        ['Web Server', 'Nginx 1.18.0'],
        ['PHP Runtime', 'PHP 7.2.34 (FPM — mode: dynamic)'],
        ['Database', 'MySQL 5.7.42'],
        ['Framework', 'Laravel 7.0 + Bootstrap 4 + jQuery'],
        ['Auth', 'Laravel Auth + Session Guard (Lifetime: 15m)']
    ]
    elems.append(make_table(t2_headers, t2_rows, [3082, 5934]))
    
    # 2. Kondisi Baseline
    elems.append(p([run('2.  Kondisi Baseline (Sebelum Test)', bold=True, font='Arial')], jc='left', space_after=100, space_before=140, style='NormalWeb'))
    t3_headers = ['Metrik', 'Nilai']
    t3_rows = [
        ['CPU Idle', '~95% idle (penggunaan aktif: ~5%)'],
        ['RAM Terpakai', '6.5 GB / 39 GB (~16.6%)'],
        ['RAM Tersedia', '31.0 GB'],
        ['Load Average (1m/5m/15m)', '0.37 / 0.60 / 0.51'],
        ['Proses PHP-FPM', '13 worker aktif'],
        ['Worker Nginx', '5 worker aktif'],
        ['Koneksi DB Aktif', '5 koneksi'],
        ['Uptime Server', '50 hari']
    ]
    elems.append(make_table(t3_headers, t3_rows, [4508, 4508]))
    
    # 3. Hasil Load Test
    elems.append(p([run('3.  Hasil Load Test', bold=True, font='Arial')], jc='left', space_after=100, space_before=140, style='NormalWeb'))
    
    # Test 1
    elems.append(p([run('Test 1 — Homepage / (500 Requests, 50 Concurrent Users)', bold=True, font='Arial')], jc='left', space_after=60, style='NormalWeb'))
    t1_stat = ("Concurrency Level:      50        Time taken for tests:   4.850 seconds        "
               "Complete requests:      500        Failed requests:        0        "
               "Requests per second:    103.09 [#/sec] (mean)        Time per request (mean): 485.021 ms        "
               "Transfer rate:          1,520.40 KB/sec")
    elems.append(p([run(t1_stat, font='Courier New', size='18')], jc='left', space_after=80, style='NormalWeb'))
    t4_headers = ['Persentil', 'Response Time']
    t4_rows = [
        ['50% (median)', '420 ms'],
        ['75%', '465 ms'],
        ['90%', '512 ms'],
        ['95%', '540 ms'],
        ['99%', '610 ms'],
        ['100% (max)', '635 ms']
    ]
    elems.append(make_table(t4_headers, t4_rows, [4508, 4508]))
    
    # Test 2
    elems.append(p([run('Test 2 — Login Page /login (500 Requests, 50 Concurrent Users)', bold=True, font='Arial')], jc='left', space_after=60, space_before=100, style='NormalWeb'))
    t2_stat = ("Concurrency Level:      50        Time taken for tests:   5.120 seconds        "
               "Complete requests:      500        Failed requests:        0        "
               "Requests per second:    97.65 [#/sec] (mean)        Time per request (mean): 512.012 ms        "
               "Transfer rate:          1,480.20 KB/sec")
    elems.append(p([run(t2_stat, font='Courier New', size='18')], jc='left', space_after=80, style='NormalWeb'))
    t5_headers = ['Persentil', 'Response Time']
    t5_rows = [
        ['50% (median)', '435 ms'],
        ['75%', '480 ms'],
        ['90%', '530 ms'],
        ['95%', '565 ms'],
        ['99%', '620 ms'],
        ['100% (max)', '650 ms']
    ]
    elems.append(make_table(t5_headers, t5_rows, [4508, 4508]))
    
    # Test 3
    elems.append(p([run('Test 3 — Modul Peraturan & Cuti (500 Requests, 50 Concurrent Users)', bold=True, font='Arial')], jc='left', space_after=60, space_before=100, style='NormalWeb'))
    t3_stat = ("Concurrency Level:      50        Time taken for tests:   5.280 seconds        "
               "Complete requests:      500        Failed requests:        0        "
               "Requests per second:    94.69 [#/sec] (mean)        Time per request (mean): 528.040 ms        "
               "Transfer rate:          1,610.15 KB/sec")
    elems.append(p([run(t3_stat, font='Courier New', size='18')], jc='left', space_after=80, style='NormalWeb'))
    t6_headers = ['Persentil', 'Response Time']
    t6_rows = [
        ['50% (median)', '450 ms'],
        ['90%', '545 ms'],
        ['95%', '580 ms'],
        ['99%', '640 ms'],
        ['100% (max)', '675 ms']
    ]
    elems.append(make_table(t6_headers, t6_rows, [4508, 4508]))
    
    # 4. CPU Usage
    elems.append(p([run('4. CPU Usage Selama Load Test (Real-time Monitor)', bold=True, font='Arial')], jc='left', space_after=60, space_before=140, style='NormalWeb'))
    elems.append(p([run('Sampel diambil setiap 1–2 detik saat berjalan:', font='Arial')], jc='left', space_after=60, style='NormalWeb'))
    t7_headers = ['Waktu Sampel', 'CPU User %', 'CPU System %', 'CPU Idle %']
    t7_rows = [
        ['Baseline (sebelum)', '2.1%', '0.8%', '97.1%'],
        ['Sampel 1 (14:00:02)', '18.5%', '3.2%', '~78%'],
        ['Sampel 2 (14:00:05)', '29.4%', '5.1%', '~65%'],
        ['Sampel 3 (14:00:08)', '38.2%', '6.4%', '~55%'],
        ['Sampel 4 (14:00:11)', '21.0%', '4.0%', '~75%'],
        ['Sampel 5 (14:00:14)', '8.5%', '1.8%', '~89%'],
        ['Sampel 6 (14:00:17)', '36.8%', '5.8%', '~57%'],
        ['Sampel 7 (14:00:20)', '39.5%', '6.5%', '~54%'],
        ['Sampel 8 (14:00:23)', '10.2%', '2.0%', '~87%'],
        ['Selama Stress Test S1–S8', '35.0–39.5%', '5–7%', '~54–60%']
    ]
    elems.append(make_table(t7_headers, t7_rows, [2254, 2254, 2254, 2254]))
    elems.append(p([run('Temuan: CPU melonjak dari 2% → 39.5% saat 50 concurrent users aktif. Pola naik-turun disebabkan PHP-FPM memproses request dalam batch. Tidak ada CPU bottleneck — masih tersisa ~54–60% kapasitas idle.', font='Arial')], jc='both', space_after=100, space_before=80, style='NormalWeb'))
    
    # 5. RAM Usage
    elems.append(p([run('5.  RAM Usage Selama Load Test', bold=True, font='Arial')], jc='left', space_after=100, space_before=140, style='NormalWeb'))
    t8_headers = ['Fase', 'RAM Terpakai', 'RAM Tersedia', 'Buffer/Cache']
    t8_rows = [
        ['Baseline', '6.5 GB', '31.0 GB', '10.0 GB'],
        ['Selama Test (peak)', '6.8 GB', '30.7 GB', '10.0 GB'],
        ['Selama Stress Test', '6.7–6.8 GB', '30.7–30.8 GB', '10.0 GB'],
        ['Swap Terpakai', '0 MB', '—', '—']
    ]
    elems.append(make_table(t8_headers, t8_rows, [2254, 2254, 2254, 2254]))
    elems.append(p([run('Temuan: RAM sangat stabil — hanya naik ~300 MB saat load 50 user. Buffer/cache tetap konstan, menandakan tidak ada memory pressure. Swap tidak terpakai sama sekali (0 MB dari 979 MB). RAM tidak menjadi bottleneck sama sekali.', font='Arial')], jc='both', space_after=100, space_before=80, style='NormalWeb'))
    
    # 6. Bandwidth Usage
    elems.append(p([run('6. Bandwidth Usage', bold=True, font='Arial')], jc='left', space_after=100, space_before=140, style='NormalWeb'))
    t9_headers = ['Test', 'Total Data Ditransfer', 'Durasi', 'Throughput']
    t9_rows = [
        ['Test 1 (Homepage×500)', '7.6 MB', '4.85 detik', '1,520 KB/s (~1.5 MB/s)'],
        ['Test 2 (Login×500)', '7.4 MB', '5.12 detik', '1,480 KB/s (~1.4 MB/s)'],
        ['Test 3 (Layanan Pegawai×500)', '8.5 MB', '5.28 detik', '1,610 KB/s (~1.6 MB/s)']
    ]
    elems.append(make_table(t9_headers, t9_rows, [2700, 2100, 1800, 2416]))
    
    bw_paragraphs = [
        'Rata-rata per request:',
        'Ukuran halaman: ~7.38 KB/request',
        'Bandwidth per-user: ~7.38–15 KB/halaman dimuat',
        'Proyeksi untuk 50 pengguna aktif (asumsi 5 request/menit/user):',
        'Load: 50 × 5 = 250 request/menit = ~4.2 req/s',
        'Bandwidth: 4.2 × 10 KB = ~42 KB/s (~0.34 Mbps)',
        'Server terukur mampu: 9.3 MB/s (74 Mbps)',
        'Kapasitas bandwidth server puluhan kali lebih besar dari kebutuhan nyata.'
    ]
    for bwp in bw_paragraphs:
        is_b = 'Rata-rata' in bwp or 'Proyeksi' in bwp
        elems.append(p([run(bwp, bold=is_b, font='Arial')], jc='left', space_after=40, style='NormalWeb'))
        
    # 7. Database MySQL
    elems.append(p([run('7.  Database MySQL — Statistik', bold=True, font='Arial')], jc='left', space_after=100, space_before=140, style='NormalWeb'))
    t10_headers = ['Metrik', 'Nilai']
    t10_rows = [
        ['Database', 'bprcianjur'],
        ['Koneksi Aktif', '5 (normal post-test)'],
        ['Total Commit', '1,441,620'],
        ['Total Rollback', '1 (0.00007%)'],
        ['Cache Hit Ratio (Buffer Pool)', '99.99%'],
        ['Blok Terbaca dari Disk', '1,072'],
        ['Blok dari Cache (Buffer Requests)', '22,590,342'],
        ['Tuple Returned / Rows Read', '38,538,610'],
        ['Tuple Fetched / Rows Inserted', '379,246']
    ]
    elems.append(make_table(t10_headers, t10_rows, [4508, 4508]))
    elems.append(p([run('Temuan: Cache hit ratio 99.99% — data hampir seluruhnya dilayani dari RAM MySQL (InnoDB Buffer Pool), sangat minim I/O disk. Database berada dalam kondisi sangat optimal. Rollback rate hanya 0.00007% menandakan tidak ada konflik transaksi.', font='Arial')], jc='both', space_after=100, space_before=80, style='NormalWeb'))
    
    # 8. Ringkasan Performa
    elems.append(p([run('8. Ringkasan Performa', bold=True, font='Arial')], jc='left', space_after=100, space_before=140, style='NormalWeb'))
    t11_headers = ['Metrik', 'Nilai Terukur', 'Rating']
    t11_rows = [
        ['Throughput', '94–103 request/detik', 'Sangat Baik'],
        ['Error Rate', '0% (0/1500 request)', 'Sempurna'],
        ['Response Time Median', '420–450 ms', 'Dapat Diterima'],
        ['Response Time P95', '540–580 ms', 'Normal'],
        ['Response Time Max', '635–675 ms', 'Aman'],
        ['CPU Peak', '~39.5% (4 core)', 'Masih Tersisa 60%'],
        ['RAM Naik', '+300 MB saat load', 'Sangat Stabil'],
        ['Bandwidth Terpakai', '~1.6 MB/s puncak', 'Jauh di Bawah Kapasitas'],
        ['DB Cache Hit', '99.99%', 'Optimal'],
        ['Swap Usage', '0 MB / 979 MB', 'Hampir 0']
    ]
    elems.append(make_table(t11_headers, t11_rows, [3000, 3200, 2816]))
    
    # 9. Apakah Aman untuk 50 Pengguna Aktif?
    elems.append(p([run('9. Apakah Aman untuk 50 Pengguna Aktif?', bold=True, font='Arial')], jc='left', space_after=80, space_before=140, style='NormalWeb'))
    elems.append(p([run('Berdasarkan hasil uji stress test dengan 50 pengguna (1500 request total, 0 error):', font='Arial')], jc='left', space_after=80, style='NormalWeb'))
    t12_headers = ['Aspek', 'Status', 'Penjelasan']
    t12_rows = [
        ['CPU', 'Aman', 'Puncak 39.5% dari 4 core — masih ada 60% headroom'],
        ['RAM', 'Aman', 'Hanya naik 300 MB — 31 GB masih tersedia'],
        ['Error Rate', 'Sempurna', '0 error dari 1500 request'],
        ['Response Time', 'Wajar', 'Median 420–450ms (< 500ms threshold web interaktif)'],
        ['Database', 'Optimal', 'Cache hit 99.99%, hampir tanpa disk I/O'],
        ['Bandwidth', 'Aman', '1.6 MB/s terukur — kapasitas masih sangat luas']
    ]
    elems.append(make_table(t12_headers, t12_rows, [1800, 1800, 5416]))
    
    # 10. Konfigurasi Stack Saat Ini
    elems.append(p([run('10.  Konfigurasi Stack Saat Ini', bold=True, font='Arial')], jc='left', space_after=100, space_before=140, style='NormalWeb'))
    
    stack_ascii = """┌─────────────────────────────────────────────────────────┐
│                    CLIENT (Browser)                     │
├─────────────────────────────────────────────────────────┤
│         Nginx 1.18.0 (Port 80, Host: 192.168.21.8)      │
│         max_body_size: 1000M                            │
├─────────────────────────────────────────────────────────┤
│         PHP-FPM 7.2 (Unix Socket)                       │
│         pm: dynamic | workers: 13 aktif                 │
├─────────────────────────────────────────────────────────┤
│         Laravel 7.0 + Bootstrap 4 + jQuery              │
│         Yajra DataTables + Laravel Auth                 │
├─────────────────────────────────────────────────────────┤
│         MySQL 5.7.42 (DB: bprcianjur)                   │
│         Cache Hit: 99.99% | Connections: 5              │
└─────────────────────────────────────────────────────────┘"""
    elems.append(p([run(stack_ascii, font='Courier New', size='18')], jc='center', space_after=120, space_before=60, line=220, style='NormalWeb'))
    
    p_net = ("Pengujian load test ini dilakukan pada lingkungan jaringan internal dengan perangkat penguji "
             "yang berada pada segment jaringan 192.168.21.xx. Hasil pengujian yang diperoleh menggambarkan performa "
             "aplikasi dan infrastruktur server berdasarkan kondisi akses dari segment jaringan tersebut. Pengujian ini "
             "belum mencakup akses pengguna yang berada pada segment jaringan 192.168.22.xx, sehingga performa aplikasi "
             "dari segment jaringan 192.168.22.xx belum menjadi bagian dari hasil pengujian ini. Perbedaan kondisi jaringan, "
             "jalur koneksi, routing, bandwidth, maupun latency antar-segment dapat memengaruhi response time dan performa "
             "akses aplikasi pada masing-masing jaringan.")
    elems.append(p([run(p_net, font='Arial')], jc='both', first_line=720, space_after=140, style='isselectedend'))
    
    # Signatures header
    elems.append(p([run('Cianjur, 28 Agustus 2026', font='Arial')], jc='left', space_after=40, space_before=140, style='NormalWeb'))
    elems.append(p([run('PT BPR CIANJUR JABAR', bold=True, font='Arial')], jc='left', space_after=40, style='NormalWeb'))
    elems.append(p([run('DIVISI PERENCANAAN & TI', bold=True, font='Arial')], jc='left', space_after=120, style='NormalWeb'))
    
    # Signature Table
    elems.append(make_signature_table())
    
    # Final Section Properties
    sect_pr = '''<w:sectPr>
        <w:pgSz w:w="11906" w:h="16838"/>
        <w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="708" w:footer="708" w:gutter="0"/>
        <w:cols w:space="720"/>
        <w:docGrid w:linePitch="360"/>
    </w:sectPr>'''
    elems.append(sect_pr)
    
    return "".join(elems)

def build_docx(output_path):
    body_xml = generate_body()
    
    doc_xml_template = f'''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:wpc="http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas" 
    xmlns:cx="http://schemas.microsoft.com/office/drawing/2014/chartex" 
    xmlns:cx1="http://schemas.microsoft.com/office/drawing/2015/9/8/chartex" 
    xmlns:cx2="http://schemas.microsoft.com/office/drawing/2015/10/21/chartex" 
    xmlns:cx3="http://schemas.microsoft.com/office/drawing/2016/5/9/chartex" 
    xmlns:cx4="http://schemas.microsoft.com/office/drawing/2016/5/10/chartex" 
    xmlns:cx5="http://schemas.microsoft.com/office/drawing/2016/5/11/chartex" 
    xmlns:cx6="http://schemas.microsoft.com/office/drawing/2016/5/12/chartex" 
    xmlns:cx7="http://schemas.microsoft.com/office/drawing/2016/5/13/chartex" 
    xmlns:cx8="http://schemas.microsoft.com/office/drawing/2016/5/14/chartex" 
    xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" 
    xmlns:aink="http://schemas.microsoft.com/office/drawing/2016/ink" 
    xmlns:am3d="http://schemas.microsoft.com/office/drawing/2017/model3d" 
    xmlns:o="urn:schemas-microsoft-com:office:office" 
    xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" 
    xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math" 
    xmlns:v="urn:schemas-microsoft-com:vml" 
    xmlns:wp14="http://schemas.microsoft.com/office/word/2010/wordprocessingDrawing" 
    xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" 
    xmlns:w10="urn:schemas-microsoft-com:office:word" 
    xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" 
    xmlns:w14="http://schemas.microsoft.com/office/word/2010/wordml" 
    xmlns:w15="http://schemas.microsoft.com/office/word/2012/wordml" 
    xmlns:w16cex="http://schemas.microsoft.com/office/word/2018/wordml/cex" 
    xmlns:w16cid="http://schemas.microsoft.com/office/word/2016/wordml/cid" 
    xmlns:w16="http://schemas.microsoft.com/office/word/2018/wordml" 
    xmlns:w16sdtdh="http://schemas.microsoft.com/office/word/2020/wordml/sdtdatahash" 
    xmlns:w16se="http://schemas.microsoft.com/office/word/2015/wordml/symex" 
    xmlns:wpg="http://schemas.microsoft.com/office/word/2010/wordprocessingGroup" 
    xmlns:wpi="http://schemas.microsoft.com/office/word/2010/wordprocessingInk" 
    xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml" 
    xmlns:wps="http://schemas.microsoft.com/office/word/2010/wordprocessingShape" 
    mc:Ignorable="w14 w15 w16se w16cid w16 w16cex w16sdtdh wp14">
    <w:body>
        {body_xml}
    </w:body>
</w:document>'''

    scratch_dir = '/var/www/bprcianjur/scratch/sikap_doc'
    os.makedirs(os.path.join(scratch_dir, 'word'), exist_ok=True)
    
    with open(os.path.join(scratch_dir, 'word', 'document.xml'), 'w', encoding='utf-8') as f:
        f.write(doc_xml_template)
        
    with zipfile.ZipFile(output_path, 'w', zipfile.ZIP_DEFLATED) as zout:
        for root, dirs, files in os.walk(scratch_dir):
            for file in files:
                abs_path = os.path.join(root, file)
                rel_path = os.path.relpath(abs_path, scratch_dir)
                zout.write(abs_path, rel_path)
                
    print(f'Successfully built docx: {output_path} ({os.path.getsize(output_path)} bytes)')

if __name__ == '__main__':
    build_docx('/var/www/bprcianjur/Laporan_Pengujian_Infrastruktur_SIKAP.docx')
    build_docx('/var/www/bprcianjur/converted-document-sikap.docx')
