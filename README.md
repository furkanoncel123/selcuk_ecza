# Selçuk Ecza - Veri Analiz ve Yönetim Sistemi

## Proje Açıklaması
Bu proje, Selçuk Ecza Deposu için geliştirilmiş bir veri analizi ve yönetim sistemi backend servisidir. Eczanelerden gelen satış verilerini analiz etmek, raporlamak ve sisteme erişecek kullanıcıları yönetmek amacıyla tasarlanmıştır. Node.js ve Express.js tabanlı olup, MySQL veritabanı kullanmaktadır.

## Senaryo Tanımı

Selçuk Ecza Deposu, farklı şehirlerdeki (örneğin İzmir) eczanelere çeşitli kategorilerde ilaç dağıtımı yapmaktadır. İş zekası (BI) gereksinimleri doğrultusunda, yöneticilerin hangi ilacın hangi eczanede ne kadar satıldığını, mevsimsel satış trendlerini ve karlılık analizlerini görmeleri gerekmektedir. Ayrıca, bu verilere erişim güvenli bir şekilde kullanıcı adı ve şifre ile sağlanmalıdır.
Bu sistem:
- Eczane ve ilaç tanımlarını tutar.
- Geçmiş satış verilerini simüle eder ve saklar.
- Kullanıcıların güvenli bir şekilde kayıt olmasını ve giriş yapmasını sağlar.
- Dashboard uygulamaları için JSON formatında veri servisi sunar.

## Kurulum Adımları

1.  **Projeyi Klonlayın veya İndirin**
    ```bash
    git clone <proje-url>
    cd selcuk_ecza_son
    ```

2.  **Bağımlılıkları Yükleyin**
    ```bash
    npm install
    ```

3.  **Çevresel Değişkenleri Ayarlayın**
    `.env.example` dosyasının adını `.env` olarak değiştirin ve kendi veritabanı bilgilerinizi girin.
    ```bash
    cp .env.example .env
    ```
    Dosya içeriği örneği:
    ```ini
    DB_HOST=127.0.0.1
    DB_PORT=8889
    DB_DATABASE=selcuk_ecza
    DB_USERNAME=root
    DB_PASSWORD=root
    # MAMP kullanıyorsanız socket yolunu aktif edebilirsiniz
    # DB_SOCKET=/Applications/MAMP/tmp/mysql/mysql.sock
    ```

4.  **Veritabanını Hazırlayın**
    Veritabanı tablolarını oluşturmak ve örnek verileri (seed) yüklemek için aşağıdaki komutu çalıştırın.
    > ⚠️ **Uyarı:** Bu işlem mevcut `selcuk_ecza` veritabanını sıfırlar.
    ```bash
    php seeder.php
    ```

5.  **Sunucuyu Başlatın**
    ```bash
    npm start
    ```
    veya geliştirme modunda:
    ```bash
    npm run dev
    ```

Sunucu varsayılan olarak `http://localhost:3001` adresinde çalışacaktır.

## API Endpoint Listesi

| Metot | Endpoint         | Açıklama                                      | İstek Gövdesi (Body) / Parametreler |
| :---  | :---             | :---                                          | :--- |
| POST  | `/api/login`     | Kullanıcı girişi yapar.                       | `{ "kullanici_adi": "...", "sifre": "..." }` |
| POST  | `/api/register`  | Yeni kullanıcı kaydı oluşturur.               | `{ "adi": "...", "soyadi": "...", "hesap_no": "...", "kullanici_adi": "...", "sifre": "..." }` |
| GET   | `/api/users`     | Sistemdeki kayıtlı kullanıcıları listeler.    | - |
| GET   | `/api/sales`     | Satış verilerini (Eczane ve İlaç detaylı) getirir.| - |


