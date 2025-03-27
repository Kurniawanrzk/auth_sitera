Berikut adalah dokumentasi API dalam format Markdown:


---

### 1. Cek Profil BSU
**Method:** `GET`  
**Endpoint:** `/profile`  
**Headers:** `Authorization: Bearer {token}`  
**Query Parameter:**
- `bsu_id` (UUID) - Required

**Response:**
```json
{
  "status": true,
  "data": {
    "bsu": {
      "id": "bsu-123",
      "nama_bsu": "Bank Sampah Sejahtera",
      "alamat": "Jl. Merdeka No. 10"
    }
  }
}
```

```
# API Documentation for BSU (Bank Sampah Unit)

**Authorization:** Bearer Token required for all endpoints.

---

## Endpoints

### 2. Edit Profil BSU
**Method:** `PUT`  
**Endpoint:** `/edit/profile`  
**Headers:**
- `Authorization: Bearer {token}`
- `Content-Type: multipart/form-data`

**Request Body (Form-Data):**
| Field             | Type     | Description                                      |
|-------------------|----------|--------------------------------------------------|
| `bsu_id`          | UUID     | ID BSU (Required)                                |
| `email`           | String   | (Optional) Email baru                            |
| `password`        | String   | (Optional) Password baru                         |
| `gambar_bsu`      | File     | (Optional) Gambar profil (max 2MB, JPG/PNG)      |
| `nama_bsu`        | String   | (Optional) Nama BSU                              |
| `kategori`        | String   | (Optional) Kategori BSU                          |
| `alamat`          | String   | (Optional) Alamat lengkap                        |
| `jalan_dusun`     | String   | (Optional) Nama jalan/dusun                      |
| `rt`              | String   | (Optional) Nomor RT                              |
| `rw`              | String   | (Optional) Nomor RW                              |
| `desa`            | String   | (Optional) Desa                                  |
| `kecamatan`       | String   | (Optional) Kecamatan                             |
| `longitude`       | Float    | (Optional) Koordinat longitude                   |
| `latitude`        | Float    | (Optional) Koordinat latitude                    |
| `tanggal_berdiri` | Date     | (Optional) Tanggal berdiri BSU                   |
| `nama_pengurus`   | String   | (Optional) Nama pengurus                         |
| `jumlah_nasabah`  | Integer  | (Optional) Jumlah nasabah                        |
| `nomor_telepon`   | String   | (Optional) Nomor telepon                         |
| `reward_level`    | String   | (Optional) Level reward BSU                      |
| `total_sampah`    | Float    | (Optional) Total sampah terkelola                |

**Response (Success):**
```json
{
  "status": true,
  "message": "profil berhasil di edit",
  "data": {
    "user_bsu": {
      "id": "bsu-123",
      "nama_bsu": "Bank Sampah Sejahtera",
      "email": "bsu@example.com",
      "gambar_bsu": "1678901234_bsu123.jpg"
    }
  }
}
```

**Response (Error):**
```json
{
  "status": false,
  "message": "Gagal untuk mengupdate profil",
  "error": "Error details"
}
```

---

### 3. Tambah Sampah BSU
**Method:** `POST`  
**Endpoint:** `/tambah-sampah`  
**Headers:**
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

**Request Body:**
```json
{
  "tipe": "plastik",
  "nama": "Botol PET",
  "harga_satuan": 5000
}
```

**Response:**
```json
{
  "status": true,
  "message": "sampah berhasil ditambahkan"
}
```

---

### 4. Transaksi Sampah Nasabah
**Method:** `POST`  
**Endpoint:** `/transaksi-sampah`  
**Headers:**
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

**Request Body:**
```json
{
  "nik_nasabah": "3273012345678901",
  "sampah": [
    {
      "id": "sampah-123",
      "berat": 2.5
    }
  ]
}
```

**Response (Success):**
```json
{
  "message": "Transaksi berhasil disimpan",
  "transaksi_id": "transaksi-456"
}
```

---

### 5. Cek Semua Transaksi
**Method:** `GET`  
**Endpoint:** `/cek-semua-transaksi-bsu`  
**Headers:** `Authorization: Bearer {token}`  
**Query Parameter:**
- `user_id` (UUID) - Required

**Response:**
```json
{
  "status": true,
  "data": [
    {
      "id": "transaksi-456",
      "total_harga": 12500,
      "detailTransaksi": [
        {
          "berat": 2.5,
          "sampah": {
            "nama": "Botol PET",
            "harga_satuan": 5000
          }
        }
      ]
    }
  ]
}
```

---

### 6. Rekapitulasi Sampah
**Method:** `GET`  
**Endpoint:** `/cek-rekapitulasi-bsu`  
**Headers:** `Authorization: Bearer {token}`  
**Query Parameters:**
- `start_date` (Date) - Optional (default: awal minggu ini)
- `end_date` (Date) - Optional (default: akhir minggu ini)

**Response:**
```json
{
  "rekapitulasi": [
    {
      "tipe": "plastik",
      "total_berat": 150
    }
  ],
  "chart_data": {
    "labels": ["Plastik"],
    "datasets": {
      "data": [150],
      "backgroundColor": ["#1F77B4"]
    }
  }
}
```

---

### 7. Kelola Penarikan Nasabah

#### 7.1 Cek Ajuan Penarikan
**Method:** `GET`  
**Endpoint:** `/cek-ajuan-bsu`  
**Headers:** `Authorization: Bearer {token}`  
**Query Parameter:**
- `user_id` (UUID) - Required

**Response:**
```json
{
  "status": true,
  "data": [
    {
      "id": "ajuan-789",
      "nik": "3273012345678901",
      "total_penarikan": 50000,
      "status": "pending"
    }
  ]
}
```

#### 7.2 Proses Ajuan Penarikan
**Method:** `POST`  
**Endpoint:** `/proses-ajuan-penarikan/{pengajuan_id}`  
**Headers:**
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

**Request Body:**
```json
{
  "status": "berhasil",
  "keterangan": "Penarikan berhasil diproses"
}
```

**Response:**
```json
{
  "status": true,
  "message": "pengajuan berhasil"
}
```

---

## Error Handling
Semua error akan mengembalikan response dengan format:
```json
{
  "status": false,
  "message": "Deskripsi error",
  "error": "Detail teknis error"
}
```

Dengan kode status HTTP:
- `400` Bad Request
- `401` Unauthorized
- `404` Not Found
- `500` Internal Server Error
```

Berikut dokumentasi API untuk Nasabah dalam format Markdown:

```markdown
# API Documentation for Nasabah

**Authorization:** Bearer Token required for all endpoints.

---

## Endpoints

### 1. Edit Profil Nasabah
**Method:** `PUT`  
**Endpoint:** `/edit-profil`  
**Headers:**
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

**Request Body (JSON):**
```json
{
  "nik": "3273012345678901",
  "nama": "John Doe",
  "alamat": "Jl. Sudirman No. 1",
  "nomor_wa": "081234567890",
  "nomor_rekening": "1234567890",
  "nama_pemilik_rekening": "John Doe",
  "jenis_rekening": "BCA"
}
```
*Note: Semua field bersifat optional*

**Response (Success):**
```json
{
  "status": true,
  "message": "Profil nasabah berhasil diperbarui",
  "data": {
    "id": "nasabah-123",
    "nik": "3273012345678901",
    "nama": "John Doe",
    "saldo": 150000
  }
}
```

**Response (Error):**
```json
{
  "status": false,
  "message": "Nasabah tidak ditemukan"
}
```

---

### 2. Cek Profil Nasabah
**Method:** `GET`  
**Endpoint:** `/cek-profil`  
**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "status": true,
  "data": {
    "user_nasabah": {
      "id": "nasabah-123",
      "nik": "3273012345678901",
      "nama": "John Doe",
      "saldo": 150000
    },
    "user": {
      "email": "john@example.com",
      "role": "nasabah"
    }
  }
}
```

---

### 3. Cek Transaksi Nasabah
**Method:** `GET`  
**Endpoint:** `/cek-transaksi`  
**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "status": true,
  "data": [
    {
      "id": "transaksi-456",
      "total_harga": 12500,
      "detail_transaksi": [
        {
          "sampah": {
            "nama": "Botol PET",
            "harga_satuan": 5000
          },
          "berat": 2.5
        }
      ]
    }
  ]
}
```

---

### 4. Ajuan Penarikan Saldo
**Method:** `POST`  
**Endpoint:** `/ajuan-penarikan`  
**Headers:**
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

**Request Body:**
```json
{
  "total_penarikan": 50000
}
```

**Response (Success):**
```json
{
  "status": true,
  "message": "Pengajuan penarikan berhasil dibuat",
  "data": {
    "id": "ajuan-789",
    "status": "pending"
  }
}
```

**Response (Saldo Tidak Cukup):**
```json
{
  "status": false,
  "message": "Total penarikan melebihi saldo"
}
```

---

### 5. Cek Status Penarikan
**Method:** `GET`  
**Endpoint:** `/cek-ajuan-penarikan`  
**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "status": true,
  "data": [
    {
      "id": "ajuan-789",
      "total_penarikan": 50000,
      "status": "berhasil",
      "waktu_pengajuan": "2023-10-05 14:30:00"
    }
  ]
}
```

---

## Error Handling
**Format Error Umum:**
```json
{
  "status": false,
  "message": "Deskripsi error",
  "error": "Detail teknis error (jika ada)"
}
```

**Kode Status HTTP:**
- `400` Bad Request
- `401` Unauthorized
- `404` Nasabah tidak ditemukan
- `500` Internal Server Error

**Catatan:**
1. Semua endpoint memerlukan header Authorization
2. Field `user_id` didapatkan secara otomatis dari token
3. Data transaksi diambil dari sistem Bank Sampah Unit (BSU)
```

Dokumentasi ini mencakup semua endpoint nasabah dengan detail request-response dan error handling. Format konsisten dengan dokumentasi BSU sebelumnya untuk memudahkan penggunaan.
