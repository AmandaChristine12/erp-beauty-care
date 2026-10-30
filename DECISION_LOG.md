# DECISION LOG & DECISION DEFENSE
**Proyek ERP:** PT Arunika Beauty Nusantara (Brand: NusaGlow)  
**Pemilik Dokumen (Owner):** Anggota 1 (Product Owner / Business Analyst / Project Manager)  
**Status:** Baseline Final — Checkpoint Minggu 1  

---

## 1. Pendahuluan & Filosofi Pengambilan Keputusan

Setiap pemangku kepentingan (*stakeholders*) dalam skenario PT Arunika Beauty Nusantara memiliki kepentingan bisnis yang rasional, namun sebagian kebutuhan saling bertentangan secara operasional dan akuntansi. Dokumen ini merekam evaluasi alternatif, keputusan akhir, pihak yang menanggung konsekuensi (*trade-off / worse off*), mitigasi bisnis, serta pemetaan bukti pengujian (*Test Case*).

Prinsip dasar yang dipegang:
1. **Integritas Akuntansi & Kebenaran Stok di atas Kemudahan Transaksi:** Mencegah pencatatan piutang fiktif, stok negatif, dan utang atas barang rusak.
2. **Kesesuaian dengan Praktik Terbaik ERP (Odoo 17 Web Standard):** Menerapkan kebijakan *Delivered Quantities*, *Three-Way Matching*, dan *Idempotency*.
3. **Keterlacakan Penuh (*Auditability*):** Setiap dokumen memiliki nomor urut unik dan perubahan data tercatat dalam jejak audit.

---

## 2. Matriks Resolusi Konflik Utama (Decision Defense)

Tabel ini merupakan format wajib sesuai pedoman presentasi (Halaman 35):

| Problem / Konflik (SR) | Opsi yang Dipertimbangkan | Keputusan & Fitur Terkait | Pihak yang Dirugikan (*Worse Off*) & Mitigasi | Bukti Pengujian (*Evidence T*) |
| :--- | :--- | :--- | :--- | :--- |
| **SR-01 vs SR-02**<br>Kebijakan waktu penerbitan faktur pelanggan (*Customer Invoice*). | **Opsi A:** Faktur dibuat 100% saat SO dikonfirmasi sebelum pengiriman (Ratih Widya - Direktur).<br><br>**Opsi B:** Faktur dibatasi hanya sebesar kuantitas terkirim neto yang sudah divalidasi (*Delivered Quantities*) (Nanda Kusuma - Kepala Finance). | **Memilih Opsi B**<br>(Fitur: `F-10`, Layar: `S-08`).<br>Faktur fisik wajib menggunakan kebijakan *Delivered quantities* sesuai keputusan pemodelan resmi (Halaman 5). | **Direktur dirugikan** (arus kas masuk tidak bisa ditarik di awal sebelum barang jalan).<br><br>**Mitigasi:** Gudang menerapkan SLA pengiriman ekspres dan sistem mendukung *Partial Invoicing* per pengiriman parsial. | **T-15:** Invoice terbit Rp266.400 untuk 2 unit yang terkirim.<br><br>**T-16:** Sistem menolak/mencegah pembuatan invoice 4 unit jika pengiriman fisik belum terjadi. |
| **SR-03 vs SR-04**<br>Janji penjualan saat ketersediaan stok di sistem kurang. | **Opsi A:** Sales dapat mengonfirmasi pesanan dan menjanjikan pengiriman meski stok *Available* kurang (Kevin Mahendra - Sales Manager).<br><br>**Opsi B:** Reservasi dan pengeluaran barang dilarang membuat *Available* atau *On Hand* lokasi menjadi negatif (Wahyu Saputra - Kepala Gudang). | **Sintesis Terkendali**<br>(Fitur: `F-08`, `F-09`, Layar: `S-06`, `S-07`).<br>Reservasi dibatasi maksimal stok *Available*. Kuantitas yang belum ada dialihkan menjadi *Backorder* (SR-09). Validasi Delivery untuk barang yang belum ada dilarang mutlak. | **Sales Manager dirugikan** (tidak bisa menjanjikan pemenuhan instan 100% untuk barang kosong).<br><br>**Mitigasi:** Backorder otomatis memicu alur pengadaan (*Reordering / RFQ* ke vendor) dengan estimasi *lead time* yang transparan. | **T-11:** Reservasi SO 4 unit memotong Available dari 12 ke 8.<br><br>**T-12:** Percobaan reservasi 13 unit (stok Available hanya 12) dicegah/ditolak.<br><br>**T-13:** Kirim 2 unit, otomatis membuat Backorder 2 unit. |
| **SR-05 vs SR-06**<br>Validasi kuantitas pada tagihan pemasok (*Vendor Bill*). | **Opsi A:** Vendor Bill dicatat langsung untuk 5 unit sesuai PO sebelum penerimaan (Taufik Rahman - Purchasing Lead).<br><br>**Opsi B:** Vendor Bill dibatasi hanya pada *Accepted Quantity* yang lolos QC; unit rejected tidak boleh ditagih (Nanda Kusuma - Kepala Finance). | **Memilih Opsi B (Three-Way Matching)**<br>(Fitur: `F-14`, Layar: `S-12`).<br>Utang usaha (AP) dan kenaikan valuasi persediaan hanya diakui atas barang yang lolos inspeksi. Unit rejected masuk area karantina non-valued. | **Purchasing Lead & Vendor dirugikan** (pencairan dana tertunda menunggu berita acara penerimaan/inspeksi).<br><br>**Mitigasi:** Dokumen *Receipt* (`WH/IN/...`) mencatat detail *accepted* vs *rejected* secara otomatis sebagai dasar sah revisi tagihan dari vendor. | **T-21:** Penerimaan 5 unit: 4 accepted, 1 rejected masuk karantina.<br><br>**T-23:** Bill diposting hanya untuk 4 unit (Rp280.000 + PPN).<br><br>**T-24:** Sistem memblokir tagihan 5 unit dari vendor. |
| **SR-07**<br>Batas wewenang diskon promosi Sales. | **Opsi A:** Memberikan kebebasan diskon tanpa batas sistem untuk mengejar target omzet.<br><br>**Opsi B:** Diskon baris maksimal 15% oleh Sales; lebih dari itu ditolak; harga/diskon terkunci setelah SO *Confirmed*. | **Memilih Opsi B**<br>(Fitur: `F-07`, Layar: `S-05`).<br>Melindungi margin kontribusi kotor (*Gross Contribution*) perusahaan dan mencegah manipulasi harga pasca-kesepakatan. | **Tim Sales dirugikan** (kehilangan fleksibilitas diskon ekstrem di lapangan).<br><br>**Mitigasi:** Diskon khusus di atas 15% harus diajukan melalui kebijakan kampanye promo terpusat yang disetujui Direktur. | **T-09:** Konfirmasi SO dengan diskon 0% berhasil.<br><br>**T-10:** Percobaan konfirmasi SO dengan diskon 20% langsung ditolak sistem. |
| **SR-08**<br>Pengendalian risiko kredit macet pelanggan (*Credit Exposure*). | **Opsi A:** Memperbolehkan pesanan baru bagi pelanggan loyal tanpa menghitung piutang lama.<br><br>**Opsi B:** Menghitung *Credit Exposure* kumulatif: Saldo Piutang Berjalan (*Unpaid AR*) + Nilai Bruto Order Baru; tolak jika melebihi *Credit Limit*. | **Memilih Opsi B**<br>(Fitur: `F-03`, `F-11`, Layar: `S-02`).<br>Mencegah terjadinya kredit macet (*bad debts*). Sistem memastikan tidak ada perhitungan komitmen ganda (*no double counting*). | **Pelanggan tertunggak & Sales dirugikan** (pesanan pelanggan C002 terblokir).<br><br>**Mitigasi:** Sales menyarankan pelanggan mencicil pembayaran piutang lama melalui fasilitas *Partial Payment* (SR-15) untuk memulihkan plafon kredit. | **T-03:** C001 limit Rp2.000.000, AR Rp0 $\rightarrow$ lolos.<br><br>**T-04:** C002 limit Rp500.000, AR Rp222.000, order Rp499.500 (total exposure Rp721.500 $>$ limit) $\rightarrow$ sistem menolak konfirmasi. |

---

## 3. Log Keputusan Aturan Operasional & Teknis (SR-09 s.d. SR-23)

| ID Aturan | Stakeholder Terkait | Keputusan Kebijakan & Implementasi Sistem | Modul & Fitur | Bukti Pengujian |
| :--- | :--- | :--- | :--- | :--- |
| **SR-09** | Wahyu Saputra (Gudang) | **Partial Delivery & Backorder:** Kuantitas *Done* dicatat terpisah dari *Demand*. Kuantitas yang belum terkirim otomatis membentuk dokumen *Backorder*. Jumlah terkirim dilarang melebihi kuantitas reservasi. | Inventory (`F-09`, `S-07`) | `T-13`, `T-14` |
| **SR-10** | Ratih Widya (Direktur) | **Retur Kosmetik & Pembalikan Jurnal:** Retur dibatasi maksimal sebesar kuantitas terkirim yang belum pernah diretur. Barang tersegel lolos inspeksi kembali ke stok jual, COGS dibalik, dan *Credit Note* mengikuti harga, diskon, dan PPN faktur asal. | Sales & Accounting (`F-18`, `S-15`) | `T-29`, `T-30` |
| **SR-11** | Nanda Kusuma (Finance) | **Idempotensi Transaksi:** Aksi *Confirm, Validate, Post, Complete, Register Payment* wajib bersifat idempoten. Klik ganda atau pengulangan perintah identik tidak boleh menciptakan mutasi stok, jurnal, atau tagihan tambahan. | Lintas Modul (`F-09`, `F-15`, `F-20`, `F-22`) | `T-14`, `T-26`, `T-34` |
| **SR-12** | Ratih Widya (Direktur) | **Audit Trail & Penomoran Dokumen:** Seluruh dokumen memiliki nomor urut unik formal (`S00007`, `PO00007`, `WH/OUT/...`, `INV/...`). Log audit mencatat aktor, waktu, jenis aksi, dan ID dokumen referensi. | Lintas Modul (`F-01`, `F-05`, `F-06`, `F-22`) | `T-01`, `T-07`, `T-08`, `T-20` |
| **SR-13** | Wahyu Saputra (Gudang) | **Struktur Varian SKU Unik:** Setiap varian ukuran dan warna produk memiliki kode SKU unik (misal `BC-LIP-001` Rose vs `BC-LIP-002` Coral). Transaksi persediaan dicatat per SKU, bukan per nama induk produk. | Inventory (`F-04`, `S-03`) | `T-05`, `T-06` |
| **SR-14** | Wahyu Saputra (Gudang) | **Konservasi Nilai Mutasi Antarlokasi:** Transfer internal mengurangi stok lokasi asal dan menambah stok lokasi tujuan dengan kuantitas persis sama. Total valuasi persediaan perusahaan tidak berubah. | Inventory (`F-17`, `S-14`) | `T-27`, `T-28` |
| **SR-15** | Nanda Kusuma (Finance) | **Pembayaran Parsial & Validasi Overpayment:** Pembayaran cicilan (*Partial Payment*) mengurangi saldo terbuka (*Open Amount*). Pembayaran yang melebihi saldo terbuka ditolak oleh sistem. | Accounting (`F-16`, `S-09`) | `T-17`, `T-18` |
| **SR-16** | Nanda Kusuma (Finance) | **Standar Perhitungan PPN & Keseimbangan Jurnal:** Tarif PPN nonmewah efektif dihitung $11\%$ ($12\% \times \frac{11}{12}$) terhadap nilai sebelum pajak. Pembulatan dilakukan per baris ke rupiah bulat terdekat. Total Debit = Total Kredit. | Accounting (`F-15`, `F-23`, `S-08`, `S-12`) | `T-15`, `T-23`, `T-25`, `T-33` |
| **SR-17** | Laras Kirana (Studio) | **Validasi Kompetensi Terapis:** Treatment *Glow Facial* (`SV-FAC-01`) hanya dapat ditugaskan kepada karyawan berstatus Aktif yang memiliki skill *Facial*. Penugasan ke terapis tanpa skill terkait ditolak sistem. | Employees (`F-19`, `S-16`) | `T-31`, `T-32` |
| **SR-18** | Laras Kirana (Studio) | **Konsumsi Otomatis Bahan Treatment:** Setiap *Glow Facial* yang berstatus *Complete* otomatis mengonsumsi 1 sachet masker `BC-MSK-001` dari lokasi Bandung (`ST-BDG`), memicu jurnal COGS, dan menerbitkan tagihan sekali. | Employees & Invoicing (`F-20`, `S-17`) | `T-33`, `T-34` |
| **SR-19** | Nanda Kusuma (Finance) | **Pemisahan Wewenang (RBAC):** Minimal 4 role utama: *Sales, Warehouse, Finance, Studio*. Hak *Posting* jurnal hanya dimiliki Finance. Modifikasi stok fisik hanya bisa dilakukan oleh petugas lokasi terkait. | System Security (`F-01`, `F-02`, `S-01`) | `T-01`, `T-02`, `T-36` |
| **SR-20** | Ratih Widya (Direktur) | **Dashboard Eksekutif & Drill-Down:** Menampilkan metrik utama skenario: *Revenue (ex tax), COGS, Gross Contribution, Unpaid AR,* dan *Stock Value*. Setiap angka dapat diklik untuk membuka dokumen transaksi sumber. | Reporting (`F-21`, `S-18`) | `T-35`, `T-36` |
| **SR-21** | Taufik Rahman (Purchasing) | **Integritas Purchase Order Confirmed:** Setelah status *Confirmed*, vendor, SKU, dan harga terkunci. Pembatalan atau revisi memerlukan jejak audit dan otorisasi khusus. | Purchase (`F-12`, `F-22`, `S-10`) | `T-19`, `T-20` |
| **SR-22** | Laras Kirana (Studio) | **Batasan Lingkup Payroll:** Data karyawan mencakup identitas, departemen, status, dan skill. Perhitungan komisi atau utang gaji dinyatakan di luar lingkup (*out of scope*) sistem ini. | Employees (`F-19`, `S-16`) | `T-31`, `T-32` |
| **SR-23** | Nanda Kusuma (Finance) | **Penyelesaian Pembayaran Pemasok:** Pembayaran vendor hanya dapat diproses untuk tagihan yang berstatus *Posted*, dengan nilai maksimal sebesar saldo *Open AP*. Jurnal Dr AP / Cr Bank dibukukan seimbang tanpa duplikasi. | Accounting (`F-25`, `S-19`) | `T-37`, `T-38` |

---

## 4. Rangkuman Bukti Kunci untuk Sesi Tanya Jawab Dosen

1. **Kenapa Opsi Delivered Quantities dipilih dibanding Invoice on Confirmation?**  
   *Jawaban:* Mencegah sengketa hukum dan kekacauan akuntansi. Jika pelanggan membayar sebelum pengiriman tetapi gudang kekurangan stok, perusahaan mencatat kewajiban pengiriman yang tidak pasti dan melanggar prinsip realisasi pendapatan.
2. **Bagaimana sistem mencegah kebangkrutan akibat piutang macet?**  
   *Jawaban:* Melalui penerapan *Credit Exposure* (SR-08 / T-04). Sistem menolak pesanan baru secara otomatis jika saldo piutang tertunggak ditambah nilai pesanan baru melampaui limit kredit pelanggan.
3. **Bagaimana integritas data terjaga dari ketidaksengajaan pengguna?**  
   *Jawaban:* Melalui prinsip *Idempotency* (SR-11) dan *Segregation of Duties* (SR-19). User gudang tidak memiliki akses ke buku besar, dan klik berulang pada tombol transaksi tidak akan menduplikasi jurnal finansial.
