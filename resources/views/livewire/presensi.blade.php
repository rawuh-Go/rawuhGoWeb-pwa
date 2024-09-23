<div>
    <div class="container mx-auto max-w-sm">
        <div class="bg-white p-6 rounded-lg mt-3 shadow-lg">
            <div class="grid grid-cols-1 gap-6 mb-6">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Informasi Pegawai</h2>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <p><strong>Nama Pegawai : </strong>{{Auth::user()->name}}</p>
                        <p><strong>Kantor : </strong>{{$schedule->office->nama}}</p>
                        <p><strong>Shift : </strong>{{$schedule->shift->nama}} ({{$schedule->shift->waktu_datang}}) -
                            ({{$schedule->shift->waktu_pulang}}) wib</p>
                        @if ($schedule->is_wfa)
                            <p class="text-blue-700"><strong>Status : </strong>WFA</p>
                        @else
                            <p><strong>Status : </strong>WFO</p>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h4 class="text-l font-bold mb-2">Waktu Masuk</h4>
                            <p><strong>{{$attendance ? $attendance->waktu_datang : '-'}}</strong></p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h4 class="text-l font-bold mb-2">Waktu Pulang</h4>
                            <p><strong>{{$attendance ? $attendance->waktu_pulang : '-'}}</strong></p>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-2">Presensi</h2>
                    <div id="map" class="mb-4 rounded-lg border border-gray-300" wire:ignore></div>
                    <form class="row g-3" enctype="multipart/form-data" wire:submit="store">
                        <button type="button" onclick="tagLocation()"
                            class="px-4 py-2 bg-blue-400 text-white rounded">Tag
                            Location</button>
                        @if ($insideRadius)
                            <button class="px-4 py-2 bg-blue-900 text-white rounded" type="submit">Submit Presensi</button>
                        @endif
                    </form>


                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // DOM
        let map;
        let lat;
        let lng;
        let component;
        let marker;
        const office = [{{$schedule->office->latitude}}, {{$schedule->office->longitude}}];
        const radius = {{$schedule->office->radius}};

        document.addEventListener('livewire:initialized', function () {
            component = @this;
            map = L.map('map').setView([{{$schedule->office->latitude}}, {{$schedule->office->longitude}}], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            //tambahkan radius pada peta
            const circle = L.circle(office, {
                color: 'blue',
                fillcolor: '#f03',
                fillOpacity: 0.5,
                radius: radius,
            }).addTo(map);
        })



        // menangkap lokasi user
        function tagLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    lat = position.coords.latitude;
                    lng = position.coords.longitude;

                    if (marker) {
                        map.removeLayer(marker);
                    }

                    marker = L.marker([lat, lng]).addTo(map);
                    map.setView([lat, lng], 15);

                    // lakukan pengecekan
                    if (radiusDistance(lat, lng, office, radius)) {
                        component.set('insideRadius', true);
                        component.set('latitude', lat);
                        component.set('longitude', lng);
                    }
                })
            } else {
                alert('Tidak bisa mendapatkan lokasi');
            }
        }


        // hitung radius user
        function radiusDistance(lat, lng, center, radius) {
            const is_wfa = {{$schedule->is_wfa}}
            if (is_wfa) {
                alert('Anda WFA');
                return true;
            } else {
                let distance = map.distance([lat, lng], center);
                return distance <= radius;
            }
        }
    </script>
</div>