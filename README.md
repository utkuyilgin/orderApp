## TürkTicaretNet sipariş task'i.

- Merhaba, bu proje Utku Yılgın tarafından hazırlanmış bir RestAPI projesidir. Bu proje TürkTicaretNet tarafından
belirtilen taskları içerir. Kuyruklama sistemi için RabbitMQ, önbellek sistemi için Redis kullanılmıştır.

## Projenin kurulumu:
- Proje git'ten çekildikten sonra .env dosyasının içinde DB ayarlarımızı yapıyoruz. Eğer kurulacak makinede Redis mevcutsa CACHE_DRIVER kısmını redis olarak değiştiriyoruz.
- DB ayarları bittikten sonra php artisan migrate komutuyla tabloları database'e yazdırıyoruz. Ardından php artisan db:seed komutuyla çalıştırıyoruz. Projenin içinde Case_Products.json dosyasının olduğundan emin olalım çünkü seeder'lar bu JSON klasörünü parse edip datayı ilgili tablolara yazıyor.
- php artisan serve ile projeyi ayağa kaldırıyoruz.
- Proje çalışmaya hazır.
- Projeyi ayağa kaldırdığınız domain adresinin sağ sonuna /api/documentation yazdığınız takdirde Swagger dökümantasyonu açılacaktır. Fakat her halükarda test yaptığım Postman JSON dosyasını mail olarak göndereceğim.
- api/login kısmına request body olarak utku.387@hotmail.com, 123456 bilgileriyle login olabilir, veya api/register kısmından kayıt olabilirsiniz. Login veya register işlemi sonrası dönen token değerinin | karakterinin sağ taraftan sonrasını request headerlerine Bearer Token olarak ekliyoruz, artık api/documentation kısmındaki endpointlere erişebiliriz.
- Proje klasörlerinde yapılan işlemler tek tek yorum satırlarıyla yazılmıştır, algoritmayı inceleyebilirsiniz.

## Ufak Bilgilendirmeler
- Proje seed edildikten sonra yazar ve kitap kategorisi içeren bir adet seeder'im bulunmakta, task'ta belirttiğiniz gibi Sabahattin Ali'nin herhangi bir kitabından 2 tane alındığı takdirde 3.sü hediye ediliyor.
Yeni bir yazar ve kategoriyi ilgili kampanyaya set etmek istediğiniz takdirde api/documentation kısmında nasıl yapıldığını anlattım.

Redis hatası alınırsa, .env içine REDIS_CLIENT=predis ekleyip deneyebilirsiniz.

Teşekkür eder, iyi çalışmalar dilerim.

- Utku Yılgın
