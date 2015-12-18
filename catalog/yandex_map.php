<div id="map" style="width:100%; height:400px"></div>
<script language="javascript">
    
ymaps.ready(init);

var myMap,myPlacemark;

function init() {
    // Поиск координат центра.
    ymaps.geocode('Новосибирска область, город Новосибирск, улица <?=$tags['address']?>', {results: 1}).then(function (res) {
        // Выбираем первый результат геокодирования.
        var firstGeoObject = res.geoObjects.get(0),
            // Создаем карту с нужным центром.
            myMap = new ymaps.Map("map", {
                center: firstGeoObject.geometry.getCoordinates(),
                zoom: 17,
                behaviors: ['default', 'scrollZoom']
            });

        myBalloonContent = '<p><?=$tags['address']?></p>';

        myPlacemark = new ymaps.Placemark(firstGeoObject.geometry.getCoordinates(), { content: 'Здесь  адрес', balloonContent: myBalloonContent }) ;
        myMap.geoObjects.add(myPlacemark);

    }, function (err) {
        // Если геокодирование не удалось, сообщаем об ошибке.
        alert(err.message);
    });

}

</script>