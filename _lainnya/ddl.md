users:
-id_users(int,autoincrement,primary_key)
-nama(varchar,64)
-email(varchar,64)
-username(varchar,32)
-password(varchar,254)
-create_at(datetime)
-update_at(datetime)
-foto(varchar,254)
-status(enum(Y,N))
-no_telepon(varchar,20)

m_karyawan(master_data):
-id_karyawan(int,autoincrement,primary_key)
-nama(varchar,64)
-jenis_kelamin(enum(L,P))
-id_divisi(TINYINT,unsigned)
-id_posisi(TINYINT,unsigned)
-no_telepon(varchar,20)
-email(varchar,64)
-alamat(text,nullable)
-nomor_karyawan(varchar,16)
-agama(varchar,16)
-tempat_lahir(varchar,64)
-tanggal_lahir(date)
-pendidikan(varchar,8)
-status_karyawan(enum(tetap, kontrak, magang))
-foto(varchar,254)
-NIK(varchar,20)
-tanggal_masuk(date)
-tangal_keluar(date)
-no_rekening(varchar,20)
-nama_bank(varchar,16)
-create_at(datetime)
-update_at(datetime)

m_divisi
-id_divisi(TINYINT,autoincrement,primary_key)
-nama_divisi(varchar,64)
-status(enum(Y,N))
-deskripsi(text)
-inisial(varchar,3)
-jumlah_karyawan(int,9)
-create_at(datetime)
-update_at(datetime)

m_jabatan
-id_jabatan(TINYINT,autoincrement,primary_key)
-nama_jabatan(varchar,64)
-status(enum(Y,N))
-create_at(datetime)
-update_at(datetime)

m_gaji_pokok
-id_gaji_pokok(int,autoincrement,primary_key)
-id_karyawan(int,foreign_key)
-amount(int,11)
-create_at(datetime)
-update_at(datetime)

trx_salary
-id_salary(int,autoincrement,primary_key)
-id_karyawan(int,foreign_key)
-tunjangan_konsumsi(int,11)
-tunjangan_harian(int,11)
-gaji_pokok(amount)(int,11)
-tahun(varchar,6)
-bulan(varchar,4)
-nomor_slip(varchar,64)
-received_at(datetime)
-bonus(int,11)
-PPH(int,11)
-asuransi(int,11)
-total_hasil(int,11)
-total_potongan(int,11)
-total_gaji(int,11)


http://localhost/latihan-1/latihan_MYSQL/table_karyawan.php








