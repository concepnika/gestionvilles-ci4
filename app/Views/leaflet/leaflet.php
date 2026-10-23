<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Add a patterned line to a map</title>
<meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
<link href="https://api.mapbox.com/mapbox-gl-js/v3.14.0/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v3.14.0/mapbox-gl.js"></script>
<style>
body { margin: 0; padding: 0; }
#map { position: absolute; top: 0; bottom: 0; width: 100%; }
</style>
</head>
<body>
<div id="map"></div>
<script>
    mapboxgl.accessToken = '<?= env('mapbox.accessToken', '') ?>';
    const map = (window.map = new mapboxgl.Map({
        container: 'map',
        center: [13.38879, 52.5191],
        zoom: 16.9,
        pitch: 30,
        bearing: 50,
        style: 'mapbox://styles/mapbox/standard',
        minZoom: 15,
        maxZoom: 22
    }));

    map.on('style.load', () => {
        map.loadImage(
            'https://docs.mapbox.com/mapbox-gl-js/assets/pattern-dot.png',
            (error, image) => {
                if (error) throw error;

                map.addImage('pattern-dot', image);

                map.addSource('route-data', {
                    type: 'geojson',
                    lineMetrics: true,
                    data: {
                        'type': 'Feature',
                        'properties': {},
                        'geometry': {
                            'coordinates': [
                                [13.390297, 52.518188],
                                [13.39042, 52.518201],
                                [13.390322, 52.518754],
                                [13.388698, 52.518654],
                                [13.388707, 52.518714],
                                [13.3887, 52.518762],
                                [13.388707, 52.518824],
                                [13.388574, 52.519737],
                                [13.388518, 52.519735],
                                [13.388459, 52.519845],
                                [13.388546, 52.519848],
                                [13.388638, 52.51987],
                                [13.389079, 52.519895],
                                [13.38907, 52.519935]
                            ],
                            'type': 'LineString'
                        }
                    }
                });

                const lineBaseWidth = 14;

                map.addLayer({
                    id: 'route-line',
                    type: 'line',
                    source: 'route-data',
                    slot: 'middle',
                    layout: {
                        'line-join': 'none'
                    },
                    paint: {
                        'line-pattern': 'pattern-dot',
                        'line-width': [
                            'interpolate',
                            ['exponential', 2],
                            ['zoom'],
                            0,
                            lineBaseWidth * 1,
                            0.9999,
                            lineBaseWidth * 2,
                            1,
                            lineBaseWidth * 1,
                            1.9999,
                            lineBaseWidth * 2,
                            2,
                            lineBaseWidth * 1,
                            2.9999,
                            lineBaseWidth * 2,
                            3,
                            lineBaseWidth * 1,
                            3.9999,
                            lineBaseWidth * 2,
                            4,
                            lineBaseWidth * 1,
                            4.9999,
                            lineBaseWidth * 2,
                            5,
                            lineBaseWidth * 1,
                            5.9999,
                            lineBaseWidth * 2,
                            6,
                            lineBaseWidth * 1,
                            6.9999,
                            lineBaseWidth * 2,
                            7,
                            lineBaseWidth * 1,
                            7.9999,
                            lineBaseWidth * 2,
                            8,
                            lineBaseWidth * 1,
                            8.9999,
                            lineBaseWidth * 2,
                            9,
                            lineBaseWidth * 1,
                            9.9999,
                            lineBaseWidth * 2,
                            10,
                            lineBaseWidth * 1,
                            10.9999,
                            lineBaseWidth * 2,
                            11,
                            lineBaseWidth * 1,
                            11.9999,
                            lineBaseWidth * 2,
                            12,
                            lineBaseWidth * 1,
                            12.9999,
                            lineBaseWidth * 2,
                            13,
                            lineBaseWidth * 1,
                            13.9999,
                            lineBaseWidth * 2,
                            14,
                            lineBaseWidth * 1,
                            14.9999,
                            lineBaseWidth * 2,
                            15,
                            lineBaseWidth * 1,
                            15.9999,
                            lineBaseWidth * 2,
                            16,
                            lineBaseWidth * 1,
                            16.9999,
                            lineBaseWidth * 2,
                            17,
                            lineBaseWidth * 1,
                            17.9999,
                            lineBaseWidth * 2,
                            18,
                            lineBaseWidth * 1,
                            18.9999,
                            lineBaseWidth * 2,
                            19,
                            lineBaseWidth * 1,
                            19.9999,
                            lineBaseWidth * 2,
                            20,
                            lineBaseWidth * 1,
                            20.9999,
                            lineBaseWidth * 2,
                            21,
                            lineBaseWidth * 1,
                            22,
                            lineBaseWidth * 2
                        ]
                    }
                });
            }
        );
    });
</script>

</body>
</html>