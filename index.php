<?php

require_once 'vendor/autoload.php';

$api = new \Yandex\Geo\Api();

$userAddress = $_GET["userAddress"];

// Или можно искать по точке
$api->setPoint(30.5166187, 50.4452705);

if (isset($userAddress)) {
  // Можно икать по адресу
  $api->setQuery($userAddress);

}

// Настройка фильтров
$api
  ->setLimit(0) // кол-во результатов
  ->setLang(\Yandex\Geo\Api::LANG_RU) // локаль ответа
  ->load();

$response = $api->getResponse();
$response->getFoundCount(); // кол-во найденных адресов
$response->getQuery(); // исходный запрос
$response->getLatitude(); // широта для исходного запроса
$response->getLongitude(); // долгота для исходного запроса

// Список найденных точек
$collection = $response->getList();
foreach ($collection as $item) {
  $item->getAddress(); // вернет адрес
  $item->getLatitude(); // широта
  $item->getLongitude(); // долгота
  $item->getData(); // необработанные данные
}

?>

<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <link href="https://fonts.googleapis.com/css?family=PT+Serif:400,700&amp;subset=cyrillic" rel="stylesheet">
  <link rel="stylesheet" href="./css/font-awesome-4.7.0/font-awesome-4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css ">
  <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
</head>
<body>
  <div class="wrapper">

        <!-- Форма ввода адреса -->
        <div class="address-form clearfix">
            <form action="">
                <input class="userAddress" name="userAddress" type="text" placeholder="Введите адрес...">
                <input name="submit" type="submit" value="Найти">
            </form>
        </div>


        <!-- Данные широты и долгототы по введенному адресу -->
      <?php if ($response->getFoundCount() <= 1) : ?>
          <div class="data">
            <?php foreach ($collection as $item) : ?>
              <?= "Вот данные для адреса - " . $item->getAddress() . ": <br>" . "<span class=\"lati\">Широта:</span> " . $item->getLatitude() . " <span class=\"long\">Долгота:</span> " . $item->getLongitude(); ?>
            <?php endforeach ?>
          </div>
      <? endif; ?>

    <div class="clearfix">
        <!-- Карта -->
        <div id="map"></div>

        <!-- Ссылки на точки -->
        <?php if ($response->getFoundCount() > 1) : ?>
        <div class="points">
            <h2>Выберите что-нибудь одно:</h2>
            <ul>
              <?php foreach ($collection as $item) : ?>
                <li><a href="?userAddress=<?= $item->getAddress() ?>"><?= $item->getAddress() ?></a></li>
              <?php endforeach ?>
            </ul>
        </div>
        <? endif; ?>
    </div>

  </div>

  <script type="text/javascript">
      ymaps.ready(init);
      var myMap,
          myPlacemark;

      function init(){
          myMap = new ymaps.Map("map", {
              center: [<?= $item->getLatitude(); ?>, <?= $item->getLongitude(); ?>],
              zoom: 10
          });

          myPlacemark = new ymaps.Placemark([<?= $item->getLatitude(); ?>, <?= $item->getLongitude(); ?>], {
              hintContent: '',
              balloonContent: ''
          });

          myMap.geoObjects.add(myPlacemark);
      }
  </script>
</body>
</html>
