<?php
function voyage_station_list(): array
{
    return [
        'A01: PSR-A',
        'S02: Permatang Damar Laut',
        'S03: Lapangan Terbang Antarabangsa Pulau Pinang',
        'S04: Sungai Tiram',
        'S05: FIZ South',
        'S06: FIZ North',
        'S07: Jalan Tengah',
        'S08: SPICE',
        'S09: Bukit Jambul',
        'S10: Sungai Nibong',
        'S11: Sungai Dua',
        'S12: Batu Uban',
        'S13: Jalan Universiti',
        'S14: Gelugor',
        'S15: Penang Waterfront',
        'S16: Jelutong East',
        'S17: Sungai Pinang',
        'S18: Bandar Sri Pinang',
        'S19: Macallum',
        'S20: Komtar',
        'S31: Penang Sentral'
    ];
}

function voyage_ticket_price_cents(string $origin, string $destination): ?int
{
    $stationIndexes = [];
    foreach (voyage_station_list() as $index => $station) {
        if (preg_match('/\A([A-Z]\d{2}):/i', $station, $matches) === 1) {
            $stationIndexes[strtoupper($matches[1])] = $index;
        }
    }

    $getStationCode = static function (string $station): ?string {
        if (preg_match('/\A\s*([A-Z]\d{2}):/i', $station, $matches) !== 1) {
            return null;
        }

        return strtoupper($matches[1]);
    };

    $originCode = $getStationCode($origin);
    $destinationCode = $getStationCode($destination);
    if (
        $originCode === null
        || $destinationCode === null
        || !isset($stationIndexes[$originCode], $stationIndexes[$destinationCode])
        || $originCode === $destinationCode
    ) {
        return null;
    }

    $stopsIncludingEndpoints = abs($stationIndexes[$originCode] - $stationIndexes[$destinationCode]) + 1;
    return $stopsIncludingEndpoints * 50;
}
