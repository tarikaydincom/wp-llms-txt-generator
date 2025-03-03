# WP LLMs.txt Generator

WordPress siteler için LLMs.txt dosyası oluşturucu eklenti. Bu eklenti, sitenizde bulunan farklı içerik türlerini (yazılar, sayfalar, ürünler, medya) seçerek özelleştirilmiş bir LLMs.txt dosyası oluşturmanıza olanak tanır.

## Özellikler

- WordPress 6.0 ve üzeri sürümlerle uyumlu
- İçerik türlerini seçme özelliği (yazılar, sayfalar, ürünler, medya)
- Otomatik LLMs.txt dosyası oluşturma
- **Otomatik güncelleme**: Yeni içerik eklendiğinde veya mevcut içerik güncellendiğinde LLMs.txt otomatik olarak güncellenir
- Çoklu dil desteği
- WordPress 2025 kodlama standartlarına uygun
- Kolay kullanımlı yönetici arayüzü

## Kurulum

1. Eklenti dosyalarını `/wp-content/plugins/wp-llms-generator` dizinine yükleyin
2. WordPress yönetici panelinden eklentiyi etkinleştirin
3. WordPress ana dizininin yazma izinlerinin doğru ayarlandığından emin olun
4. Ayarlar > LLMs.txt Generator menüsünden eklenti ayarlarını yapılandırın

## Kullanım

1. WordPress yönetici panelinde "Ayarlar > LLMs.txt Generator" menüsüne gidin
2. LLMs.txt dosyasına dahil etmek istediğiniz içerik türlerini seçin
3. "Ayarları Kaydet" butonuna tıklayın
4. LLMs.txt dosyası otomatik olarak oluşturulacak ve güncellenecektir
5. İsterseniz "LLMs.txt Oluştur" butonuna tıklayarak manuel olarak da güncelleyebilirsiniz
6. Oluşturulan dosya WordPress ana dizininde (`/llms.txt`) saklanacaktır

## Gereksinimler

- WordPress 6.0 veya üzeri
- PHP 7.4 veya üzeri
- WordPress ana dizininde yazma izinleri

## Sık Sorulan Sorular

### LLMs.txt dosyası nerede oluşturulur?
Dosya, WordPress ana dizininde (`/llms.txt`) oluşturulur. Bu sayede web tarayıcıları tarafından doğrudan erişilebilir.

### İçerik türlerini daha sonra değiştirebilir miyim?
Evet, istediğiniz zaman ayarlar sayfasından içerik türlerini değiştirebilir ve yeni bir LLMs.txt dosyası oluşturabilirsiniz.

### LLMs.txt dosyası otomatik olarak güncellenir mi?
Evet, yeni içerik eklediğinizde, mevcut içeriği güncellediğinizde veya sildiğinizde LLMs.txt dosyası otomatik olarak güncellenir.

### WordPress ana dizinine yazma izni alamıyorum, ne yapmalıyım?
Bu durumda sunucu yöneticinizle iletişime geçerek WordPress ana dizinine yazma izni talep etmelisiniz. Alternatif olarak, FTP üzerinden WordPress ana dizininin izinlerini 755 olarak ayarlayabilirsiniz.

## Lisans

GPL v2 veya daha yeni

## Değişiklik Günlüğü

### 1.0.0
- İlk sürüm
- Otomatik güncelleme özelliği eklendi
- LLMs.txt dosya konumu WordPress ana dizinine taşındı 