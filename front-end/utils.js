export function haversineDistance(coord1, coord2) {
    const toRadians = (angle) => angle * (Math.PI / 180);
    const radius = 6371; // Raggio della Terra in chilometri


    const lat1 = coord1.latitude || coord1.lat;
    const lon1 = coord1.longitude || coord1.lon || coord1.lng;
    const lat2 = coord2.latitude || coord2.lat;
    const lon2 = coord2.longitude || coord2.lon || coord2.lng;

    const dLat = toRadians(lat2 - lat1);
    const dLon = toRadians(lon2 - lon1);

    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    const distance = radius * c;

    return distance;
}